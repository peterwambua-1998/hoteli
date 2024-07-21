<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CreditNote;
use App\Models\CreditNoteItem;
use App\Models\DebitNoteItem;
use App\Models\Invoice;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiptController extends Controller
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required',
            'payment_method' => 'required',
            'bank_account_id' => 'required',
            'amount' => 'required',
        ]);

        $invoice = Invoice::find($request->invoice_id);

        DB::beginTransaction();

        try {
            $receipt  = new Receipt();
            $receipt->account_id = $request->account_id;
            $receipt->invoice_id = $request->invoice_id;
            $receipt->bank_account_id = $request->bank_account_id;
            $receipt->receipt_number = rand(1, 10000000);
            $receipt->payment_method = $request->payment_method;
            $receipt->payment_code = $request->payment_code;
            $receipt->sub_total = $invoice->sub_total;
            $receipt->tax_amount = $invoice->tax_amount;
            $receipt->amount = $request->amount;
            $receipt->save();

            $creditNote = CreditNote::where('invoice_id','=',$invoice->id)->first();

            $invoiceItems = $invoice->items;
            foreach ($invoiceItems as $key => $item) {
                // check if item has debit note

                $receiptItem = new ReceiptItem();
                $receiptItem->receipt_id = $receipt->id;
                $receiptItem->item_code = $item->item_code;
                $receiptItem->item_description = $item->item_description;
                $receiptItem->quantity = $item->quantity;
                $receiptItem->rate = $item->rate;
                $receiptItem->days = $item->days;
                $receiptItem->amount = $item->amount;
                $receiptItem->save();
            }

            DB::commit();

            return redirect()->route('accounts.show', $invoice->account_id)->with('success', 'Record saved successfully');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();

            return redirect()->route('accounts.show', $invoice->account_id)->with('error', 'System error please try again');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $receipt = Receipt::find($id);
        $account = Account::find($receipt->account_id);
        return view('receipts.show', compact('receipt', 'account'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Receipt $receipt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Receipt $receipt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Receipt $receipt)
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
        $total_amt = $subTotal + $vat;
        return [
            'subTotal' => $subTotal,
            'vat' => $vat,
            'total' => $total_amt,
        ];
    }

    /**
     * Generate invoice
     * 
     */
    public function receiptGenerator(Request $request)
    {
        // TODO check if there is previous payments for invoice and refund in receipt
        $request->validate([
            'invoice_id' => 'required'
        ]);

        $invoice = Invoice::find($request->invoice_id);

        $invoiceItems = $invoice->items;

        // receipts
        $receipts = $invoice->receipt;

        $total = 0;

        foreach ($invoiceItems as $key => $item) {
            $item_total = $item->amount;
            // credit note is to remove
            $creditNoteItem = CreditNoteItem::where('invoice_id','=', $invoice->id)
            ->where('item_id','=', $item->item_id)->get();

            foreach ($creditNoteItem as $creditNote) {
                $item_total -= $creditNote->amount;
            }

            // debit note is to add
            $debitNoteItem = DebitNoteItem::where('invoice_id','=', $invoice->id)
            ->where('item_id','=', $item->item_id)->get();

            foreach ($debitNoteItem as $debitNote) {
                $item_total += $debitNote->amount;
            }

            $total += $item_total;
        }

        $receiptsTotal = 0;

        foreach ($receipts as $key => $receipt) {

            $receiptsTotal += $receipt->paid_amount;
        }

        $toPay = $total - $receiptsTotal;

        DB::beginTransaction();
        try {
            $calTax = $this->calculateTax($toPay);

            $receipt  = new Receipt();
            $receipt->account_id = $request->account_id;
            $receipt->invoice_id = $request->invoice_id;
            $receipt->bank_account_id = $request->bank_account_id;
            $receipt->receipt_number = rand(1, 10000000);
            $receipt->payment_method = $request->payment_method;
            $receipt->payment_code = $request->payment_code;
            $receipt->sub_total = $calTax['subTotal'];
            $receipt->tax_amount = $calTax['vat'];
            $receipt->amount = $calTax['total'];
            $receipt->balance = $calTax['total'] - $request->amount;
            $receipt->paid_amount = $request->amount;
            $receipt->save();

            DB::commit();
            return redirect()->route('receipt.show', $receipt->id)->with('success','Receipt generated successfully');
        } catch (\Exception $th) {
            //throw $th;
            DB::rollBack();

            return redirect()->back()->with('error','System error please try again');
        }

    }

    /**
     * print invoice
     */
    public function print($id)
    {
        $receipt = Receipt::find($id);
        return view('receipt.print', compact('receipt'));
    }

    /**
     * withholding
     */
    public function withHolding(Request $request)
    {
        $receipt = Receipt::find($request->receipt_id);
        $receipt->withHolding = $receipt->withHolding;
        if ($receipt->update()) {
            return redirect()->back()->with('success', 'Receipt withholding updated');
        }
        return redirect()->back()->with('error', 'System error, please try again!');
    }
}
