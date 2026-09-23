<?php

namespace App\Exports;

use App\Models\Visit;
use App\Models\VisitInfo;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class VisitInfoExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, Responsable
{
    
    public $fileName = 'visit-info.xlsx';

    public function __construct(private array $filters = []) {}

    /**
     * Create a response for the export.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function toResponse($request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download($this, $this->fileName);
    }

    public function query()
    {
        /** @var Builder $q */
        $q = VisitInfo::query()->with(['appCustomer','fees']);

        if (!empty($this->filters['visit_date'])) {
            $q->whereDate('visit_date', $this->filters['visit_date']);
        }

        if (!empty($this->filters['agent_id'])) {
            $q->where('agent_id', $this->filters['agent_id']);
        } elseif (function_exists('isRole') && isRole(ROLE_AGENT)) {
            $q->where('agent_id', auth()->id());
        }

        return $q->orderBy('visit_date', 'desc');
    }

    public function headings(): array
    {
        return [
            '#SL',
            'Beneficiary Name',
            'Beneficiary Cell',
            'Beneficiary Group',
            'Beneficiary Number',
            'Visit Date & Time',
            'Invoice No(s)',
            'Total Amount',
            'Fee Types',
        ];
    }

    public function map($visit): array
    {
        $memos = is_array($visit->memo_no) ? implode(', ', $visit->memo_no) : '';
        $feeTypes = $visit->fees->map(fn($f)=> ($f->fee_type ?? 'N/A').': '.number_format($f->amount,2))->implode('; ');
        $total = number_format($visit->fees->sum('amount'), 2);

        return [
            $visit->id,
            $visit->appCustomer->name ?? 'N/A',
            $visit->appCustomer->mobile ?? 'N/A',
            $visit->customer_number,
            $visit->appCustomer->beneficiary_number ?? '',
            optional($visit->visit_date)->format('Y-m-d h:i A'),
            $memos ?: '—',
            $total,
            $feeTypes ?: 'No fees',
        ];
    }
}
