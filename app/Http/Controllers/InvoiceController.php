<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\CreditNote;
use App\Models\CreditNoteItem;
use App\Models\DebitNote;
use App\Models\DebitNoteItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Receipt;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($account_id)
    {
        $account = Account::find($account_id);
        $bankAccounts = BankAccount::all();
        return view('invoice.create', compact('account','bankAccounts'));
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
            $inv = new Invoice();
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
            $inv->user_id = Auth::user()->id;
            $inv->save();

            for ($i=0; $i < count($request->item_description); $i++) { 
                $inv_item = new InvoiceItem();
                $inv_item->invoice_id = $inv->id;
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
        $invoice = Invoice::find($id);
        $account = Account::find($invoice->account_id);
        $bankAccounts = BankAccount::all();
        $invoiceTotal = 0;
        $receiptTotal = 0;
        $balance = 0;

        // get invoice items
        $invoiceItems = $invoice->items;

        $creditNotes = new Collection();
        $debitNotes = new Collection();

        // credit notes 
        foreach ($invoiceItems as $item) {
            $item_total = $item->amount;
            // credit note for item
            $creditNoteItem = CreditNoteItem::where('invoice_id','=', $invoice->id)
            ->where('item_id','=', $item->item_id)->get();

            foreach ($creditNoteItem as $key => $creditNote) {
                $creditNotes->push($creditNote);
                $item_total -= $creditNote->amount;
            }

            // debit note for item
            $debitNoteItem = DebitNoteItem::where('invoice_id','=', $invoice->id)
            ->where('item_id','=', $item->item_id)->get();

            foreach ($debitNoteItem as $key => $debitNote) {
                $debitNotes->push($debitNote);
                $item_total += $debitNote->amount;
            }

            $invoiceTotal += $item_total;
        }

        $receipts = Receipt::where('invoice_id','=', $invoice->id)->get();

        foreach ($receipts as $key => $receipt) {
            $receiptTotal += $receipt->paid_amount;
        }

        $balance = $invoiceTotal - $receiptTotal;

        return view('invoice.show', compact('invoice', 'account', 'creditNotes','debitNotes','bankAccounts', 'invoiceTotal', 'receiptTotal', 'balance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $invoice = Invoice::find($id);
        $account = Account::find($invoice->account_id);
        $creditNotes = CreditNote::where('invoice_id','=',$invoice->id)->get();
        $bankAccounts = BankAccount::all();
        return view('invoice.edit', compact('invoice', 'account', 'creditNotes','bankAccounts'));
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
            $inv = Invoice::find($id);
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
            InvoiceItem::where('invoice_id','=', $inv->id)->delete();

            for ($i=0; $i < count($request->item_description); $i++) { 
                $inv_item = new InvoiceItem();
                $inv_item->invoice_id = $inv->id;
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
            return redirect()->back()->with('error', 'System error, please try again!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function creditNote(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required'
        ]);
        $invoice = Invoice::find($request->invoice_id);
        $invoice->withholding = $request->withholding;
        if ($invoice->update()) {
            return redirect()->back()->with('success', 'invoice withholding updated');
        }
        return redirect()->back()->with('error', 'System error, please try again!');
    }
}
