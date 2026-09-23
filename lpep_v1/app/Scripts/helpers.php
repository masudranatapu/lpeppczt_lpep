<?php

use App\Models\User;
use App\Models\Contact;
use App\Models\Deposit;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Business;
use App\Models\Employee;
use App\Models\Withdraw;
use App\Models\UserPermission;
use Illuminate\Support\Facades\DB;
use App\Models\AgentCustomerTransaction;

if (!function_exists('checkStockQuantity')) {
    function checkStockQuantity($product_id, $qty)
    {
        $product = Product::findOrFail($product_id);

        if (isRole(ROLE_AGENT)) {
            $total_stock_in = $product->agentPurchaseProducts()->sum('quantity');
            $total_stock_out = $product->agentStockTransferDetails()->sum('quantity');
        } else {
            $total_stock_in = $product->purchaseProduct->sum('quantity');
            $total_stock_out = $product->saleProduct->sum('qty');
        }

        $current_stock = $total_stock_in - $total_stock_out;
        $total_stock_in_now = $current_stock - $qty;

        if ($total_stock_in_now < $current_stock) {
            return false;
        } else {
            return true;
        }
    }
}

if (!function_exists('permission')) {
    function cacheUserPermission($user_id, $shouldUpdate = false)
    {
        if ($shouldUpdate)
            cache()->forget('user-permission-' . $user_id);

        return cache()->rememberForever('user-permission-' . $user_id, function () use ($user_id) {
            return UserPermission::query()->where('user_id', $user_id)->with('role.permission')->first();
        });
    }

    function permission($data)
    {
        $userPermission = cacheUserPermission(auth()->user()->id);

        if (isset($userPermission->role->permission)) {
            $permission = $userPermission->role->permission;
        }

        if (in_array($data, json_decode($permission->permissions))) {
            return true;
        }
    }

    function abortIfNotPermitted($data)
    {
        $userPermission = cacheUserPermission(auth()->user()->id);

        if (isset($userPermission->role->permission)) {
            $permission = $userPermission->role->permission;
        }
        if (!in_array($data, json_decode($permission->permissions))) {
            abort(401);
        }
    }

    function abortIfNotRole($roleId)
    {
        $userPermission = cacheUserPermission(auth()->user()->id);

        if (isset($userPermission->role) && $userPermission->role->id != $roleId) {
            abort(401);
        }
    }

    function isRole($roleId)
    {
        return cacheUserPermission(auth()->user()->id)->role_id == $roleId;
    }
}


if (!function_exists('loadCurrentStockQuantityFromProduct')) {

    /**
     * Calculate Available Stock Quantity From Product
     */
    function loadCurrentStockQuantityFromProduct(Product $product)
    {
        if (isRole(ROLE_AGENT)) {
            $product->loadCount([
                'stockTransferDetails as transferred_in_qty' => fn($q) => $q->whereHas('transfer', fn($q) => $q->where('agent_id', auth()->id())->select(DB::raw('sum(quantity)'))),
            ]);
            return $product->transferred_in_qty + 0;
        } else {
            $product->loadCount([
                'purchaseProduct as purchase_qty' => fn($q) => $q->select(DB::raw('sum(quantity)')),
                'purchaseReturnProduct as purchase_return_qty' => fn($q) => $q->select(DB::raw('sum(quantity)')),
                'saleProducts as sale_qty' => fn($q) => $q->select(DB::raw('sum(qty)')),
                'saleReturnProducts as sale_return_qty' => fn($q) => $q->select(DB::raw('sum(qty)')),
                'stockTransferDetails as transferred_out_qty' => fn($q) => $q->select(DB::raw('sum(quantity)')),
                'agentStockTransferDetails as agent_transferred_qty' => fn($q) => $q->select(DB::raw('sum(quantity)')),
            ]);
            return $product->purchase_qty - $product->purchase_return_qty - $product->sale_qty + $product->sale_return_qty - $product->transferred_out_qty + $product->agent_transferred_qty + 0;
        }
    }

    /**
     * Calculate Available Stock Quantity
     */
    function getCurrentStockQuantityFromProduct(Product $product, $forAgent = false)
    {
        if (isRole(ROLE_AGENT) || $forAgent) {
            // return $product->transferred_in_qty + 0;
            return $product->transferred_in_qty - $product->sale_qty;
        } else {
            return $product->purchase_qty - $product->purchase_return_qty - $product->sale_qty + $product->sale_return_qty - $product->transferred_out_qty + $product->agent_transferred_qty + 0;
        }
    }
}

if (!function_exists('accountBalance')) {
    function accountBalance($account_id)
    {
        $deposit = Deposit::where('account_id', $account_id)->sum('amount');
        $withdraw = Withdraw::where('account_id', $account_id)->sum('amount');
        $expense = Expense::where('account_id', $account_id)->sum('amount');
        $salary = Employee::join('salaries', "salaries.employee_id", "=", 'employees.id')
            ->where('account_id', $account_id)
            ->sum('amount');

        $customer_payment = 0;
        foreach (Contact::where('type', "customer")->get() as $customer) {
            $customer_payment += $customer->customerPayment($account_id)->sum('paying_amount');
        }

        $supplier_payment = 0;
        foreach (Contact::where('type', "supplier")->get() as $customer) {
            $supplier_payment += $customer->customerPayment($account_id)->sum('paying_amount');
        }

        $balance = ($customer_payment + $deposit) - ($supplier_payment + $salary + $expense + $withdraw);
        return $balance;
    }
}

function currentBranch()
{
    return cache()->rememberForever('business', function () {
        return Business::findOrFail(1);
    });
}

/** Must be 10 digits
 * Must be unique in a shop
 */
function nextProductBarcode()
{
    $productId = '' . (Product::query()->select('id')->orderByDesc('id')->first()->id ?? '1');
    return date('ym') . str_pad($productId, 6, '0', STR_PAD_LEFT);
}

function getCachedAgents($fetch = false)
{
    if ($fetch)
        cache()->forget('agents');
    return cache()->rememberForever('agents', function () {
        return User::query()
            ->active()
            ->whereRelation('userPermission', 'role_id', '=', ROLE_AGENT)
            ->select('id', 'name')
            ->get();
    });
}

function getCustomerFinalLoanAmount($customer_id)
{
    return
        AgentCustomerTransaction::query()
        ->where('app_customer_id', $customer_id)
        ->where('type', TXN_SEND)
        ->sum('amount')
        -
        AgentCustomerTransaction::query()
        ->where('app_customer_id', $customer_id)
        ->where('type', TXN_RECEIVE)
        ->sum('amount');
}



if (!function_exists('checkImage')) {

    function checkImage($imagePath)
    {
        if (isset($imagePath)) {
            return asset(json_decode($imagePath));
        } else {
            return asset('img/default.png');
        }
    }
}


if (!function_exists('numFormat')) {
    function numFormat($number = 0)
    {
        return number_format($number, 2, '.', '');
    }
}



if (!function_exists('yajraButtons')) {
    function yajraViewEditDeleteButtons($view = '', $edit = '', $deleteRoute = '')
    {
        $btn = '<div class="d-flex justify-content-center align-items-center">';

        if (!empty($view)) {
            $btn .= "<a href='" . $view . "' class='btn btn-secondary btn-sm mr-2'><i class='fa fa-eye'></i> Details</a>";
        }

        if (!empty($edit)) {
            $btn .= "<a href='" . $edit . "' class='btn btn-primary btn-sm mr-2'><i class='fa fa-pencil-alt'></i> Edit</a>";
        }

        if (!empty($deleteRoute)) {
            $btn .= "<form action='" . $deleteRoute . "' method='POST' class='d-inline delete-form'>
                        <input type='hidden' name='_token' value='" . csrf_token() . "'>
                        <input type='hidden' name='_method' value='DELETE'>
                        <button type='submit' class='btn btn-danger btn-sm delete-button'>
                            <i class='fa fa-trash'></i> Delete
                        </button>
                    </form>";
        }

        $btn .= '</div>';

        return $btn;
    }
}


if (!function_exists('incomeTypeInlineTotal')) {
    function incomeTypeInlineTotal($incomeType = '', $details = [])
    {
        $totalAmount = 0;
        foreach ($details as  $detail) {
            foreach ($detail->income_types as $type => $amount) {
                if ($incomeType === $type) {
                    $totalAmount += $amount;
                }
            }
        }
        $num = number_format($totalAmount, 2);
        return str_replace(',', '', $num);
    }
}



if (!function_exists('sumAll')) {
    function sumAll($data = [])
    {
        $result = array_sum($data);
        return $result;
    }
}
