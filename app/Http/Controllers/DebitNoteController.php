<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\DebitNote;
use App\Models\DebitNoteItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebitNoteController extends Controller
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
    public function create()
    {
        //
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
        $item->debit_note_amount = $request->amount;
        $item->update();


        DB::beginTransaction();
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


            $debitNote = new DebitNote();
            $debitNote->invoice_id = $request->invoice_id;
            $debitNote->account_id = $request->account_id;
            $debitNote->note_number = rand(1, 10000000);
            $debitNote->delivery_date = $invoice->delivery_date;
            $debitNote->tax_date = $invoice->tax_date;
            $debitNote->sub_total = $taxCal['subTotal'];
            $debitNote->levy = $taxCal['levy'];
            $debitNote->tax_amount = $taxCal['vat'];
            $debitNote->total = $request->amount;
            $debitNote->save();

            $debitNoteItem = new DebitNoteItem();
            $debitNoteItem->invoice_id = $request->invoice_id;
            $debitNoteItem->debit_note_id = $debitNote->id;
            $debitNoteItem->item_id = $request->item_id;
            $debitNoteItem->quantity = $request->quantity;
            $debitNoteItem->rate = $request->rate;
            $debitNoteItem->days = $request->days;
            $debitNoteItem->amount = $request->amount;
            $debitNoteItem->save();

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
        $debitNote = DebitNote::find($id);
        $account = Account::find($debitNote->account_id);
        $invoice = Invoice::find($debitNote->invoice_id);
        $bankAccount = BankAccount::find($invoice->bank_account_id);
        return view('debit-note.show', compact('debitNote', 'account', 'bankAccount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DebitNote $debitNote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DebitNote $debitNote)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DebitNote $debitNote)
    {
        //
    }
}
