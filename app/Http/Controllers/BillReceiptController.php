<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillCreditNote;
use App\Models\BillCreditNoteItem;
use App\Models\BillDebitNoteItem;
use App\Models\BillReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillReceiptController extends Controller
{
    /**
     * 
     */
    public function show($id)
    {
        $receipt = BillReceipt::find($id);
        return view('bill-receipt.show', compact('receipt'));
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
     * generate receipt
     */
    public function generateReceipt(Request $request)
    {
        $request->validate([
            'bill_id' => 'required',
            'supplier_id' => 'required',
            'bank_account_id' => 'required'
        ]);

        $bill = Bill::find($request->bill_id);

        $billItems = $bill->items;

        $total = 0;

        foreach ($billItems as $key => $item) {
            $item_total = $item->amount;

            $creditNoteItem = BillCreditNoteItem::where('bill_id', '=', $bill->id)
            ->where('item_id','=', $item->item_id)->get();

            foreach ($creditNoteItem as $creditNote) {
                $item_total -= $creditNote->amount;
            }

            $debitNoteItem = BillDebitNoteItem::where('bill_id', '=', $bill->id)
            ->where('item_id','=', $item->item_id)->get();

            foreach ($debitNoteItem as $debitNote) {
                $item_total += $debitNote->amount;
            }

            $total += $item_total;
        }

        // get previous receipts
        $receipts = $bill->receipt;

        $receiptsTotal = 0;

        foreach ($receipts as $receipt) {

            $receiptsTotal += $receipt->paid_amount;
        }

        $toPay = $total - $receiptsTotal;

        DB::beginTransaction();

        try {
            $calTax = $this->calculateTax($toPay);

            $receipt  = new BillReceipt();
            $receipt->bill_id = $bill->id;
            $receipt->supplier_id = $request->supplier_id;
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
            return redirect()->route('bill.receipt.show', $receipt->id)->with('success','Receipt generated successfully');

        } catch (\Exception $th) {
            DB::rollBack();

            return redirect()->back()->with('error','System error please try again');
        }

    }
}
