<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Restores the beneficiaries of agent #17 "Basontopur (Himel)" that are missing
 * on the live database, from database/seeders/data/himel_beneficiaries.json
 * (exported from the older local database).
 *
 * - A beneficiary whose ID is missing is inserted again with the same ID, so the
 *   old sales and visits that point to that ID are linked again.
 * - A beneficiary that still exists but now belongs to someone else is only
 *   moved back to Himel if you confirm it separately.
 * - A beneficiary already under Himel, or an ID now used by a different person,
 *   is left alone.
 *
 * Nothing changes before the plan is shown and confirmed; a JSON backup of the
 * rows being moved is written to storage/app/data-restore/; everything runs in one
 * transaction; running it again is a no-op.
 *
 * Run: php artisan db:seed --class=RestoreHimelBeneficiariesSeeder
 */
class RestoreHimelBeneficiariesSeeder extends Seeder
{
    private const AGENT_ID = 17;
    private const DATA_FILE = 'seeders/data/himel_beneficiaries.json';

    public function run()
    {
        $path = database_path(self::DATA_FILE);
        if (!is_file($path)) {
            $this->command->error("Data file not found: {$path}. NOTHING was changed.");
            return;
        }

        $data = json_decode(file_get_contents($path), true);
        $rows = collect($data['beneficiaries'] ?? [])->map(fn ($row) => (array) $row)->keyBy('id');
        if ($rows->isEmpty()) {
            $this->command->error('The data file has no beneficiaries. NOTHING was changed.');
            return;
        }
        $this->command->info("Restore beneficiaries of agent #" . self::AGENT_ID . " ({$data['agent']}) from " . basename($path) . ": {$rows->count()} rows.");

        // Users referenced by the rows must exist, otherwise the inserts would fail.
        $referenced = $rows->pluck('agent_id')->merge($rows->pluck('created_by'))->merge($rows->pluck('updated_by'))->filter()->unique();
        $missingUsers = $referenced->diff(DB::table('users')->whereIn('id', $referenced)->pluck('id'));
        if ($missingUsers->isNotEmpty()) {
            $this->command->error('These users do not exist in this database: #' . $missingUsers->implode(', #') . '. NOTHING was changed.');
            return;
        }

        // Only columns this database has are written (live and local may differ slightly).
        $columns = collect(Schema::getColumnListing('app_customers'));

        $current = DB::table('app_customers')->whereIn('id', $rows->keys())->get(['id', 'agent_id', 'name', 'mobile'])->keyBy('id');
        $missing = $rows->keys()->diff($current->keys())->values();
        $alreadyHimel = $current->where('agent_id', self::AGENT_ID)->keys();
        $elsewhere = $current->where('agent_id', '!=', self::AGENT_ID)
            ->filter(fn ($row) => $this->samePerson($row, $rows[$row->id]))->keys();
        $idTaken = $current->where('agent_id', '!=', self::AGENT_ID)
            ->reject(fn ($row) => $this->samePerson($row, $rows[$row->id]))->keys();

        $this->printPlan($rows, $current, $missing, $alreadyHimel, $elsewhere, $idTaken);

        if ($missing->isEmpty() && $elsewhere->isEmpty()) {
            $this->command->info('Nothing to restore. NOTHING was changed.');
            return;
        }

        $insert = $missing->isNotEmpty()
            && $this->command->confirm("Insert the {$missing->count()} missing beneficiaries with their original IDs?", false);
        $moveBack = $elsewhere->isNotEmpty()
            && $this->command->confirm("Move the {$elsewhere->count()} beneficiaries that are now under someone else back to Himel?", false);

        if (!$insert && !$moveBack) {
            $this->command->warn('Cancelled. Nothing was changed.');
            return;
        }

        if ($moveBack) {
            $backup = 'data-restore/RestoreHimelBeneficiaries-backup-' . now()->format('Ymd-His') . '.json';
            Storage::disk('local')->put($backup, json_encode(DB::table('app_customers')->whereIn('id', $elsewhere)->get(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->command->info("Backup of the rows being moved: storage/app/{$backup}");
        }

        $himelBefore = DB::table('app_customers')->where('agent_id', self::AGENT_ID)->count();

        DB::transaction(function () use ($rows, $columns, $missing, $elsewhere, $insert, $moveBack, $himelBefore) {
            $inserted = 0;
            if ($insert) {
                // Re-check inside the transaction so a row added meanwhile is not inserted twice.
                $stillMissing = $missing->diff(DB::table('app_customers')->whereIn('id', $missing)->lockForUpdate()->pluck('id'));
                foreach ($stillMissing->chunk(200) as $chunk) {
                    $batch = $chunk->map(fn ($id) => collect($rows[$id])->only($columns)->all())->values()->all();
                    DB::table('app_customers')->insert($batch);
                    $inserted += count($batch);
                }
            }

            $moved = $moveBack
                ? DB::table('app_customers')->whereIn('id', $elsewhere)->update(['agent_id' => self::AGENT_ID, 'updated_at' => now()])
                : 0;

            $himelAfter = DB::table('app_customers')->where('agent_id', self::AGENT_ID)->count();
            if ($himelAfter !== $himelBefore + $inserted + $moved) {
                throw new RuntimeException("Result check failed (Himel before {$himelBefore}, inserted {$inserted}, moved {$moved}, after {$himelAfter}). All changes were rolled back.");
            }

            $this->command->info("Inserted {$inserted}, moved back {$moved}. Himel now has {$himelAfter} beneficiaries (was {$himelBefore}).");
        });

        $restored = $rows->keys();
        $this->command->table(['Linked data for these beneficiaries in this database', 'Rows'], [
            ['Old agent sales (agent_sales)', DB::table('agent_sales')->whereIn('app_customer_id', $restored)->count()],
            ['Visits (visit_infos)', DB::table('visit_infos')->whereIn('app_customer_id', $restored)->count()],
            ['Farms', DB::table('farms')->whereIn('app_customer_id', $restored)->count()],
        ]);
        $this->command->info('Done.');
    }

    /** Same person when the name or the mobile matches (either may have been corrected later). */
    private function samePerson(object $current, array $original): bool
    {
        $name = fn ($value) => strtolower(preg_replace('/\s+/', ' ', trim((string) $value)));
        $mobile = fn ($value) => substr(preg_replace('/\D/', '', (string) $value), -10);

        return ($name($current->name) !== '' && $name($current->name) === $name($original['name'] ?? ''))
            || ($mobile($current->mobile) !== '' && $mobile($current->mobile) === $mobile($original['mobile'] ?? ''));
    }

    private function printPlan(Collection $rows, Collection $current, Collection $missing, Collection $alreadyHimel, Collection $elsewhere, Collection $idTaken): void
    {
        $this->command->table(['Status', 'Beneficiaries', 'Action'], [
            ['Missing in this database', $missing->count(), 'insert with the same ID (asks first)'],
            ['Now under someone else', $elsewhere->count(), 'move back to Himel (asks first)'],
            ['Already under Himel', $alreadyHimel->count(), 'nothing'],
            ['ID now used by a different person', $idTaken->count(), 'nothing (listed below)'],
        ]);

        if ($elsewhere->isNotEmpty()) {
            $owners = DB::table('app_customers as c')->leftJoin('users as u', 'u.id', '=', 'c.agent_id')
                ->whereIn('c.id', $elsewhere)
                ->selectRaw('c.agent_id, u.name, u.employee_name, count(*) as total')
                ->groupBy('c.agent_id', 'u.name', 'u.employee_name')->get();
            $this->command->table(['Now under', 'Beneficiaries'], $owners->map(fn ($o) => [
                ($o->name ?? '?') . ($o->employee_name && $o->employee_name !== $o->name ? " ({$o->employee_name})" : '') . " #{$o->agent_id}",
                $o->total,
            ])->all());
        }

        if ($idTaken->isNotEmpty()) {
            $this->command->table(['ID', 'In the file', 'In this database now'], $idTaken->take(50)->map(fn ($id) => [
                $id,
                ($rows[$id]['name'] ?? '') . ' ' . ($rows[$id]['mobile'] ?? ''),
                $current[$id]->name . ' ' . $current[$id]->mobile,
            ])->all());
        }
    }
}
