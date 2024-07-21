<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\MealPlan;
use App\Models\Reservation;
use App\Models\ReservationDetails;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservations = Reservation::all();
        $mealPlans = MealPlan::all();
        return view('reservations.index', compact('reservations', 'mealPlans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $mealPlans = MealPlan::all();
        $roomTypes = RoomType::all();
        $accounts = Account::where('id', '!=', 1)->orderBy('created_at', 'desc')->get();

        if ($request->type == 1) {
            return view('reservations.createSingle', compact('mealPlans', 'roomTypes', 'accounts'));
        }

        if ($request->type == 2) {
            return view('reservations.createGroup', compact('mealPlans', 'roomTypes', 'accounts'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
        ]);

        if ($request->type == 1) {
            $this->singleReservation($request);
        }

        if ($request->type == 2) {
            $this->orgReservation($request);
        }

        return redirect()->route('b.index')->with('success', 'Reservation successful');
    }

    public function singleReservation(Request $request)
    {

        $reservation = new Reservation();
        $reservation->type = $request->type; //
        $reservation->date_arrival = $request->date_arrival; //
        $reservation->date_departure = $request->date_departure; //
        $reservation->meal_plan = $request->meal_plan; //
        $reservation->num_of_pax = 1; // 
        $reservation->extra_info = $request->extra_info;//
        $reservation->num_of_vehicles = $request->num_of_vehicles; //
        if ($request->account_id != 0) {
            $reservation->account_id = $request->account_id; //
        }

        $reservation->surname = $request->surname;
        $reservation->other_names = $request->other_names;
        $reservation->profession = $request->profession;
        $reservation->id_number = $request->id_number;
        $reservation->single_email = $request->single_email;
        $reservation->id_url = $request->id_url;
        $reservation->telephone = $request->telephone;
        $reservation->location = $request->location;

        $reservation->save();

        return redirect()->route('b.index')->with('success', 'Reservation successful');
    }


    public function orgReservation(Request $request)
    {

        $request->validate([
            'type' => 'required',
        ]);

        DB::beginTransaction();

        try {
            //code...
            $reservation = new Reservation();
            $reservation->type = $request->type; //
            $reservation->date_arrival = $request->date_arrival; //
            $reservation->date_departure = $request->date_departure; //
            $reservation->meal_plan = $request->meal_plan; // 
            $reservation->num_of_pax = count($request->org_member_name); // 
            $reservation->extra_info = $request->extra_info; //
            $reservation->num_of_vehicles = $request->num_of_vehicles; //
            $reservation->account_id = $request->account_id; //

            $reservation->org_name = $request->org_name;
            $reservation->org_email = $request->org_email;
            $reservation->telephone = $request->telephone;
            $reservation->location = $request->location;

            $reservation->save();

            for ($i = 0; $i < count($request->org_member_name); $i++) {
                $details = new ReservationDetails();
                $details->org_member_name = $request->org_member_name[$i];
                $details->room_type = $request->room_type[$i];
                $details->save();
            }

            $reservation->save();

            DB::commit();

            return redirect()->route('b.index')->with('success', 'Record stored successfully');
        } catch (\PDOException $th) {
            DB::rollBack();

            return redirect()->back()->with('error', 'System error please try again!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function adjustBooking(Request $request)
    {
        $reservation = Reservation::find($request->reservation_id);
        $reservation->change_status_to = $request->change_status_to;
        $reservation->reason_for_change = $request->reason_for_change;
        $reservation->amend_status = 2;
        if ($request->change_status_to == 3) {
            $reservation->amended_board = $request->board_id;
        } else {
            $reservation->amended_board = null;
        }
        if ($reservation->update()) {
            return redirect()->back()->with('success', 'Amendment sent for approval!');
        }
        return redirect()->back()->with('error', 'System error please try again!');
    }

    public function approveAmendment(Request $request)
    {
        // 0 reject 1 approve
        if ($request->amend_type == 1) {
            $reservation = Reservation::find($request->reservation_id);
            $reservation->status = $reservation->change_status_to;
            $reservation->amend_status = 1;
            if ($reservation->change_status_to == 3) {
                $reservation->meal_plan = $reservation->amended_board;
            }
            if ($reservation->update()) {
                return redirect()->back()->with('success', 'Reservation amended successfully!');
            }
            return redirect()->back()->with('error', 'System error please try again!');
        } 

        if ($request->amend_type == 0) {
            $reservation = Reservation::find($request->reservation_id);
            $reservation->reason_for_rejection = $request->reason_for_rejection;
            $reservation->amend_status = 0;
            if ($reservation->update()) {
                return redirect()->back()->with('success', 'Amendment sent for approval!');
            }
            return redirect()->back()->with('error', 'System error please try again!');
        }
    }
}
