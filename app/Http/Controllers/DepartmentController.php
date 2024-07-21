<?php

namespace App\Http\Controllers;

use App\Models\BarStore;
use App\Models\Department;
use App\Models\MaterialRequisition;
use App\Models\MaterialRequisitionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::all();
        return view('departments.index', compact('departments'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $department = new Department();
        $department->name = $request->name;
        if ($department->save()) {
            return redirect()->back()->with('success', 'Record created successfully');
        }
        return redirect()->back()->with('error', 'System error please try again');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $department = Department::find($id);
        $department->name = $request->name;
        if ($department->update()) {
            return redirect()->back()->with('success', 'Record updated successfully');
        }
        return redirect()->back()->with('error', 'System error please try again');
    }

    /**
     * bar requisition index
     */
    public function barRequisitionView()
    {
        $requisitions = MaterialRequisition::where('department_id','=', 5)->get();
        return view('departments.bar.index', compact('requisitions'));
    }

    /**
     * bar requisition create
     */
    public function barRequisitionCreate()
    {
        return view('departments.bar.requisition');
    }

    /**
     * kitchen requisition index
     */
    public function kitchenRequisitionView()
    {
        $requisitions = MaterialRequisition::where('department_id','=', 6)->get();
        return view('departments.kitchen.index', compact('requisitions'));
    }

    /**
     * kitchen requisition create
     */
    public function kitchenRequisitionCreate()
    {
        return view('departments.kitchen.requisition');
    }

    
    /**
     * kitchen requisition index
     */
    public function officeRequisitionView()
    {
        $requisitions = MaterialRequisition::where('department_id','=', 1)->get();
        return view('departments.office.index', compact('requisitions'));
    }

    /**
     * kitchen requisition create
     */
    public function officeRequisitionCreate()
    {
        return view('departments.office.requisition');
    }

     /**
     * kitchen requisition index
     */
    public function houseRequisitionView()
    {
        $requisitions = MaterialRequisition::where('department_id','=', 3)->get();
        return view('departments.house.index', compact('requisitions'));
    }

    /**
     * kitchen requisition create
     */
    public function houseRequisitionCreate()
    {
        return view('departments.house.requisition');
    }

     /**
     * kitchen requisition index
     */
    public function maintenanceRequisitionView()
    {
        $requisitions = MaterialRequisition::where('department_id','=', 2)->get();
        return view('departments.maintenance.index', compact('requisitions'));
    }

    /**
     * kitchen requisition create
     */
    public function maintenanceRequisitionCreate()
    {
        return view('departments.maintenance.requisition');
    }


    /**
     * store requisition
     */
    public function requisitionStore(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'quantity' => 'required',
        ]);

        DB::beginTransaction();
        try {

            $requisition = new MaterialRequisition();
            $requisition->department_id = $request->department_id;
            $requisition->user_id = Auth::user()->id;
            $requisition->store_id = $request->store_id;
            $requisition->save();

            for ($i=0; $i < count($request->item_id); $i++) { 
                $item = new MaterialRequisitionItem();
                $item->requisition_id = $requisition->id;
                $item->item_id = $request->item_id[$i];
                $item->quantity = $request->quantity[$i];
                $item->save();
            }

            DB::commit();

            if ($request->department_id == 1) {
                return redirect()->route('office.requisition.view')->with('success', 'Requisition record sent');
            }

            if ($request->department_id == 2) {
                return redirect()->route('maintenance.requisition.view')->with('success', 'Requisition record sent');
            }

            if ($request->department_id == 3) {
                return redirect()->route('house.requisition.view')->with('success', 'Requisition record sent');
            }

            if ($request->department_id == 5) {
                return redirect()->route('bar.requisition.view')->with('success', 'Requisition record sent');
            }

            if ($request->department_id == 6) {
                return redirect()->route('kitchen.requisition.view')->with('success', 'Requisition record sent');
            }
            
        } catch (\Exception $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'System error please try again');
        }
    }
}
