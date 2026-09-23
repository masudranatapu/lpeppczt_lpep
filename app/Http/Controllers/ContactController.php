<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Models\CustomerGroup;
use App\Models\ContactPayment;
use Illuminate\Support\Facades\Hash;

class ContactController extends Controller
{
    public function contact(Request $request)
    {
        $type = $request->type;
        if (empty($request->type)) {
            return redirect('/home');
        }

        return view('contact.index', [
            'type' => $type
        ]);
    }

    public function createSupplier()
    {
        return view('contact.partial.supplierCreate', []);
    }

    public function createCustomer()
    {
        return view('contact.partial.customerCreate', [
            'customer_groups' => CustomerGroup::query()->get()
        ]);
    }

    public function contactAjax(Request $request)
    {
        $request->validate(['type' => 'required|in:customer,supplier']);

        $contacts = Contact::query()
            ->where('type', $request->type)
            ->where('name', 'LIKE', "%$request->q%")
            ->select("id", "name as text")
            ->skip(($request->page - 1) * 5)
            ->limit(5)
            ->get();

        $contacts_count = Contact::query()
            ->where('type', $request->type)
            ->where('name', 'LIKE', "%$request->q%")
            ->select("id", "name")
            ->skip(($request->page) * 5)
            ->limit(1)
            ->get()
            ->count();

        return response()->json([
            'items' => $contacts,
            'pagination' => ['more' => $contacts_count > 0]
        ]);
    }

    public function contactStore(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'address' => 'required',
        ]);

        $contact = new Contact;
        $contact->type = $request->type;
        $contact->supplier_business_name = $request->supplier_business_name;
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->mobile = $request->mobile;
        $contact->note = $request->note;
        $contact->address = $request->address;

        if ($request->customer_group_id) {
            $contact->customer_group_id = $request->customer_group_id;
        }

        if ($contact->save()) {
            return response()->json("New Contact Added Successfully");
        }
    }

    public function contactEdit($id)
    {
        $contact = Contact::find($id);
        if ($contact->type == 'customer') {
            return view('contact.partial.customerEdit', [
                'contact' => $contact,
                'customer_groups' => CustomerGroup::query()->get()
            ]);
        } else {
            return view('contact.partial.supplierEdit', [
                'contact' => $contact,
            ]);
        }
    }

    public function contactUpdate(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'address' => 'required',
        ]);
        $contact = Contact::find($request->id);

        $contact->type = $request->type;
        $contact->supplier_business_name = $request->supplier_business_name;
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->mobile = $request->mobile;
        $contact->note = $request->note;
        $contact->address = $request->address;

        if ($request->customer_group_id == 0) {
            $contact->customer_group_id = null;
        } else {
            $contact->customer_group_id = $request->customer_group_id;
        }

        if ($contact->save()) {
            return response()->json("Contact Information Updated Successfully");
        }
    }

    public function statusUpdate(Request $request)
    {
        $contact = Contact::findOrFail($request->id);
        if ($contact->status == 1) {
            $contact->status = 0;
            $contact->save();
            return response()->json('Contact Deactived Successfully');
        } else {
            $contact->status = 1;
            $contact->save();
            return response()->json('Contact Active Successfully');
        }
    }


    public function contactListJson($type)
    {
        $data = Contact::query()
            ->where('contacts.type', $type)
            ->when($type == "customer", function ($query) {
                $query->leftJoin('customer_groups', 'customer_groups.id', '=', 'contacts.customer_group_id')
                    ->select('contacts.*', "customer_groups.name as group_name");
            })
            ->latest()
            ->get();

        if ($type == "customer") {
            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    if ($data->name == "Guest") {
                        return "N/A";
                    }

                    $btn = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle waves-effect btn-sm" data-toggle="dropdown" aria-expanded="false"> Action <span class="caret"></span> </button>
                    <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px; will-change: transform;">';
                    if (permission('c3')) {
                        $btn = $btn . '<a data-id="' . $data->id . '" class="dropdown-item" id="contactEdit">Edit</a>';
                    }
                    if (permission('c4')) {
                        if ($data->status == 1) {
                            $btn = $btn . '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" id="deleteData" class="dropdown-item">Deactive</a>';
                        } else {
                            $btn = $btn . '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" id="deleteData" class="dropdown-item">Active</a>';
                        }
                    }

                    $btn = $btn . '<a href="' . route('sale', ['customer' => $data->id]) . '" data-toggle="tooltip"  data-id="' . $data->id . '" class="dropdown-item">Orders</a>';
                    $btn = $btn . '<a href="javascript:viod(0)" onclick="customerDelete(' . $data->id . ')" data-toggle="tooltip"  data-id="' . $data->id . '" class="dropdown-item">Delete</a>';

                    $btn = $btn . "</div></div>";
                    return $btn;
                })
                ->rawColumns([
                    'action',
                ])
                ->setRowClass(fn ($data) => $data->status == 1 ? '' : 'bg-danger')
                ->addIndexColumn()
                ->make(true);
        } else {
            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $btn = '<div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle waves-effect btn-sm" data-toggle="dropdown" aria-expanded="false"> Action <span class="caret"></span> </button><div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px; will-change: transform;">';
                    if (permission('s3')) {
                        $btn = $btn . '<a data-id="' . $data->id . '" class="dropdown-item" id="contactEdit">Edit</a>';
                    }
                    if (permission('s4')) {
                        if ($data->status == 1) {
                            $btn = $btn . '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" id="deleteData" class="dropdown-item">Deactive</a>';
                        } else {
                            $btn = $btn . '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" id="deleteData" class="dropdown-item">Active</a>';
                        }
                    }

                    $btn = $btn . "</div></div>";
                    return $btn;
                })
                ->rawColumns([
                    'action',
                ])
                ->setRowClass(fn ($data) => $data->status == 1 ? '' : 'bg-danger')
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function supplierJson(Request $request)
    {
        return view('contact.partial.jsonSupplier', [
            'contacts' => Contact::where('type', 'supplier')
                ->get(),
        ]);
    }

    public function customerJson()
    {
        return view('contact.partial.jsonCustomer', [
            'customers' => Contact::query()->where('type', 'customer')->get(),
        ]);
    }

    public function contactGroupCheck(Request $request)
    {
        $contact = Contact::findOrFail($request->contact_id);

        if ($contact->customerGroups) {
            return $contact->customerGroups->amount;
        }
    }

    public function contactGroupCheckAgent(Request $request)
    {
        $contact = Contact::query()
            ->withSum('customerReceivedLoans as received_loans', 'amount')
            ->withSum('customerSalePayments as used_loans', 'paying_amount')
            ->findOrFail($request->contact_id);

        return response()->json([
            'group_amount' => $contact->customerGroups ? $contact->customerGroups->amount : 0,
            'available_points' => $contact->received_loans - $contact->used_loans
        ]);
    }

    public function contactPay(Request $request)
    {
        $contact = Contact::query()
            ->withSum("contactPayments as total_normal_paid_amount", "paying_amount")
            ->when($request->type == "customer", function ($query) {
                $query->withSum("sale as total_sale", "total_amount")
                    ->withSum("saleReturns as total_sale_return", "total_amount")
                    ->withSum("customerSalePayments as total_sale_paid_amount", "paying_amount")
                    ->withSum("customerSaleReturnPayments as total_sale_return_paid_amount", "paying_amount");
            })
            ->when($request->type == "supplier", function ($query) {
                $query->withSum("purchase as total_purchase", "total")
                    ->withSum("purchaseReturns as total_purchase_return", "total")
                    ->withSum("supplierPurchasePayments as total_purchase_paid_amount", "paying_amount")
                    ->withSum("supplierPurchaseReturnPayments as total_purchase_return_paid_amount", "paying_amount")
                    ->withSum("contactPayments as total_normal_paid_amount", "paying_amount");
            })
            ->find($request->id);

        $view = $request->type == "customer" ? "contact.partial.customerPayment" : "contact.partial.supplierPayment";
        return view($view, compact('contact'));
    }

    public function paymentStore(Request $request)
    {
        $payment = new ContactPayment();
        $payment->contact_id = $request->id;
        $payment->paying_amount = $request->paying_amount * $request->money_sign;
        $payment->paying_date = $request->paying_date;
        $payment->pay_by = $request->pay_by;
        if ($payment->save()) {
            return back()->with('message', "Payment Successfully Done");
        }
    }

    public function businessWiseContact()
    {
        return Contact::query()
            ->get();
    }

    public function customerDelete($id)
    {
        try {
            if (Contact::is_deleteable($id)) {
                $contact = Contact::query()
                    ->findOrFail($id);
                $contact->delete();
            } else {
                return response()->json('Customer Cannot Deleted', 400);
            }

            return response()->json('Customer Successfully Deleted');
        } catch (\Throwable $th) {
            return response()->json('Customer Cannot Deleted', 400);
        }
    }
}
