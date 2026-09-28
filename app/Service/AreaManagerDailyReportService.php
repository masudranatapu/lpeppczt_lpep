<?php

namespace App\Service;

use App\Models\Warehouse;
use App\Models\WarehouseSale;
use App\Models\WarehouseSalesman;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

/**
 * Area Manager Daily Earning Report: LSP sales of one Area Office, per day and
 * per LSP, split into the service columns of the paper report.
 */
class AreaManagerDailyReportService
{
    public const COLUMNS = [
        'deworming' => 'Deworming',
        'anestrus' => 'Anestrus',
        'fattening' => 'Fattening',
        'treatment' => 'Treat & Oth',
        'ai' => 'A/I',
        'medicine' => 'Medicine',
    ];

    public function build(Warehouse $warehouse, string $month): array
    {
        $monthStart = Carbon::createFromFormat('Y-m-d', $month . '-01')->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth();
        $lastDay = $monthEnd->copy()->min(Carbon::today());
        $target = (float) ($warehouse->daily_target ?? 5000);

        $sales = $monthStart->gt($lastDay) ? collect() : WarehouseSale::query()
            ->with('items.product.category')
            ->where('warehouse_id', $warehouse->id)
            ->whereNotNull('warehouse_salesman_id')
            ->whereBetween('sale_date', [$monthStart->toDateString(), $lastDay->toDateString()])
            ->get();

        $lsps = WarehouseSalesman::query()
            ->where('warehouse_id', $warehouse->id)
            ->where(fn ($query) => $query->where('status', 1)->orWhereIn('id', $sales->pluck('warehouse_salesman_id')->unique()))
            ->orderBy('id')
            ->get(['id', 'name']);

        // amounts[date][lsp_id][column]
        $amounts = [];
        foreach ($sales as $sale) {
            $date = Carbon::parse($sale->sale_date)->toDateString();
            foreach ($this->splitSale($sale) as $column => $amount) {
                $amounts[$date][$sale->warehouse_salesman_id][$column] = ($amounts[$date][$sale->warehouse_salesman_id][$column] ?? 0) + $amount;
            }
        }

        // Every day except Friday up to today, plus any Friday that has sales.
        $dates = $monthStart->gt($lastDay) ? collect() : collect(CarbonPeriod::create($monthStart, $lastDay))
            ->filter(fn ($day) => $day->dayOfWeek !== Carbon::FRIDAY || isset($amounts[$day->toDateString()]))
            ->values();

        $days = [];
        $runningSales = 0;
        $monthTotals = array_fill_keys(array_keys(self::COLUMNS), 0.0) + ['total' => 0.0];
        foreach ($dates as $index => $day) {
            $key = $day->toDateString();
            $rows = [];
            $dayTotals = array_fill_keys(array_keys(self::COLUMNS), 0.0) + ['total' => 0.0];
            foreach ($lsps as $lsp) {
                $row = ['name' => $lsp->name, 'total' => 0.0];
                foreach (self::COLUMNS as $column => $label) {
                    $row[$column] = round($amounts[$key][$lsp->id][$column] ?? 0, 2);
                    $row['total'] += $row[$column];
                    $dayTotals[$column] += $row[$column];
                }
                $dayTotals['total'] += $row['total'];
                $rows[] = $row;
            }
            foreach ($dayTotals as $column => $value) {
                $monthTotals[$column] += $value;
            }
            $runningSales += $dayTotals['total'];

            $days[] = [
                'date' => $day,
                'rows' => $rows,
                'totals' => $dayTotals,
                'target' => $target,
                'sales' => $dayTotals['total'],
                'treatment' => $dayTotals['treatment'],
                'achievement' => $target > 0 ? $dayTotals['total'] / $target * 100 : 0,
                'average_achievement' => $target > 0 ? $runningSales / ($target * ($index + 1)) * 100 : 0,
            ];
        }

        $monthTarget = $target * count($days);

        return [
            'warehouse' => $warehouse,
            'month' => $monthStart->format('Y-m'),
            'monthLabel' => $monthStart->format('M-y'),
            'areaLabel' => trim($warehouse->name . ($warehouse->address ? ', ' . $warehouse->address : '')) . ' Area',
            'columns' => self::COLUMNS,
            'lsps' => $lsps,
            'days' => $days,
            'target' => $target,
            'monthTotals' => $monthTotals,
            'monthTarget' => $monthTarget,
            'monthAchievement' => $monthTarget > 0 ? $monthTotals['total'] / $monthTarget * 100 : 0,
        ];
    }

    /**
     * Net amount of a sale per report column. The invoice discount is shared
     * out by line value; a sale without lines counts as Medicine.
     */
    private function splitSale(WarehouseSale $sale): array
    {
        $subtotal = (float) $sale->items->sum('total');
        if ($subtotal <= 0) {
            return ['medicine' => (float) $sale->total_amount];
        }

        $ratio = (float) $sale->total_amount / $subtotal;
        $split = [];
        foreach ($sale->items as $item) {
            $column = $this->columnFor($item->product?->category?->category_name);
            $split[$column] = ($split[$column] ?? 0) + (float) $item->total * $ratio;
        }

        return $split;
    }

    private function columnFor(?string $category): string
    {
        $category = strtolower(trim((string) $category));

        if (str_contains($category, 'deworm')) return 'deworming';
        if (str_contains($category, 'anestrus')) return 'anestrus';
        if (str_contains($category, 'fattening')) return 'fattening';
        if (str_contains($category, 'treatment')) return 'treatment';
        if (str_contains($category, 'insemination') || in_array($category, ['ai', 'a/i'], true)) return 'ai';

        return 'medicine';
    }
}
