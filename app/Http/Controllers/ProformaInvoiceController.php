<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\ProformaInvoice;
use App\Models\ProformaInvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProformaInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $proforma = ProformaInvoice::all();


        return view('proforma.index', compact('proforma'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($account_id)
    {
        $items = Product::orderBy('created_at', 'desc')->get();
        $account = Account::find($account_id);
        $bankAccounts = BankAccount::all();
        return view('proforma.create', compact('account', 'bankAccounts','items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required',
            'delivery_date' => 'required',
            'tax_date' => 'required',
            'invoiced_to' => 'required',
            'vat_registration_number' => 'required',
            'sub_total' => 'required',
            'total' => 'required',
            'tax_amount' => 'required',
        ]);


        DB::beginTransaction();
        try {
            $inv = new ProformaInvoice();
            $inv->account_id = $request->account_id;
            $inv->inv_number = $request->inv_number;
            $inv->delivery_date = $request->delivery_date;
            $inv->tax_date = $request->tax_date;
            $inv->to_date = $request->to_date;
            $inv->from_date = $request->from_date;
            $inv->invoiced_to = $request->invoiced_to;
            $inv->vat_registration_number = $request->vat_registration_number;
            $inv->sub_total = $request->sub_total;
            $inv->tax_amount = $request->tax_amount;
            $inv->levy = $request->levy;
            $inv->total = $request->total;
            $inv->save();

            for ($i=0; $i < count($request->item_description); $i++) { 
                $inv_item = new ProformaInvoiceItem();
                $inv_item->proforma_invoice_id = $inv->id;
                $inv_item->item_id = $request->item_id[$i];
                $inv_item->item_code = $request->item_code[$i];
                $inv_item->item_description = $request->item_description[$i];
                $inv_item->quantity = $request->quantity[$i];
                $inv_item->rate = $request->rate[$i];
                $inv_item->days = $request->days[$i];
                $inv_item->amount = $request->amount[$i];
                $inv_item->save();
            }

            DB::commit();
            return redirect()->route('accounts.show', $request->account_id)->with('success', 'Record added successfully');

        } catch (\Exception $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $invoice = ProformaInvoice::find($id);
        $account = Account::find($invoice->account_id);
        $bankAccounts = BankAccount::all();
        return view('proforma.show', compact('invoice', 'account', 'bankAccounts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $invoice = ProformaInvoice::find($id);
        $account = Account::find($invoice->account_id);
        $bankAccounts = BankAccount::all();
        return view('proforma.edit', compact('invoice', 'account', 'bankAccounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'account_id' => 'required',
            'delivery_date' => 'required',
            'tax_date' => 'required',
            'invoiced_to' => 'required',
            'vat_registration_number' => 'required',
            'sub_total' => 'required',
            'total' => 'required',
            'tax_amount' => 'required',
        ]);


        DB::beginTransaction();
        try {
            $inv = ProformaInvoice::find($id);
            $inv->account_id = $request->account_id;
            $inv->inv_number = $request->inv_number;
            $inv->delivery_date = $request->delivery_date;
            $inv->tax_date = $request->tax_date;
            $inv->to_date = $request->to_date;
            $inv->from_date = $request->from_date;
            $inv->invoiced_to = $request->invoiced_to;
            $inv->vat_registration_number = $request->vat_registration_number;
            $inv->sub_total = $request->sub_total;
            $inv->tax_amount = $request->tax_amount;
            $inv->levy = $request->levy;
            $inv->total = $request->total;
            $inv->update();

            // delete items
            ProformaInvoiceItem::where('proforma_invoice_id','=', $inv->id)->delete();

            for ($i=0; $i < count($request->item_description); $i++) { 
                $inv_item = new ProformaInvoiceItem();
                $inv_item->proforma_invoice_id = $inv->id;
                $inv_item->item_id = $request->item_id[$i];
                $inv_item->item_code = $request->item_code[$i];
                $inv_item->item_description = $request->item_description[$i];
                $inv_item->quantity = $request->quantity[$i];
                $inv_item->rate = $request->rate[$i];
                $inv_item->days = $request->days[$i];
                $inv_item->amount = $request->amount[$i];
                $inv_item->save();
            }

            DB::commit();
            return redirect()->route('accounts.show', $request->account_id)->with('success', 'Record added successfully');

        } catch (\Exception $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProformaInvoice $proformaInvoice)
    {
        //
    }

    /**
     * convert proforma to invoice
     */
    public function convertToInvoice(Request $request)
    {
        $proforma = ProformaInvoice::find($request->proforma_id);

        DB::beginTransaction();
        try {
            $inv = new Invoice();
            $inv->account_id = $proforma->account_id;
            $inv->bank_account_id = $proforma->bank_account_id;
            $inv->inv_number = $proforma->inv_number;
            $inv->delivery_date = $proforma->delivery_date;
            $inv->tax_date = $proforma->tax_date;
            $inv->invoiced_to = $proforma->invoiced_to;
            $inv->vat_registration_number = $proforma->vat_registration_number;
            $inv->sub_total = $proforma->sub_total;
            $inv->tax_amount = $proforma->tax_amount;
            $inv->total = $proforma->total;
            $inv->user_id = Auth::user()->id;
            $inv->save();

            foreach ($proforma->items as $key => $item) {
                $inv_item = new InvoiceItem();
                $inv_item->invoice_id = $inv->id;
                $inv_item->item_id = $item->item_id;
                $inv_item->item_code = $item->item_code;
                $inv_item->item_description = $item->item_description;
                $inv_item->quantity = $item->quantity;
                $inv_item->rate = $item->rate;
                $inv_item->days = $item->days;
                $inv_item->amount = $item->amount;
                $inv_item->save();
            }

            DB::commit();

            return redirect()->route('invoice.show', $inv->id)->with('success', 'Invoice generated!');

        } catch (\Exception $th) {
            dd($th->getMessage(), $th->getLine());
            DB::rollBack();
            return redirect()->back()->with('error', 'System error, please try again!');
        }
    } 
}
