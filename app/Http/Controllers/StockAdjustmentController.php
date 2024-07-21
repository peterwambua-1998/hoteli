<?php

namespace App\Http\Controllers;

use App\Models\BarStore;
use App\Models\KitchenStore;
use App\Models\MainStore;
use App\Models\MaintenanceStore;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
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
            'item_id' => 'required',
            'store_id' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $stockAdjustment = new StockAdjustment();
            $stockAdjustment->store_id = $request->store_id;
            $stockAdjustment->item_id = $request->item_id;
            $stockAdjustment->user_id = Auth::user()->id;
            $stockAdjustment->adjustment = $request->quantity;
            $stockAdjustment->save();

            if ($request->store_id == 1) {
                $mainStoreItem = MainStore::where('item_id','=', $request->item_id)->first();
                if ($mainStoreItem) {
                    $mainStoreItem->quantity = $request->quantity;
                    $mainStoreItem->update();
                }
            }

            if ($request->store_id == 2) {
                $mainStoreItem = KitchenStore::where('item_id','=', $request->item_id)->first();
                if ($mainStoreItem) {
                    $mainStoreItem->quantity = $request->quantity;
                    $mainStoreItem->update();
                }
            }

            if ($request->store_id == 3) {
                $mainStoreItem = BarStore::where('item_id','=', $request->item_id)->first();
                if ($mainStoreItem) {
                    $mainStoreItem->quantity = $request->quantity;
                    $mainStoreItem->update();
                }
            }

            if ($request->store_id == 4) {
                $mainStoreItem = MaintenanceStore::where('item_id','=', $request->item_id)->first();
                if ($mainStoreItem) {
                    $mainStoreItem->quantity = $request->quantity;
                    $mainStoreItem->update();
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Record adjusted successfully');
        } catch (\Exception $th) {
            DB::rollBack();
            dd($th->getMessage(), $th->getLine());
            return redirect()->back()->with('error', 'System error please try again');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StockAdjustment $stockAdjustment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockAdjustment $stockAdjustment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockAdjustment $stockAdjustment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockAdjustment $stockAdjustment)
    {
        //
    }
}
