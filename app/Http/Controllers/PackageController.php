<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use App\Models\Package;
use App\Models\PackageFacility;
use App\Models\PackageItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packages = Package::all();
        $boards = MealPlan::all();
        return view('package.index', compact('packages', 'boards'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $facilities = PackageFacility::all();
        return view('package.create', compact('facilities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'package_name' => 'required',
            'facility' => 'required',
            'description' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $package = new Package();
            $package->name = $request->package_name;
            $package->description = $request->description;

            $packagePrice = 0;

            for ($i = 0; $i < count($request->facility); $i++) {
                $packagePrice += $request[$request->facility[$i]];
            }
            $package->price = $packagePrice;
            $package->save();


            for ($i = 0; $i < count($request->facility); $i++) {
                $facility = PackageFacility::where('id', '=', $request->facility[$i])->first();
                if ($facility) {
                    $packageItem = new PackageItems();
                    $packageItem->package_id = $package->id;
                    $packageItem->package_facility_id = $facility->id;
                    $packageItem->price = $request[$request->facility[$i]];
                    $num = $request->facility[$i];
                    $packageItem->main_or_extra_item = $request->get("extra_item_$num");
                    $packageItem->save();
                }
            }
            
            DB::commit();

            return redirect()->route('packages.index')->with('success', 'Package added successfully');
        } catch (\PDOException $th) {
            //throw $th;
            DB::rollBack();

            dd($th->getMessage(), $th->getLine());
            return redirect()->back()->with('error', 'System error please try again!');
        }


       
    }

    /**
     * Display the specified resource.
     */
    public function show(Package $package)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Package $package)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'meal_plan_id' => 'required',
            'name' => 'required',
            'price' => 'required'
        ]);

        $package = Package::find($id);
        if ($request->board_id != 0) {
            $board = MealPlan::find($request->board_id);
            $package->meal_plan_id = $request->board_id;
            $package->price = $request->price + $board->price;
        } else {
            $package->price = $request->price;
        }
        $package->name = $request->name;
        if ($package->update()) {
            return redirect()->back()->with('success', 'Package added successfully');
        }
        return redirect()->back()->with('error', 'System error please try again!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Package $package)
    {
        //
    }
}
