<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::all();
        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'telephone' => 'required',
            'contact_person' => 'required',
            'email' => 'required',
        ]);

        $supplier = new Supplier();
        $supplier->name = $request->name;
        $supplier->address = $request->address;
        $supplier->telephone = $request->telephone;
        $supplier->contact_person = $request->contact_person;
        $supplier->email = $request->email;
        if ($supplier->save()) {
            return redirect()->back()->with('success','Record added successfully');
        }
        return redirect()->back()->with('error','System error, please tr again!');
    }

    /**
     * Display the specified resource.
     */
    public function show( $id)
    {
        $supplier = Supplier::find($id);
        // purchase order
        $purchaseOrder = $supplier->purchaseOrder;
        // grn
        $grns = $supplier->goodsReceivedNote;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'telephone' => 'required',
            'contact_person' => 'required',
            'email' => 'required',
        ]);

        $supplier = Supplier::find($id);
        $supplier->name = $request->name;
        $supplier->address = $request->address;
        $supplier->telephone = $request->telephone;
        $supplier->contact_person = $request->contact_person;
        $supplier->email = $request->email;
        if ($supplier->save()) {
            return redirect()->back()->with('success','Record updated successfully');
        }
        return redirect()->back()->with('error','System error, please tr again!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        //
    }
}
