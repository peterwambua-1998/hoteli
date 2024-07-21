<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillDebitNote;
use App\Models\BillDebitNoteItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillDebitNoteController extends Controller
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
     * cal tax
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bill_item_id' => 'required'
        ]);

        $bill = Bill::find($request->bill_id);

        DB::beginTransaction();
        try {

            $product = Product::find($request->item_id);

            if ($product->taxable == 1) {
                $taxCal = $this->calculateTax($request->amount);
            } else {
                $taxCal = [
                    'subTotal' => $request->amount,
                    'vat' => 0,
                    'total' => $request->amount,
                ];
            }

            $creditNote = new BillDebitNote();
            $creditNote->bill_id = $request->bill_id;
            $creditNote->supplier_id = $request->supplier_id;
            $creditNote->note_number = rand(1, 10000000);
            $creditNote->sub_total = $taxCal['subTotal'];
            $creditNote->tax_amount = $taxCal['vat'];
            $creditNote->total = $request->amount;
            $creditNote->save();

            $creditNoteItem = new BillDebitNoteItem();
            $creditNoteItem->bill_id = $request->bill_id;
            $creditNoteItem->bill_debit_note_id = $creditNote->id;
            $creditNoteItem->item_id = $request->item_id;
            $creditNoteItem->quantity = $request->quantity;
            $creditNoteItem->rate = $request->rate;
            $creditNoteItem->amount = $request->amount;
            $creditNoteItem->save();

            DB::commit();

            return redirect()->back()->with('success', 'Record added Successfully');
        } catch (\Exception $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'System error please try again');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BillDebitNote $billDebitNote)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BillDebitNote $billDebitNote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BillDebitNote $billDebitNote)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BillDebitNote $billDebitNote)
    {
        //
    }
}
