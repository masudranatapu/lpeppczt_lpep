<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

/**
 * Beneficiary export. The headings double as the import template for
 * the Area Office beneficiary import, so keep them in sync with
 * App\Imports\BeneficiaryImport.
 */
class AppCustomerExport extends DefaultValueBinder implements FromQuery, WithHeadings, WithMapping, WithCustomValueBinder, ShouldAutoSize, WithTitle
{
    public const HEADINGS = [
        'ID', 'Agent ID', 'Agent Name', 'Name', 'Mobile', 'Email',
        'Beneficiary Number', 'Group Number', 'Village', 'Union',
        'Cow', 'Bull', 'Bakna', 'Goat', 'Khasi',
        'Membership', 'Deworming', 'Bringing', 'Fattening', 'Treatment', 'AI', 'Medicine',
    ];

    // Mobile is column E. Written as text so Excel keeps the leading zero.
    private const TEXT_COLUMNS = ['E'];

    private Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query->with('agent:id,name')->orderBy('id');
    }

    public function headings(): array
    {
        return self::HEADINGS;
    }

    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->agent_id,
            $customer->agent?->name,
            $customer->name,
            $customer->mobile,
            $customer->email,
            $customer->beneficiary_number,
            $customer->group_number,
            $customer->village,
            $customer->unions,
            (int) $customer->cow,
            (int) $customer->bull,
            (int) $customer->bakna,
            (int) $customer->goat,
            (int) $customer->khasi,
            $customer->membership,
            $customer->deworming,
            $customer->bringing,
            $customer->fattening,
            $customer->treatment,
            $customer->ai,
            $customer->medicine,
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        if (in_array($cell->getColumn(), self::TEXT_COLUMNS, true) && $cell->getRow() > 1 && $value !== null) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function title(): string
    {
        return 'Beneficiaries';
    }
}
