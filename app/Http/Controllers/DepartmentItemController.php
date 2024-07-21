<?php

namespace App\Http\Controllers;

use App\Models\DepartmentItem;
use Illuminate\Http\Request;

class DepartmentItemController extends Controller
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
            'department_id' => 'required',
            'name' => 'required',
            'price' => 'required'
        ]);

        $item = new DepartmentItem();
        $item->department_id = $request->department_id;
        $item->name = $request->name;
        $item->price = $request->price;
        if ($item->save()) {
            return redirect()->back()->with('success', 'Record created successfully');
        }
        return redirect()->back()->with('error', 'System error please try again');
    }

    /**
     * Display the specified resource.
     */
    public function show(DepartmentItem $departmentItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DepartmentItem $departmentItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DepartmentItem $departmentItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DepartmentItem $departmentItem)
    {
        //
    }
}
