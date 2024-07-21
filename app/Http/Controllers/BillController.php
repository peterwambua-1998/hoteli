<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Bill;
use App\Models\BillCreditNote;
use App\Models\BillCreditNoteItem;
use App\Models\BillDebitNoteItem;
use App\Models\BillItem;
use App\Models\BillReceipt;
use App\Models\GoodReceiveNote;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bills = Bill::all();
        return view('bills.index', compact('bills'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        return view('bills.create', compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'note_id' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $note = GoodReceiveNote::find($request->note_id);

            $bill = new Bill();
            $bill->supplier_id = $note->supplier_id;
            $bill->bill_number = rand(100, 10000000);
            $bill->sub_total = $note->sub_total;
            $bill->vat = $note->vat;
            $bill->total = $note->total;
            $bill->save();

            $noteItems = $note->items;

            foreach ($noteItems as $key => $item) {
                $billItem = new BillItem();
                $billItem->bill_id = $bill->id;
                $billItem->item_id = $item->item_id;
                $billItem->quantity = $item->qty_received;
                $billItem->rate = $item->price;
                $billItem->amount = $item->amount;
                $billItem->save();
            }

            DB::commit();

            return redirect()->route('bill.show', $bill->id)->with('success', 'Record added successfully');

        } catch (\Exception $th) {
            //throw $th;
            DB::rollBack();
            return redirect()->back()->with('error', 'System error please try again');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $bill = Bill::find($id);
        $supplier = Supplier::find($bill->supplier_id);
        $creditNotes = $bill->creditNote;
        $debitNotes = $bill->debitNote;
        $bankAccounts = BankAccount::all();


        $billTotal = 0;
        $receiptTotal = 0;
        $balance = 0;

        $creditNotes = new Collection();
        $debitNotes = new Collection();

        $billItems = $bill->items;


        foreach ($billItems as $key => $item) {
            $item_total = $item->amount;
            // credit note for item
            $creditNoteItem = BillCreditNoteItem::where('bill_id','=', $bill->id)
            ->where('item_id','=', $item->item_id)->get();

            foreach ($creditNoteItem as $key => $creditNote) {
                $creditNotes->push($creditNote);
                $item_total -= $creditNote->amount;
            }

            // debit note for item
            $debitNoteItem = BillDebitNoteItem::where('bill_id','=', $bill->id)
            ->where('item_id','=', $item->item_id)->get();

            foreach ($debitNoteItem as $key => $debitNote) {
                $debitNotes->push($debitNote);
                $item_total += $debitNote->amount;
            }

            $billTotal += $item_total;
        }

        $receipts = BillReceipt::where('bill_id','=', $bill->id)->get();

        foreach ($receipts as $key => $receipt) {
            $receiptTotal += $receipt->paid_amount;
        }

        $balance = $billTotal - $receiptTotal;


        return view('bills.show', compact('bill', 'creditNotes', 'debitNotes', 'bankAccounts', 'billTotal', 'receiptTotal', 'balance'));
    }
    
}
