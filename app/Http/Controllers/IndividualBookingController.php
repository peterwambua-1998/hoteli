<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Booking;
use App\Models\Day;
use App\Models\Package;
use Illuminate\Http\Request;

class IndividualBookingController extends Controller
{
    public function booking(Request $request)
    {   
        $day = Day::where('status', '=', 1)->first();
        if (!$day) {
            return redirect()->back()->with('error', 'Please add day!');
        }

        $package = Package::find($request->package_id);

        // create individual account
        if ($request->existing_account_id == 0) {
            $account = new Account();
            $account->type = 2;
            $account->name = "$request->surname $request->other_names";
            $account->email = $request->email;
            $account->telephone = $request->telephone;
            $account->location = $request->location;
            $account->profession = $request->profession;
            $account->id_number = $request->id_number;
            $account->save();
        } else {
            $account = Account::find($request->existing_account_id);
        }
        

        if ($request->acc_paid_by == 1) {
            $booking = new Booking();
            $booking->type = $request->type; //
            $booking->bill_options = $request->bill_options; //
            $booking->acc_paid_by = $request->acc_paid_by; //
            $booking->account_id = $account->id; //
            $booking->package_id = $package->id;
            // personal details
            $booking->surname = $request->surname;
            $booking->other_names = $request->other_names;
            $booking->profession = $request->profession;
            $booking->id_number = $request->id_number;
            $booking->email = $request->email;
            $booking->telephone = $request->telephone;

            // more details
            $booking->extras_paid_by = $request->extras_paid_by;
            $booking->check_in = $request->check_in;
            $booking->check_out = $request->check_out;
            $booking->pax = $request->pax;
            $booking->bill_interval = $request->bill_interval;
            $booking->extra_details = $request->extra_details;
            $booking->num_of_vehicles = $request->num_of_vehicles;
            $booking->company_id = $request->company_id;

            $booking->underage_child = $request->underage_child;
            $booking->different_room = $request->different_room;
            $booking->num_of_underage = $request->num_of_underage;

            // down payment
            $booking->down_payment = 0;
            $booking->day_id = $day->id;
            $booking->save();
        }
    }
}
