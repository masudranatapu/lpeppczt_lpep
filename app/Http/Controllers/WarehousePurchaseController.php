<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Account;
use App\Models\Contact;
use App\Models\WarehousePurchase;
use App\Models\WarehousePurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Service\WarehouseInventoryService;

class WarehousePurchaseController extends Controller
{
    private function ensureAdmin(): void
    {
        abort_unless(Auth::check() && Auth::user()?->userPermission?->role?->role_name === 'Admin', 403);
    }

    public function index(Request $request)
    {
        $this->ensureAdmin();

        $filterType = $request->input('filter_type', 'all');
        if (! in_array($filterType, ['all', 'month_year', 'year', 'custom'], true)) {
            $filterType = 'all';
        }

        $perPage = $request->input('per_page', 'all');
        if (! in_array($perPage, ['10', '20', '30', 'all'], true)) {
            $perPage = 'all';
        }

        $query = WarehousePurchase::query()
            ->with(['warehouse', 'supplier', 'items.product'])
            ->when($request->search, function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('invoice_no', 'like', "%{$search}%")
                        ->orWhereHas('supplier', fn ($supplierQuery) => $supplierQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('items.product', fn ($productQuery) => $productQuery->where('product_name', 'like', "%{$search}%"));
                });
            })
            ->when($filterType === 'month_year' && $request->year, fn ($query) => $query->whereYear('purchase_date', $request->year)->whereMonth('purchase_date', $request->month))
            ->when($filterType === 'year' && $request->year, fn ($query) => $query->whereYear('purchase_date', $request->year))
            ->when($filterType === 'custom' && $request->from_date && $request->to_date, fn ($query) => $query->whereBetween('purchase_date', [$request->from_date, $request->to_date]))
            ->latest();

        $pageSize = $perPage === 'all' ? max($query->count(), 1) : (int) $perPage;
        $purchases = $query->paginate($pageSize)->withQueryString();

        $accounts = Account::query()
            ->with('bank')
            ->whereIn('account_type', ['Mobile Banking', 'Card', 'Bank Account'])
            ->orderBy('account_type')
            ->get();

        return view('warehouse-purchase.index', compact('purchases', 'accounts'));
    }

    public function create(Request $request)
    {
        $this->ensureAdmin();

        $products = Product::query()
            ->active()
            ->orderBy('product_name')
            ->get(['id', 'product_name', 'selling_price']);
        $suppliers = Contact::query()->where('type', 'supplier')->orderBy('name')->get();
        $accounts = Account::query()
            ->with('bank')
            ->whereIn('account_type', ['Mobile Banking', 'Card', 'Bank Account'])
            ->orderBy('account_type')
            ->get();

        return view('warehouse-purchase.create', compact('products', 'accounts', 'suppliers'));
    }

    public function edit(WarehousePurchase $warehouse_purchase)
    {
        $this->ensureAdmin();
        abort_if($warehouse_purchase->isLegacyWarehouseReceipt(), 404);

        $warehouse_purchase->load('items');
        $existingProductIds = $warehouse_purchase->items->pluck('product_id');
        $products = Product::query()
            ->where(function ($query) use ($existingProductIds) {
                $query->where('status', 1)->orWhereIn('id', $existingProductIds);
            })
            ->orderBy('product_name')
            ->get(['id', 'product_name', 'selling_price']);
        $suppliers = Contact::query()->where('type', 'supplier')->orderBy('name')->get();
        $accounts = Account::query()->with('bank')
            ->whereIn('account_type', ['Mobile Banking', 'Card', 'Bank Account'])
            ->orderBy('account_type')->get();

        return view('warehouse-purchase.create', [
            'products' => $products,
            'accounts' => $accounts,
            'suppliers' => $suppliers,
            'purchase' => $warehouse_purchase,
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'supplier_id' => ['required', 'exists:contacts,id'],
            'purchase_date' => ['required', 'date'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'notes' => ['nullable', 'string'],
            'pay_by' => ['required', 'in:Cash,Mobile Banking,Card,Bank Account'],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.purchase_price' => ['required', 'numeric', 'min:0'],
            'items.*.sale_price' => ['required', 'numeric', 'min:0'],
        ]);

        if ($data['pay_by'] !== 'Cash') {
            $accountIsValid = Account::query()
                ->whereKey($data['account_id'] ?? null)
                ->where('account_type', $data['pay_by'])
                ->exists();

            if (! $accountIsValid) {
                throw ValidationException::withMessages([
                    'account_id' => 'Please select a valid account for the chosen payment method.',
                ]);
            }
        }

        $attachmentPath = $request->hasFile('attachment')
            ? $request->file('attachment')->store('uploads/warehouse-purchase-invoices', ['disk' => 'public_uploads'])
            : null;

        try {
            DB::transaction(function () use ($data, $attachmentPath) {
                $total = collect($data['items'])->sum(function ($item) {
                    return round($item['quantity'] * $item['purchase_price'], 2);
                });

                if ($data['paid_amount'] > $total) {
                    throw ValidationException::withMessages([
                        'paid_amount' => 'Paid amount cannot be greater than the purchase total.',
                    ]);
                }

                $purchase = WarehousePurchase::create([
                    'warehouse_id' => null,
                    'supplier_id' => $data['supplier_id'],
                    'purchase_date' => $data['purchase_date'],
                    'invoice_no' => WarehousePurchase::nextInvoiceNo(),
                    'attachment' => $attachmentPath,
                    'notes' => $data['notes'] ?? null,
                    'total_amount' => $total,
                    'pay_by' => $data['pay_by'],
                    'account_id' => $data['pay_by'] === 'Cash' ? null : $data['account_id'],
                    'paid_amount' => $data['paid_amount'],
                    'due_amount' => $total - $data['paid_amount'],
                    'created_by' => Auth::id(),
                ]);

                foreach ($data['items'] as $item) {
                    $lineTotal = round($item['quantity'] * $item['purchase_price'], 2);

                    WarehousePurchaseItem::create([
                        'warehouse_purchase_id' => $purchase->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'purchase_price' => $item['purchase_price'],
                        'sale_price' => $item['sale_price'],
                        'total' => $lineTotal,
                    ]);
                }

                if ($data['paid_amount'] > 0) {
                    $purchase->payments()->create([
                        'payment_date' => $data['purchase_date'],
                        'pay_by' => $data['pay_by'],
                        'account_id' => $data['pay_by'] === 'Cash' ? null : $data['account_id'],
                        'amount' => $data['paid_amount'],
                        'notes' => 'Initial purchase payment',
                        'created_by' => Auth::id(),
                    ]);
                }

            });
        } catch (\Throwable $exception) {
            if ($attachmentPath) {
                Storage::disk('public_uploads')->delete($attachmentPath);
            }

            throw $exception;
        }

        return redirect()->route('warehouse-purchases.index')->with('message', 'Admin purchase saved to central stock successfully.');
    }

    public function update(Request $request, WarehousePurchase $warehouse_purchase)
    {
        $this->ensureAdmin();
        abort_if($warehouse_purchase->isLegacyWarehouseReceipt(), 404);

        $data = $request->validate([
            'supplier_id' => ['required', 'exists:contacts,id'],
            'purchase_date' => ['required', 'date'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'remove_attachment' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'pay_by' => ['required', 'in:Cash,Mobile Banking,Card,Bank Account'],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.purchase_price' => ['required', 'numeric', 'min:0'],
            'items.*.sale_price' => ['required', 'numeric', 'min:0'],
        ]);

        if ($data['pay_by'] !== 'Cash') {
            $accountIsValid = Account::query()->whereKey($data['account_id'] ?? null)
                ->where('account_type', $data['pay_by'])->exists();
            if (! $accountIsValid) {
                throw ValidationException::withMessages(['account_id' => 'Please select a valid account for the chosen payment method.']);
            }
        }

        $newAttachmentPath = $request->hasFile('attachment')
            ? $request->file('attachment')->store('uploads/warehouse-purchase-invoices', ['disk' => 'public_uploads'])
            : null;
        $oldAttachmentPath = $warehouse_purchase->attachment;

        try {
            DB::transaction(function () use ($data, $warehouse_purchase, $newAttachmentPath) {
                $purchase = WarehousePurchase::with('items')->lockForUpdate()->findOrFail($warehouse_purchase->id);
                $newItems = collect($data['items']);
                $total = $newItems->sum(fn ($item) => round($item['quantity'] * $item['purchase_price'], 2));
                $alreadyPaid = (float) $purchase->paid_amount;
                $newPaid = (float) $data['paid_amount'];

                if ($newPaid < $alreadyPaid) {
                    throw ValidationException::withMessages(['paid_amount' => 'Paid amount cannot be less than the amount already recorded.']);
                }
                if ($newPaid > $total) {
                    throw ValidationException::withMessages(['paid_amount' => 'Paid amount cannot be greater than the purchase total.']);
                }

                foreach ($purchase->items->pluck('product_id')->merge($newItems->pluck('product_id'))->unique() as $productId) {
                    $otherPurchased = WarehousePurchaseItem::query()
                        ->where('warehouse_purchase_id', '!=', $purchase->id)
                        ->where('product_id', $productId)
                        ->whereHas('purchase', fn ($query) => $query->whereNull('warehouse_id'))
                        ->sum('quantity');
                    $newQuantity = $newItems->where('product_id', $productId)->sum('quantity');
                    $transferred = DB::table('lpep_warehouse_stock_transfer_items')->where('product_id', $productId)->sum('quantity');
                    if ((float) $otherPurchased + (float) $newQuantity < (float) $transferred) {
                        throw ValidationException::withMessages(['items' => 'Purchased quantity cannot be reduced below stock already transferred to Area Offices.']);
                    }
                }

                $purchase->update([
                    'supplier_id' => $data['supplier_id'],
                    'purchase_date' => $data['purchase_date'],
                    'attachment' => $newAttachmentPath ?: (($data['remove_attachment'] ?? false) ? null : $purchase->attachment),
                    'notes' => $data['notes'] ?? null,
                    'total_amount' => $total,
                    'pay_by' => $data['pay_by'],
                    'account_id' => $data['pay_by'] === 'Cash' ? null : $data['account_id'],
                    'paid_amount' => $newPaid,
                    'due_amount' => $total - $newPaid,
                ]);

                $purchase->items()->delete();
                foreach ($data['items'] as $item) {
                    $purchase->items()->create([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'purchase_price' => $item['purchase_price'],
                        'sale_price' => $item['sale_price'],
                        'total' => round($item['quantity'] * $item['purchase_price'], 2),
                    ]);
                }

                if ($newPaid > $alreadyPaid) {
                    $purchase->payments()->create([
                        'payment_date' => $data['purchase_date'],
                        'pay_by' => $data['pay_by'],
                        'account_id' => $data['pay_by'] === 'Cash' ? null : $data['account_id'],
                        'amount' => $newPaid - $alreadyPaid,
                        'notes' => 'Payment added while updating purchase',
                        'created_by' => Auth::id(),
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            if ($newAttachmentPath) {
                Storage::disk('public_uploads')->delete($newAttachmentPath);
            }
            throw $exception;
        }

        if ($oldAttachmentPath && ($newAttachmentPath || ($data['remove_attachment'] ?? false))) {
            Storage::disk('public_uploads')->delete($oldAttachmentPath);
        }

        return redirect()->route('warehouse-purchases.index')->with('message', 'Admin purchase updated successfully.');
    }

    public function show(WarehousePurchase $warehouse_purchase)
    {
        $this->ensureAdmin();

        $warehouse_purchase->load(['warehouse', 'supplier', 'items.product.unit', 'account.bank', 'payments.account.bank', 'payments.createdBy']);

        return view('warehouse-purchase.show', ['purchase' => $warehouse_purchase]);
    }

    public function destroy(WarehousePurchase $warehouse_purchase, WarehouseInventoryService $inventory)
    {
        $this->ensureAdmin();

        $attachmentPath = $warehouse_purchase->attachment;

        DB::transaction(function () use ($warehouse_purchase, $inventory) {
            $purchase = WarehousePurchase::with('items')->lockForUpdate()->findOrFail($warehouse_purchase->id);

            if ($purchase->warehouse_id === null) {
                foreach ($purchase->items->groupBy('product_id') as $productId => $items) {
                    $otherPurchased = WarehousePurchaseItem::query()
                        ->where('warehouse_purchase_id', '!=', $purchase->id)
                        ->where('product_id', $productId)
                        ->whereHas('purchase', fn ($query) => $query->whereNull('warehouse_id'))
                        ->sum('quantity');
                    $transferred = DB::table('lpep_warehouse_stock_transfer_items')->where('product_id', $productId)->sum('quantity');
                    if ((float) $otherPurchased < (float) $transferred) {
                        throw ValidationException::withMessages(['purchase' => 'This purchase cannot be deleted because its stock has already been transferred.']);
                    }
                }
            } else {
                foreach ($purchase->items->groupBy('product_id') as $productId => $items) {
                    $inventory->lockWarehouseProduct((int) $purchase->warehouse_id, (int) $productId);
                    $remainingReceived = $inventory->warehouseReceived((int) $purchase->warehouse_id, (int) $productId)
                        - (float) $items->sum('quantity');
                    $committed = $inventory->warehouseAssigned((int) $purchase->warehouse_id, (int) $productId)
                        + $inventory->warehouseDirectSold((int) $purchase->warehouse_id, (int) $productId);
                    if ($remainingReceived < $committed || $remainingReceived < $inventory->warehouseSold((int) $purchase->warehouse_id, (int) $productId)) {
                        throw ValidationException::withMessages(['purchase' => 'This legacy purchase cannot be deleted because its stock has already been assigned or sold.']);
                    }
                }
            }

            $purchase->delete();
        });

        if ($attachmentPath) {
            Storage::disk('public_uploads')->delete($attachmentPath);
        }

        return redirect()
            ->route('warehouse-purchases.index')
            ->with('message', 'Admin purchase deleted successfully.');
    }

    public function invoice(WarehousePurchase $warehouse_purchase)
    {
        $this->ensureAdmin();

        $warehouse_purchase->load(['warehouse', 'supplier', 'items.product.unit', 'account.bank', 'createdBy']);

        return view('warehouse-purchase.invoice', [
            'purchase' => $warehouse_purchase,
            'business' => currentBranch(),
        ]);
    }

    public function payDue(Request $request, WarehousePurchase $warehouse_purchase)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'payment_date' => ['required', 'date'],
            'pay_by' => ['required', 'in:Cash,Mobile Banking,Card,Bank Account'],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($data['pay_by'] !== 'Cash') {
            $accountIsValid = Account::query()
                ->whereKey($data['account_id'] ?? null)
                ->where('account_type', $data['pay_by'])
                ->exists();

            if (! $accountIsValid) {
                throw ValidationException::withMessages([
                    'account_id' => 'Please select a valid account for the chosen payment method.',
                ]);
            }
        }

        DB::transaction(function () use ($data, $warehouse_purchase) {
            $purchase = WarehousePurchase::query()->lockForUpdate()->findOrFail($warehouse_purchase->id);
            $dueAmount = max((float) $purchase->due_amount, 0);

            if ($dueAmount <= 0) {
                throw ValidationException::withMessages(['amount' => 'This purchase has no outstanding due amount.']);
            }

            if ((float) $data['amount'] > $dueAmount) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment cannot be greater than the outstanding due amount.',
                ]);
            }

            $purchase->payments()->create([
                'payment_date' => $data['payment_date'],
                'pay_by' => $data['pay_by'],
                'account_id' => $data['pay_by'] === 'Cash' ? null : $data['account_id'],
                'amount' => $data['amount'],
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $purchase->update([
                'paid_amount' => (float) $purchase->paid_amount + (float) $data['amount'],
                'due_amount' => max($dueAmount - (float) $data['amount'], 0),
                'pay_by' => $data['pay_by'],
                'account_id' => $data['pay_by'] === 'Cash' ? null : $data['account_id'],
            ]);
        });

        return redirect()
            ->route('warehouse-purchases.index')
            ->with('message', 'Due payment recorded successfully.');
    }
}
