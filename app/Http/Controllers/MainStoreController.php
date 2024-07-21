<?php

namespace App\Http\Controllers;

use App\Models\BarStore;
use App\Models\FrontOfficeStore;
use App\Models\KitchenStore;
use App\Models\MainStore;
use App\Models\MaintenanceStore;
use App\Models\MaterialRequisition;
use App\Models\MaterialRequisitionItem;
use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MainStoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = MainStore::all();
        return view('main-store.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $note)
    {
        for ($i=0; $i < count($request->item_id); $i++) { 
            $storeItemExists = MainStore::where('item_id', '=', $request->item_id[$i])->first();
            if ($storeItemExists) {
                $storeItemExists->quantity = $storeItemExists->quantity + $request->qty_received[$i];
                $storeItemExists->update();
            } else {
                $store = new MainStore();
                $store->good_receive_note_id = $note->id;
                $store->item_id = $request->item_id[$i];
                $store->quantity = $request->qty_received[$i];
                $store->save();
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function adjustStock()
    {
        $mainStoreItems = MainStore::all();
        return view('main-store.adjust', compact('mainStoreItems'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function adjustStockStore(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'reason' => 'required',
        ]);

        DB::beginTransaction();

        try {
            for ($i=0; $i < count($request->item_id); $i++) { 
                $mainStore = MainStore::where('item_id','=', $request->item_id[$i])->first();
                $mainStore->quantity = $request->quantity[$i];
                $mainStore->update();

                // add to adjustment table
                $adjustment = new StockAdjustment();
                $adjustment->store_id = 1;
                $adjustment->item_id = $request->item_id[$i];
                $adjustment->adjustment = $request->quantity[$i];
                $adjustment->reason  = $request->reason[$i];
                $adjustment->user_id = Auth::user()->id;
                $adjustment->save();
            }

            DB::commit();
            return redirect()->route('main.store.index')->with('success','Stock adjusted successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'System error please try again');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MainStore $mainStore)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MainStore $mainStore)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function requisitionPage(MainStore $mainStore)
    {
        $requisitions = MaterialRequisition::orderBy('created_at','desc')->get();
        foreach ($requisitions as $key => $item) {
            if ($item->department_id == 1) {
                $item->department_name = 'Office';
            }
            if ($item->department_id == 2) {
                $item->department_name = 'Maintenance';
            }
            if ($item->department_id == 3) {
                $item->department_name = 'House Keeping';
            }
            if ($item->department_id == 4) {
                $item->department_name = 'Restaurant';
            }
            if ($item->department_id == 5) {
                $item->department_name = 'Bar';
            }
            if ($item->department_id == 6) {
                $item->department_name = 'Kitchen';
            }
        }
        return view('main-store.requisitions', compact('requisitions'));
    }

    /**
     * 
     */
    public function requisitionCreate($id)
    {
        $requisition = MaterialRequisition::find($id);
        if ($requisition->department_id == 1) {
            $requisition->department_name = 'Office';
        }
        if ($requisition->department_id == 2) {
            $requisition->department_name = 'Maintenance';
        }
        if ($requisition->department_id == 3) {
            $requisition->department_name = 'House Keeping';
        }
        if ($requisition->department_id == 4) {
            $requisition->department_name = 'Restaurant';
        }
        if ($requisition->department_id == 5) {
            $requisition->department_name = 'Bar';
        }
        if ($requisition->department_id == 6) {
            $requisition->department_name = 'Kitchen';
        }
        return view('main-store.issue', compact('requisition'));
    }

    /***/
    public function requisitionIssue(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'requisition_id' => 'required'
        ]);

        DB::beginTransaction();
        try {
            //code...
            $requisition = MaterialRequisition::find($request->requisition_id);
            
            $requisition->status = 1;
            $requisition->update();

            $items = $request->item_id;

            // bar store
            if ($requisition->department_id == 5) {
                for ($i = 0; $i < count($items); $i++) {
                    $checkItem = BarStore::where('item_id', '=', $request->item_id[$i])->first();
                    $mainStore = MainStore::where('item_id', '=', $request->item_id[$i])->first();
                    if ($mainStore->quantity >= $request->quantity_issued[$i]) {
                        if ($checkItem) {
                            $checkItem->quantity = $checkItem->quantity + $request->quantity_issued[$i];
                            $checkItem->update();
    
                            // adjust main store
                            $mainStore->quantity = $mainStore->quantity -  $request->quantity_issued[$i];
                            $mainStore->update();
                        } else {
                            $barStore = new BarStore();
                            $barStore->item_id = $request->item_id[$i];
                            $barStore->quantity = $request->quantity_issued[$i];
                            $barStore->save();

                            // adjust main store
                            $mainStore->quantity = $mainStore->quantity -  $request->quantity_issued[$i];
                            $mainStore->update();
                        }
                        $materialRequisitionItem = MaterialRequisitionItem::where('item_id', '=', $items[$i])->where('requisition_id','=', $requisition->id)->first();
                        $materialRequisitionItem->quantity_issued = $request->quantity_issued[$i];
                        $materialRequisitionItem->update();
                    } else {
                        return redirect()->back()->with('error', 'Quantity issued greater than the quantity available in store');
                    }
                   
                }
            }

            // kitchen store
            if ($requisition->department_id == 6) {
                for ($i = 0; $i < count($items); $i++) {
                    $checkItem = KitchenStore::where('item_id', '=', $request->item_id[$i])->first();
                    $mainStore = MainStore::where('item_id', '=', $request->item_id[$i])->first();
                    if ($mainStore->quantity >= $request->quantity_issued[$i]) {
                        if ($checkItem) {
                            $checkItem->quantity = $checkItem->quantity + $request->quantity_issued[$i];
                            $checkItem->update();

                            // adjust main store
                            $mainStore->quantity = $mainStore->quantity -  $request->quantity_issued[$i];
                            $mainStore->update();

                        } else {
                            $barStore = new KitchenStore();
                            $barStore->item_id = $request->item_id[$i];
                            $barStore->quantity = $request->quantity_issued[$i];
                            $barStore->save();

                            // adjust main store
                            $mainStore->quantity = $mainStore->quantity -  $request->quantity_issued[$i];
                            $mainStore->update();

                        }
                        $materialRequisitionItem = MaterialRequisitionItem::where('item_id', '=', $items[$i])->where('requisition_id','=', $requisition->id)->first();
                        $materialRequisitionItem->quantity_issued = $request->quantity_issued[$i];
                        $materialRequisitionItem->update();
                    } else {
                        return redirect()->back()->with('error', 'Quantity issued greater than the quantity available in store');
                    }
                }
            }

            // MaintenanceStore
            if ($requisition->department_id == 2) {
                for ($i = 0; $i < count($items); $i++) {
                    $checkItem = MaintenanceStore::where('item_id', '=', $request->item_id[$i])->first();
                    $mainStore = MainStore::where('item_id', '=', $request->item_id[$i])->first();
                    if ($mainStore->quantity >= $request->quantity_issued[$i]) {
                        
                        if ($checkItem) {
                            $checkItem->quantity = $checkItem->quantity + $request->quantity_issued[$i];
                            $checkItem->update();

                            // adjust main store
                            $mainStore->quantity = $mainStore->quantity -  $request->quantity_issued[$i];
                            $mainStore->update();

                        } else {
                            $barStore = new MaintenanceStore();
                            $barStore->item_id = $request->item_id[$i];
                            $barStore->quantity = $request->quantity_issued[$i];
                            $barStore->save();

                            $mainStore->quantity = $mainStore->quantity -  $request->quantity_issued[$i];
                            $mainStore->update();
                        }
                        $materialRequisitionItem = MaterialRequisitionItem::where('item_id', '=', $items[$i])->where('requisition_id','=', $requisition->id)->first();
                        $materialRequisitionItem->quantity_issued = $request->quantity_issued[$i];
                        $materialRequisitionItem->update();
                    } else {
                        return redirect()->back()->with('error', 'Quantity issued greater than the quantity available in store');
                    }
                }
            }


            // OfficeStroe
            if ($requisition->department_id == 1) {
                for ($i = 0; $i < count($items); $i++) {
                    $checkItem = FrontOfficeStore::where('item_id', '=', $request->item_id[$i])->first();
                    $mainStore = MainStore::where('item_id', '=', $request->item_id[$i])->first();
                    if ($mainStore->quantity >= $request->quantity_issued[$i]) {
                        
                        if ($checkItem) {
                            $checkItem->quantity = $checkItem->quantity + $request->quantity_issued[$i];
                            $checkItem->update();

                            // adjust main store
                            $mainStore->quantity = $mainStore->quantity -  $request->quantity_issued[$i];
                            $mainStore->update();

                        } else {
                            $barStore = new FrontOfficeStore();
                            $barStore->item_id = $request->item_id[$i];
                            $barStore->quantity = $request->quantity_issued[$i];
                            $barStore->save();

                            $mainStore->quantity = $mainStore->quantity -  $request->quantity_issued[$i];
                            $mainStore->update();
                        }
                        $materialRequisitionItem = MaterialRequisitionItem::where('item_id', '=', $items[$i])->where('requisition_id','=', $requisition->id)->first();
                        $materialRequisitionItem->quantity_issued = $request->quantity_issued[$i];
                        $materialRequisitionItem->update();
                    } else {
                        return redirect()->back()->with('error', 'Quantity issued greater than the quantity available in store');
                    }
                }
            }

            DB::commit();

            return redirect()->route('main.store.requisition')->with('success', 'Record saved successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'System error please try again');
        }
    }
}
