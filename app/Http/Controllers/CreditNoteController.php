<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\CreditNote;
use App\Models\CreditNoteItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditNoteController extends Controller
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
    public function create($invoice_item_id)
    {
        $invoice_item = InvoiceItem::find($invoice_item_id);
        $invoice = Invoice::find($invoice_item->invoice_id);
        $account = Account::find($invoice->account_id);
        return view('credit-note.create', compact('invoice_item', 'invoice', 'account'));
    }

    /**
     * calculate tax
     */
    public function calculateTax($totalAmount)
    {
        $subTotal = $totalAmount;
        $total = $totalAmount;

        $subTotal = round($subTotal / 1.16, 2);
        $vat = round($total - $subTotal, 2);
        $levy = round(0.02 * $subTotal, 2);
        $total_amt = $subTotal + $vat;
        return [
            'subTotal' => $subTotal,
            'vat' => $vat,
            'levy' => $levy,
            'total' => $total_amt,
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_item_id' => 'required'
        ]);

        $invoice = Invoice::find($request->invoice_id);

        $item = InvoiceItem::find($request->invoice_item_id);
        $item->credit_note_amount = $request->amount;
        $item->update();

        try {
            
            $product = Product::find($request->item_id);

            if ($product->taxable == 1) {
                $taxCal = $this->calculateTax($request->amount);
            } else {
                $taxCal = [
                    'subTotal' => $request->amount,
                    'vat' => 0,
                    'levy' => 0,
                    'total' => $request->amount,
                ];
            }

            $creditNote = new CreditNote();
            $creditNote->invoice_id = $request->invoice_id;
            $creditNote->account_id = $request->account_id;
            $creditNote->note_number = rand(1, 10000000);
            $creditNote->delivery_date = $invoice->delivery_date;
            $creditNote->tax_date = $invoice->tax_date;
            $creditNote->sub_total = $taxCal['subTotal'];
            $creditNote->tax_amount = $taxCal['vat'];
            $creditNote->levy = $taxCal['levy'];
            $creditNote->total = $request->amount;
            $creditNote->save();

            $creditNoteItem = new CreditNoteItem();
            $creditNoteItem->invoice_id = $request->invoice_id;
            $creditNoteItem->credit_note_id = $creditNote->id;
            $creditNoteItem->item_id = $request->item_id;
            $creditNoteItem->quantity = $request->quantity;
            $creditNoteItem->rate = $request->rate;
            $creditNoteItem->days = $request->days;
            $creditNoteItem->amount = $request->amount;
            $creditNoteItem->save();

            DB::commit();

            return redirect()->back()->with('success', 'Record saved successfully');
        } catch (\Exception $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'system error, please try again!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $creditNote = CreditNote::find($id);
        $invoice = Invoice::find($creditNote->invoice_id);
        $account = Account::find($invoice->account_id);
        return view('credit-note.show', compact('creditNote', 'invoice', 'account'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CreditNote $creditNote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CreditNote $creditNote)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CreditNote $creditNote)
    {
        //
    }
}
