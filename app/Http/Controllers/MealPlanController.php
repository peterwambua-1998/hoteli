<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use Illuminate\Http\Request;

class MealPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = MealPlan::all();
        return view('meal-plans.index', compact('plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required'
        ]);

        $mealPlan = new MealPlan();
        $mealPlan->name = $request->name;
        $mealPlan->price = $request->price;
        if ($mealPlan->save()) {
            return redirect()->route('meal-plan.index')->with('success', 'Record added successfully');
        }
        return redirect()->back()->with('error', 'System error, please try again!');
    }

    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required'
        ]);

        $mealPlan =  MealPlan::find($id);
        $mealPlan->name = $request->name;
        $mealPlan->price = $request->price;
        if ($mealPlan->update()) {
            return redirect()->route('meal-plan.index')->with('success', 'Record updated successfully');
        }
        return redirect()->back()->with('error', 'System error, please try again!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MealPlan $mealPlan)
    {
        //
    }
}
