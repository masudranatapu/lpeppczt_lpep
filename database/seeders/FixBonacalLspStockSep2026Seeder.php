<?php

namespace Database\Seeders;

use App\Service\WarehouseInventoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * One-off correction for Bonacal P oral suspension 1 liter (client stock count, 13-09-2026).
 *
 * Admin purchase WP-260900005 (1 pc) was entered for stock that never arrived
 * and was transferred to the Rajbari office (WT-260900003). The office has since
 * handed all its Bonacal to LSPs, so the extra unit now sits in one LSP's
 * software stock. FixAdminStockSep2026Seeder could not remove it for that reason.
 *
 * This seeder removes the purchase (with its payment) and the transfer, and
 * takes 1 Bonacal off the LSP who is physically short, chosen when it runs,
 * from that LSP's latest Bonacal assignment. Admin and office stock stay the
 * same; only the chosen LSP goes down by 1.
 *
 * Safety: rows are verified first and nothing changes if the data differs; the
 * plan is shown and must be confirmed; a JSON backup is written to
 * storage/app/stock-fix/; one transaction, re-checked before commit; running it
 * again is a no-op.
 *
 * Run: php artisan db:seed --class=FixBonacalLspStockSep2026Seeder
 */
class FixBonacalLspStockSep2026Seeder extends Seeder
{
    private const WAREHOUSE_ID = 1; // Rajbari Area Office
    private const PRODUCT_ID = 238;
    private const PRODUCT_NAME = 'Bonacal P oral suspension 1 liter';

    // [id, invoice_no, total_amount]
    private const PURCHASE = [65, 'WP-260900005', 194.91];
    // [id, invoice_no]
    private const TRANSFER = [66, 'WT-260900003'];

    private const CANCEL = 'Cancel - change nothing';

    public function run(WarehouseInventoryService $inventory)
    {
        $this->command->info('Stock correction: ' . self::PRODUCT_NAME . ' (1 pc)');

        $problems = $this->pendingProblems();
        if ($problems) {
            if ($this->isAlreadyApplied()) {
                $this->command->info('Already applied. Nothing to do.');
                return;
            }
            $this->command->error('The database does not match the expected rows. NOTHING was changed:');
            foreach ($problems as $problem) {
                $this->command->line('  - ' . $problem);
            }
            return;
        }

        $lsps = DB::table('lpep_warehouse_salesmen')->where('warehouse_id', self::WAREHOUSE_ID)->orderBy('name')->get(['id', 'name'])
            ->map(function ($lsp) use ($inventory) {
                $lsp->stock = $inventory->salesmanAvailable($lsp->id, self::PRODUCT_ID);
                return $lsp;
            })
            ->filter(fn ($lsp) => $lsp->stock >= 1)
            ->values();

        if ($lsps->isEmpty()) {
            $this->command->error('No Rajbari LSP has Bonacal in software stock. NOTHING was changed.');
            return;
        }

        $this->command->table(['LSP', 'Bonacal in software'], $lsps->map(fn ($lsp) => [$lsp->name, $lsp->stock])->all());

        $options = $lsps->mapWithKeys(fn ($lsp) => ["{$lsp->name} (LSP #{$lsp->id}, software stock {$lsp->stock})" => $lsp])->all();
        $answer = $this->command->choice('Which LSP is physically short by 1 Bonacal?', array_merge([self::CANCEL], array_keys($options)), 0);
        if ($answer === self::CANCEL) {
            $this->command->warn('Cancelled. Nothing was changed.');
            return;
        }
        $lsp = $options[$answer];

        $item = $this->latestAssignmentItem($lsp->id);
        if (!$item) {
            $this->command->error("No Bonacal assignment found for {$lsp->name}. NOTHING was changed.");
            return;
        }

        $before = $this->snapshot($inventory, $lsp->id);

        [$purchaseId, $purchaseInvoice, $amount] = self::PURCHASE;
        [$transferId, $transferInvoice] = self::TRANSFER;
        $this->command->table(['Action', 'Row', 'Detail'], [
            ['Delete purchase', "#{$purchaseId} {$purchaseInvoice}", "1 pc, total {$amount} with its payment"],
            ['Delete transfer', "#{$transferId} {$transferInvoice}", '1 pc to Rajbari'],
            ['Reduce LSP assignment', "#{$item->assignment_id} {$item->invoice_no} ({$item->assignment_date})", "{$lsp->name}: Bonacal {$item->quantity} -> " . ($item->quantity - 1)],
        ]);
        $this->command->table(['', 'Purchased', 'Transferred', 'Admin stock', 'Rajbari office', "{$lsp->name} stock"], [[
            self::PRODUCT_NAME,
            $before['purchased'] . ' -> ' . ($before['purchased'] - 1),
            $before['transferred'] . ' -> ' . ($before['transferred'] - 1),
            $before['admin'],
            $before['office'],
            $before['lsp'] . ' -> ' . ($before['lsp'] - 1),
        ]]);

        if (!$this->command->confirm('Apply these changes to the database?', false)) {
            $this->command->warn('Cancelled. Nothing was changed.');
            return;
        }

        $backupPath = $this->writeBackup($item);
        $this->command->info("Backup of the rows being changed: storage/app/{$backupPath}");

        DB::transaction(function () use ($inventory, $lsp, $item, $before) {
            DB::table('lpep_warehouse_purchases')->where('id', self::PURCHASE[0])->lockForUpdate()->first();
            DB::table('lpep_warehouse_salesman_assignments')->where('id', $item->assignment_id)->lockForUpdate()->first();

            if ($problems = $this->pendingProblems()) {
                throw new RuntimeException('Data changed while running: ' . implode(' | ', $problems));
            }
            $current = DB::table('lpep_warehouse_salesman_assignment_items')->find($item->item_id);
            if (!$current || !$this->same($current->quantity, $item->quantity)) {
                throw new RuntimeException('The LSP assignment changed while running.');
            }

            DB::table('warehouse_purchase_payments')->where('warehouse_purchase_id', self::PURCHASE[0])->delete();
            DB::table('lpep_warehouse_purchase_items')->where('warehouse_purchase_id', self::PURCHASE[0])->delete();
            DB::table('lpep_warehouse_purchases')->where('id', self::PURCHASE[0])->delete();
            DB::table('lpep_warehouse_stock_transfer_items')->where('warehouse_stock_transfer_id', self::TRANSFER[0])->delete();
            DB::table('lpep_warehouse_stock_transfers')->where('id', self::TRANSFER[0])->delete();

            if ($this->same($item->quantity, 1)) {
                DB::table('lpep_warehouse_salesman_assignment_items')->where('id', $item->item_id)->delete();
            } else {
                DB::table('lpep_warehouse_salesman_assignment_items')->where('id', $item->item_id)
                    ->update(['quantity' => $item->quantity - 1, 'updated_at' => now()]);
            }
            if (DB::table('lpep_warehouse_salesman_assignment_items')->where('warehouse_salesman_assignment_id', $item->assignment_id)->exists()) {
                DB::table('lpep_warehouse_salesman_assignments')->where('id', $item->assignment_id)
                    ->update(['total_quantity' => DB::raw('total_quantity - 1'), 'updated_at' => now()]);
            } else {
                DB::table('lpep_warehouse_salesman_assignments')->where('id', $item->assignment_id)->delete();
            }

            $after = $this->snapshot($inventory, $lsp->id);
            $ok = $this->same($after['purchased'], $before['purchased'] - 1)
                && $this->same($after['transferred'], $before['transferred'] - 1)
                && $this->same($after['admin'], $before['admin'])
                && $this->same($after['office'], $before['office'])
                && $this->same($after['lsp'], $before['lsp'] - 1)
                && $after['otherLsps'] == $before['otherLsps']
                && $after['admin'] >= 0 && $after['office'] >= 0 && $after['lsp'] >= 0;
            if (!$ok) {
                throw new RuntimeException('Result check failed, all changes were rolled back. Before ' . json_encode($before) . ', after ' . json_encode($after));
            }
        });

        $after = $this->snapshot($inventory, $lsp->id);
        $this->command->table(['After', 'Purchased', 'Transferred', 'Admin stock', 'Rajbari office', "{$lsp->name} stock"], [[
            self::PRODUCT_NAME, $after['purchased'], $after['transferred'], $after['admin'], $after['office'], $after['lsp'],
        ]]);
        $this->command->info("Done. Bonacal purchase is now {$after['purchased']} and {$lsp->name}'s stock matches the physical count.");
    }

    private function pendingProblems(): array
    {
        $problems = [];
        [$purchaseId, $purchaseInvoice, $amount] = self::PURCHASE;
        [$transferId, $transferInvoice] = self::TRANSFER;

        $purchase = DB::table('lpep_warehouse_purchases')->find($purchaseId);
        if (!$purchase) {
            $problems[] = "Purchase #{$purchaseId} ({$purchaseInvoice}) not found.";
        } else {
            if ($purchase->invoice_no !== $purchaseInvoice || $purchase->warehouse_id !== null || !$this->same($purchase->total_amount, $amount)) {
                $problems[] = "Purchase #{$purchaseId} is not {$purchaseInvoice} admin purchase of {$amount} (found {$purchase->invoice_no}, total {$purchase->total_amount}).";
            }
            if (!$this->onlyOneBonacal('lpep_warehouse_purchase_items', 'warehouse_purchase_id', $purchaseId)) {
                $problems[] = "Purchase #{$purchaseId} ({$purchaseInvoice}) does not hold exactly 1 Bonacal.";
            }
        }

        $transfer = DB::table('lpep_warehouse_stock_transfers')->find($transferId);
        if (!$transfer) {
            $problems[] = "Transfer #{$transferId} ({$transferInvoice}) not found.";
        } else {
            if ($transfer->invoice_no !== $transferInvoice || (int) $transfer->warehouse_id !== self::WAREHOUSE_ID || !$this->same($transfer->total_quantity, 1)) {
                $problems[] = "Transfer #{$transferId} is not {$transferInvoice} of 1 to the Rajbari office (found {$transfer->invoice_no}, warehouse {$transfer->warehouse_id}, total {$transfer->total_quantity}).";
            }
            if (!$this->onlyOneBonacal('lpep_warehouse_stock_transfer_items', 'warehouse_stock_transfer_id', $transferId)) {
                $problems[] = "Transfer #{$transferId} ({$transferInvoice}) does not hold exactly 1 Bonacal.";
            }
        }

        return $problems;
    }

    private function onlyOneBonacal(string $table, string $foreignKey, int $id): bool
    {
        $items = DB::table($table)->where($foreignKey, $id)->selectRaw('product_id, SUM(quantity) as quantity')->groupBy('product_id')->pluck('quantity', 'product_id');

        return $items->count() === 1 && $this->same($items->get(self::PRODUCT_ID), 1);
    }

    private function isAlreadyApplied(): bool
    {
        return !DB::table('lpep_warehouse_purchases')->where('id', self::PURCHASE[0])->exists()
            && !DB::table('lpep_warehouse_stock_transfers')->where('id', self::TRANSFER[0])->exists()
            && !DB::table('lpep_warehouse_stock_transfer_items')->where('warehouse_stock_transfer_id', self::TRANSFER[0])->exists();
    }

    private function latestAssignmentItem(int $lspId)
    {
        return DB::table('lpep_warehouse_salesman_assignment_items as i')
            ->join('lpep_warehouse_salesman_assignments as a', 'a.id', '=', 'i.warehouse_salesman_assignment_id')
            ->where('a.warehouse_id', self::WAREHOUSE_ID)
            ->where('a.warehouse_salesman_id', $lspId)
            ->where('i.product_id', self::PRODUCT_ID)
            ->where('i.quantity', '>=', 1)
            ->orderByDesc('a.assignment_date')->orderByDesc('a.id')
            ->first(['i.id as item_id', 'i.quantity', 'a.id as assignment_id', 'a.invoice_no', 'a.assignment_date']);
    }

    private function snapshot(WarehouseInventoryService $inventory, int $lspId): array
    {
        $otherLsps = DB::table('lpep_warehouse_salesmen')->where('id', '!=', $lspId)->pluck('id')
            ->mapWithKeys(fn ($id) => [$id => $inventory->salesmanAvailable($id, self::PRODUCT_ID)])->all();

        return [
            'purchased' => $inventory->adminPurchased(self::PRODUCT_ID),
            'transferred' => $inventory->adminTransferred(self::PRODUCT_ID),
            'admin' => $inventory->adminAvailable(self::PRODUCT_ID),
            'office' => $inventory->warehouseUnassignedAvailable(self::WAREHOUSE_ID, self::PRODUCT_ID),
            'lsp' => $inventory->salesmanAvailable($lspId, self::PRODUCT_ID),
            'otherLsps' => $otherLsps,
        ];
    }

    private function writeBackup($item): string
    {
        $backup = [
            'seeder' => static::class,
            'created_at' => now()->toDateTimeString(),
            'lpep_warehouse_purchases' => DB::table('lpep_warehouse_purchases')->where('id', self::PURCHASE[0])->get(),
            'lpep_warehouse_purchase_items' => DB::table('lpep_warehouse_purchase_items')->where('warehouse_purchase_id', self::PURCHASE[0])->get(),
            'warehouse_purchase_payments' => DB::table('warehouse_purchase_payments')->where('warehouse_purchase_id', self::PURCHASE[0])->get(),
            'lpep_warehouse_stock_transfers' => DB::table('lpep_warehouse_stock_transfers')->where('id', self::TRANSFER[0])->get(),
            'lpep_warehouse_stock_transfer_items' => DB::table('lpep_warehouse_stock_transfer_items')->where('warehouse_stock_transfer_id', self::TRANSFER[0])->get(),
            'lpep_warehouse_salesman_assignments' => DB::table('lpep_warehouse_salesman_assignments')->where('id', $item->assignment_id)->get(),
            'lpep_warehouse_salesman_assignment_items' => DB::table('lpep_warehouse_salesman_assignment_items')->where('id', $item->item_id)->get(),
        ];

        $path = 'stock-fix/FixBonacalLspStockSep2026-backup-' . now()->format('Ymd-His') . '.json';
        Storage::disk('local')->put($path, json_encode($backup, JSON_PRETTY_PRINT));

        return $path;
    }

    private function same($a, $b): bool
    {
        return abs((float) $a - (float) $b) < 0.001;
    }
}
