<?php

namespace App\Http\Controllers;

use App\Models\GoodReceiveNote;
use App\Models\GoodReceiveNoteItem;
use App\Models\MainStore;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoodReceiveNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $note = GoodReceiveNote::orderBy('created_at', 'desc')->get();
        return view('goods-received.index', compact('note'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($purchase_order_id)
    {
        $purchaseOrder = PurchaseOrder::find($purchase_order_id);
        return view('goods-received.create', compact('purchaseOrder'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'grn_number' => 'required',
            'purchase_order_id' => 'required',
            'date_issue' => 'required',
            'total' => 'required',
        ]);

        DB::beginTransaction();

        try {
            // update purchase order status
            $purchaseOrder = PurchaseOrder::find($request->purchase_order_id);
            $purchaseOrder->status = 1;
            $purchaseOrder->update();

            $note = new GoodReceiveNote();
            $note->purchase_order_id = $request->purchase_order_id;
            $note->supplier_id = $purchaseOrder->supplier_id;
            $note->grn_number = $request->grn_number;
            $note->sub_total = $request->sub_total;
            $note->vat = $request->vat;
            $note->total = $request->total;
            $note->received_by = Auth::user()->id;
            $note->note = $request->note;
            $note->save();

            for ($i = 0; $i < count($request->item_id); $i++) {
                $item = new GoodReceiveNoteItem();
                $item->item_id = $request->item_id[$i];
                $item->note_id = $note->id;
                $item->qty_received = $request->qty_received[$i];
                $item->price = $request->price[$i];
                $item->amount = $request->amount[$i];
                $item->save();
            }

            // add to main store
            // first check if record exists
            if ($request->send_to == 1) {
                $mainStoreController = new MainStoreController();
                $mainStoreController->store($request, $note);
            }
            // kitchen store
            if ($request->send_to == 2) {
                $kitchenController = new KitchenStoreController();
                $kitchenController->store($request, $note);
            }
            // bar store
            if ($request->send_to == 3) {
                $barStoreController = new BarStoreController();
                $barStoreController->store($request, $note);
            }

            // maintenance store
            if ($request->send_to == 4) {
                $maintenance = new MaintenanceStoreController();
                $maintenance->store($request, $note);
            }
            DB::commit();
            return redirect()->route('goods.receive.index')->with('success', 'Record added successfully');
        } catch (\Exception $th) {
            //throw $th;
            DB::rollBack();

            return redirect()->back()->with('error', 'System error, please try again!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $note = GoodReceiveNote::find($id);
        return view('goods-received.show', compact('note'));
    }

    /**
     * GRN without purchase order view
     */
    public function grnWithoutPOView()
    {
        $suppliers = Supplier::all();
        return view('goods-received.without-po', compact('suppliers'));
    } 

    /**
     * GRN without purchase order view
     */
    public function grnWithoutPOStore(Request $request)
    {
        $request->validate([
            'grn_number' => 'required',
        ]);

        // TODO Ensure you add to which store goods go to
        DB::beginTransaction();
        try {
            $note = new GoodReceiveNote();
            $note->purchase_order_id = $request->purchase_order_id;
            $note->supplier_id = $request->supplier_id;
            $note->grn_number = $request->grn_number;
            $note->sub_total = $request->sub_total;
            $note->vat = $request->tax_amount;
            $note->total = $request->total;
            $note->received_by = Auth::user()->id;
            $note->note = $request->note;
            $note->save();

            for ($i=0; $i < count($request->item_id); $i++) { 
                $item = new GoodReceiveNoteItem();
                $item->item_id = $request->item_id[$i];
                $item->note_id = $note->id;
                $item->qty_received = $request->qty_received[$i];
                $item->price = $request->price[$i];
                $item->amount = $request->amount[$i];
                $item->save();
            }

            // add to main store
            // first check if record exists
            if ($request->send_to == 1) {
                $mainStoreController = new MainStoreController();
                $mainStoreController->store($request, $note);
            }
            // kitchen store
            if ($request->send_to == 2) {
                $kitchenController = new KitchenStoreController();
                $kitchenController->store($request, $note);
            }
            // bar store
            if ($request->send_to == 3) {
                $barStoreController = new BarStoreController();
                $barStoreController->store($request, $note);
            }

            // maintenance store
            if ($request->send_to == 4) {
                $maintenance = new MaintenanceStoreController();
                $maintenance->store($request, $note);
            }
            DB::commit();

            return redirect()->route('goods.receive.index')->with('success', 'Record stored successfully');
            
        } catch (\Exception $th) {
            //throw $th
            DB::rollBack();
            dd($th->getMessage(), $th->getLine());
            return redirect()->back()->with('error', 'System error please try again');
        }
    } 
}
