<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceStore;
use App\Models\Product;
use Illuminate\Http\Request;

class MaintenanceStoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = MaintenanceStore::all();
        return view('maintenance-store.index', compact('items'));
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
    public function store(Request $request, $note)
    {
        for ($i=0; $i < count($request->item_id); $i++) { 
            $storeItemExists = MaintenanceStore::where('item_id', '=', $request->item_id[$i])->first();
            if ($storeItemExists) {
                $storeItemExists->quantity = $storeItemExists->quantity + $request->qty_received[$i];
                $storeItemExists->update();
            } else {
                $store = new MaintenanceStore();
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
    public function show(MaintenanceStore $maintenanceStore)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaintenanceStore $maintenanceStore)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaintenanceStore $maintenanceStore)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaintenanceStore $maintenanceStore)
    {
        //
    }
}
