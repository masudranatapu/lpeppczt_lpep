<?php

namespace App\Service;

use App\Models\AppCustomer;
use App\Models\Income;
use App\Models\IncomeType;
use App\Models\User;
use App\Models\UserPermission;
use App\Models\VisitFee;
use App\Models\VisitInfo;
use App\Models\WarehouseSale;
use App\Models\WarehouseSaleAdminIntegration;
use App\Models\WarehouseSalesman;

class WarehouseSaleAdminSyncService
{
    /**
     * Remove the admin-side records generated for a warehouse sale.
     * Customer records are intentionally preserved because they may be reused.
     */
    public function deleteSaleSync(WarehouseSale $sale): void
    {
        $integration = WarehouseSaleAdminIntegration::where('warehouse_sale_id', $sale->id)->first();
        $invoice = (string) $sale->invoice_no;
        $visits = VisitInfo::query()
            ->where(function ($query) use ($invoice, $integration) {
                $query->whereJsonContains('memo_no', $invoice);
                if ($integration) {
                    $query->orWhere('id', $integration->visit_info_id);
                }
            })
            ->with('fees')
            ->get()
            ->unique('id');

        // The integration has RESTRICT foreign keys to both visit and income.
        // Remove it first; the in-memory object still provides the linked IDs below.
        if ($integration) {
            $integration->delete();
        }

        $incomeAmounts = [];
        foreach ($visits as $visit) {
            $amount = (float) $visit->fees->sum('amount');
            $incomeKey = $visit->visit_date ? \Carbon\Carbon::parse($visit->visit_date)->format('Y-m-d') : null;
            if ($incomeKey && $amount > 0) {
                $incomeAmounts[$incomeKey] = ($incomeAmounts[$incomeKey] ?? 0) + $amount;
            }
            $visit->fees()->delete();
            $visit->delete();
        }

        if ($integration) {
            if (!$incomeAmounts || !array_filter($incomeAmounts)) {
                $income = Income::find($integration->income_id);
                $incomeAmounts[$income && $income->income_date ? \Carbon\Carbon::parse($income->income_date)->format('Y-m-d') : ''] = (float) $integration->income_amount;
            }
            foreach ($incomeAmounts as $date => $amount) {
                if (!$date) continue;
                $income = Income::whereDate('income_date', $date)->first();
                $detail = $income?->details()->where('user_id', $integration->user_id)->first();
            if ($detail && $amount > 0) {
                $types = $detail->income_types ?? [];
                $remaining = $amount;
                foreach ($types as $type => $value) {
                    if ($remaining <= 0) break;
                    if ($type === 'Warehouse Sales' || stripos((string) $type, 'sale') !== false) {
                        $deduction = min((float) $value, $remaining);
                        $types[$type] = round((float) $value - $deduction, 2);
                        $remaining -= $deduction;
                    }
                }
                $newTotal = max(0, (float) $detail->total - $amount);
                $detail->update(['income_types' => $types, 'total' => $newTotal]);
                if ($newTotal <= 0.0001) {
                    $detail->delete();
                }
            }
                // Income headers can be shared by other legacy records. Keep the
                // header and only adjust the LSP user's detail above.
            }
        }
    }

    public function sync(WarehouseSale $sale, WarehouseSalesman $salesman): void
    {
        $integration = WarehouseSaleAdminIntegration::where('warehouse_sale_id', $sale->id)->first();
        if ($integration) {
            $this->reconcileExistingSale($sale, $integration);
            return;
        }

        $user = $this->resolveUser($salesman);
        $customer = $this->resolveCustomer($sale, $user);
        $sale->loadMissing('items.product.category');

        IncomeType::firstOrCreate(['income_type' => 'Warehouse Sales']);
        $incomeTypes = IncomeType::query()->pluck('income_type');
        $incomeAmounts = array_fill_keys($incomeTypes->all(), 0);
        $feeRows = [];
        $incomeTotal = 0;
        $paidRatio = (float) $sale->total_amount > 0
            ? min((float) $sale->paid_amount / (float) $sale->total_amount, 1)
            : 0;

        foreach ($sale->items as $item) {
            $label = $item->product?->product_name === 'Membership'
                ? 'Membership'
                : ($item->product?->category?->category_name ?: $item->product?->product_name ?: 'Sales');
            $matchedType = $incomeTypes->first(fn ($type) => stripos($type, $label) !== false || stripos($label, $type) !== false);
            $amount = round((float) $item->total * $paidRatio, 2);
            $incomeType = $matchedType ?: 'Warehouse Sales';
            $feeType = $matchedType ?: $label;
            $feeRows[$feeType] = ($feeRows[$feeType] ?? 0) + $amount;
            $incomeAmounts[$incomeType] = (float) ($incomeAmounts[$incomeType] ?? 0) + $amount;
            $incomeTotal += $amount;
        }

        $feeTotal = array_sum(array_map(fn ($amount) => round($amount), $feeRows));
        $feeDifference = round((float) $sale->paid_amount - $feeTotal);
        if ($feeRows && $feeDifference !== 0) {
            $lastFeeType = array_key_last($feeRows);
            $feeRows[$lastFeeType] = round($feeRows[$lastFeeType]) + $feeDifference;
        }
        $incomeDifference = round((float) $sale->paid_amount - array_sum($incomeAmounts), 2);
        $incomeType = array_key_exists('Warehouse Sales', $incomeAmounts)
            ? 'Warehouse Sales'
            : array_key_last($incomeAmounts);
        if ($incomeType !== null) {
            $incomeAmounts[$incomeType] = round((float) $incomeAmounts[$incomeType] + $incomeDifference, 2);
        }
        $incomeTotal = round((float) $sale->paid_amount, 2);

        $visit = VisitInfo::create([
            // Visit-info uses customer_number for the beneficiary group and
            // beneficiary_number for the customer's individual number.
            'customer_number' => $customer->group_number,
            'app_customer_id' => $customer->id,
            'memo_no' => [$sale->invoice_no],
            'visit_date' => $sale->sale_date,
            'area_id' => $user->id,
            'agent_id' => $user->id,
            'description' => 'Auto LSP sale ' . $sale->invoice_no . ' (paid amount)',
            'beneficiary_number' => $customer->beneficiary_number ?: $sale->customer_phone ?: $sale->invoice_no,
            'visit_type' => 'N.V',
        ]);

        foreach ($feeRows as $feeType => $amount) {
            VisitFee::create(['visit_info_id' => $visit->id, 'fee_type' => VisitFee::normalizeType($feeType), 'amount' => round($amount)]);
        }

        $income = Income::firstOrCreate(['income_date' => $sale->sale_date]);
        $detail = $income->details()->where('user_id', $user->id)->lockForUpdate()->first();
        if ($detail) {
            $existing = $detail->income_types ?? [];
            foreach ($incomeAmounts as $type => $amount) {
                $existing[$type] = (float) ($existing[$type] ?? 0) + (float) $amount;
            }
            $detail->update([
                'income_types' => $existing,
                'total' => (float) $detail->total + $incomeTotal,
                'is_absent' => 0,
                'note' => 'Includes LSP warehouse sales.',
            ]);
        } else {
            $income->details()->create([
                'user_id' => $user->id,
                'income_types' => $incomeAmounts,
                'total' => $incomeTotal,
                'is_absent' => 0,
                'note' => 'Generated from LSP warehouse sales.',
            ]);
        }

        WarehouseSaleAdminIntegration::create([
            'warehouse_sale_id' => $sale->id,
            'user_id' => $user->id,
            'app_customer_id' => $customer->id,
            'visit_info_id' => $visit->id,
            'income_id' => $income->id,
            'income_amount' => $incomeTotal,
        ]);
    }

    public function syncPayment(WarehouseSale $sale, $payment): void
    {
        $salesman = $sale->salesman;
        if (!$salesman) {
            return;
        }

        $user = $this->resolveUser($salesman);
        $customer = $this->resolveCustomer($sale, $user);
        $sale->loadMissing('items.product.category');
        IncomeType::firstOrCreate(['income_type' => 'Warehouse Sales']);
        $incomeTypes = IncomeType::query()->pluck('income_type');
        $incomeAmounts = array_fill_keys($incomeTypes->all(), 0);
        $feeRows = [];
        $ratio = (float) $sale->total_amount > 0 ? (float) $payment->amount / (float) $sale->total_amount : 0;
        $incomeTotal = 0;

        foreach ($sale->items as $item) {
            $label = $item->product?->category?->category_name ?: $item->product?->product_name ?: 'Sales';
            $amount = round((float) $item->total * $ratio, 2);
            $incomeType = $incomeTypes->first(fn ($type) => stripos($type, $label) !== false || stripos($label, $type) !== false) ?: 'Warehouse Sales';
            $feeRows[$label] = ($feeRows[$label] ?? 0) + $amount;
            $incomeAmounts[$incomeType] = (float) ($incomeAmounts[$incomeType] ?? 0) + $amount;
            $incomeTotal += $amount;
        }

        $feeTotal = array_sum(array_map(fn ($amount) => round($amount), $feeRows));
        $feeDifference = round((float) $payment->amount - $feeTotal);
        if ($feeRows && $feeDifference !== 0) {
            $lastFeeType = array_key_last($feeRows);
            $feeRows[$lastFeeType] = round($feeRows[$lastFeeType]) + $feeDifference;
        }
        $incomeDifference = round((float) $payment->amount - array_sum($incomeAmounts), 2);
        $incomeType = array_key_exists('Warehouse Sales', $incomeAmounts)
            ? 'Warehouse Sales'
            : array_key_last($incomeAmounts);
        if ($incomeType !== null) {
            $incomeAmounts[$incomeType] = round((float) $incomeAmounts[$incomeType] + $incomeDifference, 2);
        }
        $incomeTotal = round((float) $payment->amount, 2);

        $visit = VisitInfo::create([
            'customer_number' => $customer->group_number,
            'app_customer_id' => $customer->id,
            'memo_no' => [$sale->invoice_no],
            'visit_date' => $payment->payment_date,
            'area_id' => $user->id,
            'agent_id' => $user->id,
            'description' => 'Due For Due Payment',
            'beneficiary_number' => $customer->beneficiary_number ?: $sale->customer_phone ?: $sale->invoice_no,
            'visit_type' => 'N.V',
        ]);

        foreach ($feeRows as $feeType => $amount) {
            if ($amount > 0) {
                VisitFee::create(['visit_info_id' => $visit->id, 'fee_type' => VisitFee::normalizeType($feeType), 'amount' => round($amount)]);
            }
        }

        $income = Income::firstOrCreate(['income_date' => $payment->payment_date]);
        $detail = $income->details()->where('user_id', $user->id)->lockForUpdate()->first();
        if ($detail) {
            $existing = $detail->income_types ?? [];
            foreach ($incomeAmounts as $type => $amount) {
                $existing[$type] = (float) ($existing[$type] ?? 0) + (float) $amount;
            }
            $detail->update(['income_types' => $existing, 'total' => (float) $detail->total + $incomeTotal, 'is_absent' => 0, 'note' => 'Includes LSP due payments.']);
        } else {
            $income->details()->create(['user_id' => $user->id, 'income_types' => $incomeAmounts, 'total' => $incomeTotal, 'is_absent' => 0, 'note' => 'Generated from LSP due payment.']);
        }
    }

    private function reconcileExistingSale(WarehouseSale $sale, WarehouseSaleAdminIntegration $integration): void
    {
        $oldAmount = (float) $integration->income_amount;
        $newAmount = (float) $sale->paid_amount;
        if ($oldAmount <= 0 || abs($oldAmount - $newAmount) < 0.01) {
            return;
        }

        $ratio = $newAmount / $oldAmount;
        $visit = VisitInfo::with('fees')->find($integration->visit_info_id);
        if ($visit) {
            foreach ($visit->fees as $fee) {
                $fee->update(['amount' => round((float) $fee->amount * $ratio)]);
            }
        }

        $income = Income::find($integration->income_id);
        $detail = $income?->details()->where('user_id', $integration->user_id)->first();
        if ($detail) {
            $types = $detail->income_types ?? [];
            foreach ($types as $type => $amount) {
                if ($type === 'Warehouse Sales' || stripos((string) $type, 'sale') !== false) {
                    $types[$type] = round((float) $amount * $ratio, 2);
                }
            }
            $detail->update(['income_types' => $types, 'total' => max(0, (float) $detail->total - $oldAmount + $newAmount)]);
        }

        $integration->update(['income_amount' => $newAmount]);
    }

    private function resolveUser(WarehouseSalesman $salesman): User
    {
        $user = $salesman->user_id ? User::find($salesman->user_id) : null;
        $user ??= User::where('email', $salesman->email)->first();

        if (!$user) {
            $mobile = $salesman->phone;
            if (!$mobile || User::where('mobile', $mobile)->exists()) {
                $mobile = 'LSP-' . $salesman->id;
            }
            $user = User::create([
                'name' => $salesman->warehouse?->name ?: $salesman->name,
                'employee_name' => $salesman->name,
                'mobile' => $mobile,
                'email' => $salesman->email,
                'password' => $salesman->password,
                'user_type' => 'staff',
                'status' => 1,
            ]);
        }

        $permission = UserPermission::firstOrNew(['user_id' => $user->id]);
        $permission->role_id = ROLE_AGENT;
        $permission->save();
        if ((int) $salesman->user_id !== (int) $user->id) {
            $salesman->update(['user_id' => $user->id]);
        }

        return $user;
    }

    private function resolveCustomer(WarehouseSale $sale, User $user): AppCustomer
    {
        $query = AppCustomer::where('agent_id', $user->id);
        $customer = $sale->customer_phone
            ? (clone $query)->where('mobile', $sale->customer_phone)->first()
            : (clone $query)->where('name', $sale->customer_name)->first();

        return $customer ?: AppCustomer::create([
            'agent_id' => $user->id,
            'name' => $sale->customer_name,
            'mobile' => $sale->customer_phone,
            'created_by' => $user->id,
        ]);
    }
}
