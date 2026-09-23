<?php

namespace App\Models;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\SaleReturn;
use App\Models\CustomerGroup;
use App\Models\ContactPayment;
use App\Models\PurchaseReturn;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use App\Models\AgentCustomerTransaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory, HasApiTokens;

    protected $guarded = [];

    public function customerGroups()
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }

    public function sale()
    {
        return $this->hasMany(Sale::class, 'contact_id');
    }

    public function saleReturns()
    {
        return $this->hasMany(SaleReturn::class, 'contact_id');
    }

    public function purchase()
    {
        return $this->hasMany(Purchase::class, 'contact_id');
    }

    public function contactPayment()
    {
        return $this->hasMany(ContactPayment::class, 'contact_id');
    }

    public function customerPayment($account_id)
    {
        return $this->hasMany(ContactPayment::class, 'contact_id')->where('account_id', $account_id);
    }

    public function customerCashPayment()
    {
        return $this->hasMany(ContactPayment::class, 'contact_id')->whereNull('account_id');
    }

    public function contactPayments()
    {
        return $this->hasMany(ContactPayment::class, 'contact_id')
            ->whereNull("purchase_id")
            ->whereNull("purchase_return_id")
            ->whereNull("sale_id")
            ->whereNull("sale_return_id");
    }

    public function customerSalePayments()
    {
        return $this->hasMany(ContactPayment::class, 'contact_id')
            ->whereNotNull("sale_id");
    }

    public function customerSaleReturnPayments()
    {
        return $this->hasMany(ContactPayment::class, 'contact_id')
            ->whereNotNull("sale_return_id");
    }

    public function supplierPurchasePayments()
    {
        return $this->hasMany(ContactPayment::class, 'contact_id')
            ->whereNotNull("purchase_id");
    }

    public function supplierPurchaseReturnPayments()
    {
        return $this->hasMany(ContactPayment::class, 'contact_id')
            ->whereNotNull("purchase_return_id");
    }

    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class, 'contact_id');
    }

    public function customerReceivedLoans()
    {
        return $this->hasMany(AgentCustomerTransaction::class, 'app_customer_id')->where('type', TXN_SEND);
    }


    public static function is_deleteable($id)
    {

        $orders = Sale::query()->where('contact_id', $id)->count();
        $saleReturns = SaleReturn::query()->where('contact_id', $id)->count();
        $purchase = Purchase::query()->where('contact_id', $id)->count();
        $payments = ContactPayment::query()
            ->where('contact_id', $id)
            ->count();
        $customerReceivedLoans = AgentCustomerTransaction::query()
            ->where('app_customer_id', $id)
            ->count();
        if ($orders == 0 and $saleReturns == 0 and $purchase == 0  and  $payments == 0 and $customerReceivedLoans == 0) {

            return true;
        } else {
            return false;
        }
    }
}
