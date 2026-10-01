<?php

namespace App\Imports;

use App\Models\AppCustomer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Imports the file produced by App\Exports\AppCustomerExport into one LSP.
 *
 * Every row creates a NEW beneficiary (a copy) under the chosen LSP; the
 * original beneficiary and its owner are never changed. The copy is flagged
 * with imported_at and, when the row has the original ID, imported_from_id.
 * A row is skipped when the same person is already under that LSP, so
 * importing the same file twice does not double it.
 */
class BeneficiaryImport implements ToCollection, WithHeadingRow
{
    private const GROUP_LIMIT = 40;
    private const MAX_REPORTED_ERRORS = 200;

    private const TEXT_FIELDS = ['name', 'mobile', 'email', 'village', 'union', 'membership', 'deworming', 'bringing', 'fattening', 'treatment', 'ai', 'medicine'];
    private const INTEGER_FIELDS = ['id', 'beneficiary_number', 'group_number', 'cow', 'bull', 'bakna', 'goat', 'khasi'];
    private const LIVESTOCK_FIELDS = ['cow', 'bull', 'bakna', 'goat', 'khasi'];

    private int $agentUserId;

    public array $report = ['created' => 0, 'skipped' => 0, 'errors' => []];

    /**
     * @param int $agentUserId users.id of the LSP the copies are created for
     */
    public function __construct(int $agentUserId)
    {
        $this->agentUserId = $agentUserId;
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $index => $row) {
                // Row 1 is the heading row.
                $this->importRow($row->toArray(), $index + 2);
            }
        });
    }

    private function importRow(array $row, int $rowNumber): void
    {
        $row = $this->normalize($row);

        if ($this->isEmpty($row)) {
            return;
        }

        // Older beneficiaries often have no (or unusual) beneficiary/group numbers,
        // so those are copied as they are; only name and mobile are required.
        $validator = Validator::make($row, [
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'beneficiary_number' => ['nullable', 'integer', 'min:0'],
            'group_number' => ['nullable', 'integer', 'min:0'],
            'village' => ['nullable', 'string', 'max:255'],
            'union' => ['nullable', 'string', 'max:255'],
            'cow' => ['nullable', 'integer', 'min:0'],
            'bull' => ['nullable', 'integer', 'min:0'],
            'bakna' => ['nullable', 'integer', 'min:0'],
            'goat' => ['nullable', 'integer', 'min:0'],
            'khasi' => ['nullable', 'integer', 'min:0'],
            'membership' => ['nullable', 'string', 'max:255'],
            'deworming' => ['nullable', 'string', 'max:255'],
            'bringing' => ['nullable', 'string', 'max:255'],
            'fattening' => ['nullable', 'string', 'max:255'],
            'treatment' => ['nullable', 'string', 'max:255'],
            'ai' => ['nullable', 'string', 'max:255'],
            'medicine' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            $this->skip($rowNumber, $row, implode(' ', $validator->errors()->all()));
            return;
        }

        $data = [
            'name' => $row['name'],
            'mobile' => $row['mobile'],
            'email' => $row['email'],
            'beneficiary_number' => $row['beneficiary_number'],
            'group_number' => $row['group_number'],
            'village' => $row['village'],
            'unions' => $row['union'],
            'membership' => $row['membership'],
            'deworming' => $row['deworming'],
            'bringing' => $row['bringing'],
            'fattening' => $row['fattening'],
            'treatment' => $row['treatment'],
            'ai' => $row['ai'],
            'medicine' => $row['medicine'],
        ];
        foreach (self::LIVESTOCK_FIELDS as $field) {
            $data[$field] = $row[$field] ?? 0;
        }

        // The ID column only records where the copy came from; the original is not touched.
        $sourceId = is_int($row['id']) && AppCustomer::query()->whereKey($row['id'])->exists() ? $row['id'] : null;

        $alreadyHere = AppCustomer::query()
            ->where('agent_id', $this->agentUserId)
            ->where(function ($query) use ($sourceId, $data) {
                $query->where(fn ($same) => $same->where('name', $data['name'])->where('mobile', $data['mobile']));
                if ($sourceId) {
                    $query->orWhere('imported_from_id', $sourceId)->orWhere('id', $sourceId);
                }
            })
            ->value('id');

        if ($alreadyHere) {
            $this->skip($rowNumber, $row, "Already under this LSP (ID {$alreadyHere}).");
            return;
        }

        $groupCount = $data['group_number'] === null ? 0 : AppCustomer::query()
            ->where('agent_id', $this->agentUserId)
            ->where('group_number', $data['group_number'])
            ->count();

        if ($groupCount >= self::GROUP_LIMIT) {
            $this->skip($rowNumber, $row, "Group {$data['group_number']} already has " . self::GROUP_LIMIT . ' members.');
            return;
        }

        AppCustomer::query()->create($data + [
            'agent_id' => $this->agentUserId,
            'password' => Hash::make(Str::random(20)),
            'date' => now()->toDateString(),
            'created_by' => $this->agentUserId,
            'imported_from_id' => $sourceId,
            'imported_at' => now(),
        ]);
        $this->report['created']++;
    }

    private function normalize(array $row): array
    {
        $normalized = [];

        foreach (self::TEXT_FIELDS as $field) {
            $value = $row[$field] ?? null;
            $value = $value === null ? '' : trim((string) $value);
            $normalized[$field] = $value === '' ? null : $value;
        }

        foreach (self::INTEGER_FIELDS as $field) {
            $value = $row[$field] ?? null;
            if (is_string($value)) {
                $value = trim($value);
            }
            if ($value === null || $value === '') {
                $normalized[$field] = null;
            } elseif (is_numeric($value) && (float) $value == (int) $value) {
                $normalized[$field] = (int) $value;
            } else {
                $normalized[$field] = $value; // left as-is so validation reports it
            }
        }

        // Excel drops the leading zero when a mobile cell is stored as a number.
        if ($normalized['mobile'] !== null && preg_match('/^1\d{9}$/', $normalized['mobile'])) {
            $normalized['mobile'] = '0' . $normalized['mobile'];
        }

        return $normalized;
    }

    private function isEmpty(array $row): bool
    {
        return collect($row)->filter(fn ($value) => $value !== null)->isEmpty();
    }

    private function skip(int $rowNumber, array $row, string $message): void
    {
        $this->report['skipped']++;

        if (count($this->report['errors']) < self::MAX_REPORTED_ERRORS) {
            $this->report['errors'][] = [
                'row' => $rowNumber,
                'name' => $row['name'] ?? '',
                'message' => $message,
            ];
        }
    }
}
