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
 * A row whose ID matches an existing beneficiary updates that record and
 * moves it to the LSP, so its farms, cattle, sales and visits stay linked.
 * A row without a matching ID creates a new beneficiary.
 */
class BeneficiaryImport implements ToCollection, WithHeadingRow
{
    private const GROUP_LIMIT = 40;
    private const MAX_REPORTED_ERRORS = 200;

    private const TEXT_FIELDS = ['name', 'mobile', 'email', 'village', 'union', 'membership', 'deworming', 'bringing', 'fattening', 'treatment', 'ai', 'medicine'];
    private const INTEGER_FIELDS = ['id', 'beneficiary_number', 'group_number', 'cow', 'bull', 'bakna', 'goat', 'khasi'];
    private const LIVESTOCK_FIELDS = ['cow', 'bull', 'bakna', 'goat', 'khasi'];

    private int $agentUserId;
    private array $blockedAgentIds;

    public array $report = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];

    /**
     * @param int   $agentUserId     users.id of the LSP the beneficiaries are assigned to
     * @param array $blockedAgentIds LSP user ids of other Area Offices; their beneficiaries are never moved
     */
    public function __construct(int $agentUserId, array $blockedAgentIds = [])
    {
        $this->agentUserId = $agentUserId;
        $this->blockedAgentIds = array_map('intval', $blockedAgentIds);
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

        if ($row['id'] !== null && !is_int($row['id'])) {
            $this->skip($rowNumber, $row, 'The ID must be a number.');
            return;
        }

        $existing = $row['id'] ? AppCustomer::query()->find($row['id']) : null;

        // Older beneficiaries often have no (or out of range) beneficiary/group
        // numbers, so those are only enforced for new beneficiaries.
        $validator = Validator::make($row, [
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'beneficiary_number' => $existing ? ['nullable', 'integer', 'min:0'] : ['required', 'integer', 'between:1,40'],
            'group_number' => $existing ? ['nullable', 'integer', 'min:0'] : ['required', 'integer', 'between:1,28'],
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

        if ($existing && in_array((int) $existing->agent_id, $this->blockedAgentIds, true)) {
            $this->skip($rowNumber, $row, "Beneficiary ID {$existing->id} belongs to another Area Office.");
            return;
        }

        if (!$existing) {
            $duplicate = AppCustomer::query()
                ->where('agent_id', $this->agentUserId)
                ->where('mobile', $data['mobile'])
                ->where('name', $data['name'])
                ->value('id');

            if ($duplicate) {
                $this->skip($rowNumber, $row, "Already exists for this LSP (ID {$duplicate}).");
                return;
            }
        }

        $groupCount = $data['group_number'] === null ? 0 : AppCustomer::query()
            ->where('agent_id', $this->agentUserId)
            ->where('group_number', $data['group_number'])
            ->when($existing, fn ($query) => $query->whereKeyNot($existing->id))
            ->count();

        if ($groupCount >= self::GROUP_LIMIT) {
            $this->skip($rowNumber, $row, "Group {$data['group_number']} already has " . self::GROUP_LIMIT . ' members.');
            return;
        }

        if ($existing) {
            $existing->update($data + ['agent_id' => $this->agentUserId, 'updated_by' => $this->agentUserId]);
            $this->report['updated']++;
            return;
        }

        AppCustomer::query()->create($data + [
            'agent_id' => $this->agentUserId,
            'password' => Hash::make(Str::random(20)),
            'date' => now()->toDateString(),
            'created_by' => $this->agentUserId,
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
