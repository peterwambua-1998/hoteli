<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Invoice;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class DayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $day = Day::where('status','=',1)->orderBy('created_at')->first();
        
        return view('days.index', compact('day'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'start_time' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $activeDay = Day::where('status','=',1)->orderBy('created_at')->first();
            if ($activeDay) {
                $activeDay->status = 0;
                $activeDay->update();
            }

            $day = new Day();
            $day->start_time = $request->start_time;
            $day->started_by = Auth::user()->id;
            $day->save();

            DB::commit();

            return redirect()->back()->with('success', 'Day has started');

        } catch (\PDOException $th) {
            //throw $th;
            DB::rollBack();

            return redirect()->back()->with('error', 'System error please try again!');
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function endDay(Request $request)
    {
        $request->validate([
            'end_time' => 'required',
            'cash_collected' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $day = Day::find($request->day_id);
            $day->end_time = $request->end_time;
            $day->cash_collected = $request->cash_collected;
            $day->status = 0;
            $day->ended_by = Auth::user()->id;
            $day->update();

            $day = new Day();
            $day->start_time = $request->end_time;
            $day->started_by = Auth::user()->id;
            $day->save();

            DB::commit();

            return redirect()->back()->with('success','Day ended successfully!');
        } catch (\PDOException $th) {
            DB::rollBack();
            dd($th->getMessage());
            return redirect()->back()->with('error','System error please try again!');

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function systemCashCollected()
    {
        // day
        $days = Day::where('status', '!=', 1)->orderBy('created_at', 'desc')->get();

        foreach ($days as $key => $day) {
            $invoices = Invoice::whereIn('pos_used', [1, 3, 4])->where('voided', '=', 0)->where('day_id','=', $day->id)->get();
            $cash = 0;
            foreach ($invoices as $key => $invoice) {
                $receipts = $invoice->receipt;
                foreach ($receipts as $key => $receipt) {
                    if ($receipt->payment_method == 1) {
                        $cash += $receipt->paid_amount;
                    }
                }
                
            }
            $day->system_cash = $cash;
        }

        return view('days.compare', compact('days'));
    }
}
