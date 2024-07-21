<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchaseOrder = PurchaseOrder::orderBy('created_at','desc')->get();
        return view('purchase-orders.index', compact('purchaseOrder'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        return view('purchase-orders.create', compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'date_issue' => 'required',
            'sub_total' => 'required',
            'vat' => 'required',
            'total' => 'required',
        ]);

        if ($request->supplier_id == 0) {
            return redirect()->back()->with('error', 'Select supplier');
        }

        DB::beginTransaction();

        try {
            $purchaseOrder = new PurchaseOrder();
            $purchaseOrder->supplier_id = $request->supplier_id;
            $purchaseOrder->po_number = $request->po_number;
            $purchaseOrder->date_issue = $request->date_issue;
            $purchaseOrder->sub_total = $request->sub_total;
            $purchaseOrder->vat = $request->vat;
            $purchaseOrder->total = $request->total;
            $purchaseOrder->note = $request->note;
            $purchaseOrder->save();

            for ($i=0; $i < count($request->item_id); $i++) { 
                $product = Product::find($request->item_id[$i]);

                $item = new PurchaseOrderItem();
                $item->purchase_order_id = $purchaseOrder->id;
                $item->item_id = $request->item_id[$i];
                $item->item_description = $product->description;
                $item->quantity = $request->quantity[$i];
                $item->unit_price = $request->rate[$i];
                $item->amount = $request->amount[$i];
                $item->save();
            }

            DB::commit();
            return redirect()->route('purchase.order.show', $purchaseOrder->id)->with('success','Recorded added successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error','System error, please try again!');

        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $purchaseOrder  = PurchaseOrder::find($id);
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * reject
     */
    public function reject(Request $request)
    {
        $request->validate([
            'purchase_order_id' => 'required'
        ]);

        $purchaseOrder = PurchaseOrder::find($request->purchase_order_id);
        $purchaseOrder->status = 2;
        $purchaseOrder->reason = $request->reason;
        if ($purchaseOrder->update()) {
            return redirect()->back()->with('success', 'Purchase order rejected!');
        }
        return redirect()->back()->with('error', 'System error, please try again!');
    }
}
