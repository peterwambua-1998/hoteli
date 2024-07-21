<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Day;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\MealPlan;
use App\Models\Package;
use App\Models\PackageFacility;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingsController extends Controller
{

    public function createBooking()
    {
        $accounts = Account::where('id', '!=', 1)->get();
        $reservations = Reservation::all();
        $mealPlans = MealPlan::all();
        $rooms = Room::where('room_status', '=', 0)->get();
        $packages = Package::all();
        return view('bookings.create', compact('accounts', 'reservations', 'mealPlans', 'rooms', 'packages'));
    }

    public function createBookingCompany()
    {
        $accounts = Account::where('id', '!=', 1)->where('type', '!=', 2)->get();
        $reservations = Reservation::all();
        $mealPlans = MealPlan::all();
        $rooms = Room::where('room_status', '=', 0)->get();
        $packages = Package::all();
        return view('bookings.createCompany', compact('accounts', 'reservations', 'mealPlans', 'rooms', 'packages'));
    }

    /**
     * company booking express
     */
    public function saveBookingCompany(Request $request)
    {

        $request->validate([
            'type' => 'required'
        ]);

        if ($request->account_id == 0) {
            return redirect()->back()->with('error', 'Kindly select account');
        }

        // express checkin
        if ($request->type == 1) {
            return $this->expressCheckinCompany($request);
        }
    }

    public function expressCheckinCompany(Request $request)
    {
        DB::beginTransaction();
        try {
            // day
            $day = Day::where('status', '=', 1)->first();
            if (!$day) {
                return redirect()->back()->with('error', 'Please add day!');
            }

            if ($request->package_id == 0) {
                return redirect()->back()->with('error', 'Please add package');
            }

            if ($request->package_id == 0 || !$request->package_id) {
                return redirect()->back()->with('error', 'Please add package');
            }

            $package = Package::find($request->package_id);

            // account  
            $account = Account::find($request->account_id);

            $booking = new Booking();
            $booking->type = $request->type; //
            $booking->bill_options = 2; //
            $booking->acc_paid_by = 2; //
            $booking->account_id = $account->id; //
            $booking->company_id = $account->id; //
            $booking->package_id = $package->id;

            // personal details
            $booking->surname = $account->name;
            $booking->other_names = $account->name;
            $booking->profession = 'n/a';
            $booking->id_number = $account->vat_registration_number;
            $booking->email = $account->email;
            $booking->telephone = $account->telephone;

            // more details
            $booking->check_in = $request->check_in;
            $booking->check_out = $request->check_out;
            $booking->pax = $request->pax;
            $booking->extra_details = $request->extra_details;
            $booking->num_of_vehicles = $request->num_of_vehicles;
            $booking->extras_paid_by = $request->extras_paid_by;

            // down payment
            $booking->down_payment = 0;
            $booking->day_id = $day->id;
            $booking->save();


           

            $invoice_t = $this->calculateTax($package->price * $request->num_of_days * $request->pax);

            // create invoice here
            $inv = new Invoice();
            $inv->account_id = $account->id;
            $inv->booking_id = $booking->id;
            $inv->inv_number = rand(1, 1000000);
            $inv->delivery_date = date('Y-m-d');
            $inv->tax_date = date('Y-m-d');
            $inv->to_date = $request->check_in;
            $inv->from_date = $request->check_out;
            $inv->invoiced_to = $account->id;
            $inv->sub_total = $invoice_t['subTotal'];
            $inv->tax_amount = $invoice_t['vat'];
            $inv->levy = $invoice_t['levy'];
            $inv->total = $invoice_t['total'];
            $inv->pos_used = 2;
            $inv->user_id = Auth::user()->id;
            $inv->description =  $package->description;
            $inv->day_id = $day->id;
            $inv->save();

            $invoice_id = $inv->id;

            $inv_item = new InvoiceItem();
            $inv_item->invoice_id = $invoice_id;
            $inv_item->item_id = $package->id;
            $inv_item->item_code = $package->id;
            $inv_item->item_description = "$package->description";
            $inv_item->quantity = $request->pax;
            $inv_item->rate = $package->price;
            $inv_item->days = $request->num_of_days;
            $inv_item->amount = $invoice_t['total'];
            $inv_item->save();

            DB::commit();

            return redirect()->route('reservations.index')->with('success', 'Checkin successful!');
        } catch (\PDOException $th) {
            dd($th->getMessage(), $th->getLine());
            DB::rollBack();
            return redirect()->back()->with('error', 'System error please try again!');
        }
    }

    /**
     * individual booking express
     */
    public function saveBooking(Request $request)
    {

        $request->validate([
            'type' => 'required'
        ]);

        // express checkin
        if ($request->type == 1) {
            return $this->expressCheckin($request);
        }
    }

    public function expressCheckin(Request $request)
    {
        DB::beginTransaction();

        try {
            if ($request->package_id == 0) {
                return redirect()->back()->with('error', 'Kindly choose package');
            }
            // day
            $day = Day::where('status', '=', 1)->first();
            if (!$day) {
                return redirect()->back()->with('error', 'Please add day!');
            }
            if ($request->package_id == 0 || !$request->package_id) {
                return redirect()->back()->with('error', 'Please add package');
            }

            $hooking = Booking::where('company_id', '=', $request->company_id)->where('status', '=', 1)->first();

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


            $booking = new Booking();
            $booking->type = $request->type; //
            $booking->bill_options = $request->bill_options; //
            $booking->acc_paid_by = $request->acc_paid_by; //
            $booking->account_id = $account->id; //
            if ($request->acc_paid_by == 2) {
                $booking->company_id = $request->company_id; //
            }
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
            /**
             * under 8 years in same room take 50% of bill and add to accommodation
             * 
             */
            // TODO ask if we add swimming to under 8 years
            $accountTotal = 0;
            $companyTotal = 0;
            $childTotal = 0;

            if ($request->underage_child == 1 && $request->different_room == 0) {
                // here we half package price
                $childTotal = $package->price * (50/100) * $request->num_of_underage;
            }

            if ($request->underage_child == 1 && $request->different_room == 1) {
                // here we half package price
                $childTotal = $package->price * (75/100) * $request->num_of_underage;
            }

            if ($request->acc_paid_by == 1) {
                $accountTotal += ($package->price + $childTotal) * $request->number_of_days;
            }

            if ($request->acc_paid_by == 2) {
                $companyTotal += ($package->price + $childTotal) * $request->number_of_days;
            }
            // room details
            

            $accountTotalTax = $this->calculateTax($accountTotal);
            $companyTotalTax = $this->calculateTax($companyTotal);
            // create invoice here
            // company pays main bills
            if ($request->acc_paid_by == 1) {
                $inv = new Invoice();
                $inv->account_id = $account->id;
                $inv->booking_id = $booking->id;
                $inv->inv_number = rand(1, 1000000);
                $inv->delivery_date = date('Y-m-d');
                $inv->tax_date = date('Y-m-d');
                $inv->to_date = $request->check_in;
                $inv->from_date = $request->check_out;
                $inv->invoiced_to = $account->id;
                $inv->sub_total = $accountTotalTax['subTotal'];
                $inv->tax_amount = $accountTotalTax['vat'];
                $inv->levy = $accountTotalTax['levy'];
                $inv->total = $accountTotalTax['total'];
                $inv->pos_used = 2;
                $inv->user_id = Auth::user()->id;
                $inv->description =  $package->description;
                $inv->day_id = $day->id;
                $inv->save();

                $inv_item = new InvoiceItem();
                $inv_item->invoice_id = $inv->id;
                $inv_item->item_id = $package->id;
                $inv_item->item_code = $package->id;
                $inv_item->item_description = "$package->name";
                $inv_item->quantity = 1;
                $inv_item->rate = $package->price + $childTotal;
                $inv_item->days = $request->number_of_days;
                $inv_item->amount = $accountTotal;
                $inv_item->save();
                
            }

            if ($request->acc_paid_by == 2) {
                // check if there is active booking with company_id similar to request->company_id
                // then find the invoice used for that booking then add items to it 
                $invC = '';
                if ($hooking) {
                    // look fo invoice with that account
                    $invC = Invoice::where('account_id', '=', $request->company_id)->where('booking_id','=', $hooking->id)->get()->last();
                    $invC->inv_number = rand(1, 1000000);
                    $invC->delivery_date = date('Y-m-d');
                    $invC->tax_date = date('Y-m-d');
                    $invC->to_date = $request->check_in;
                    $invC->from_date = $request->check_out;
                    $invC->invoiced_to = $account->id;
                    $invC->pos_used = 2;
                    $invC->user_id = Auth::user()->id;
                    $invC->description =  $package->description;
                    $invC->day_id = $day->id;
                    $qty = 0;
                    $amt = 0;
                    foreach ($invC->items as $key => $item) {
                        $qty += $item->quantity;
                        $amt += $item->amount;
                    }

                    $amt += $companyTotal;

                    $newTax = $this->calculateTax($amt);

                    $invC->sub_total = $newTax['subTotal'];
                    $invC->tax_amount = $newTax['vat'];
                    $invC->levy = $newTax['levy'];
                    $invC->total = $newTax['total'];
                    $invC->update();

                    $inv_item = new InvoiceItem();
                    $inv_item->invoice_id = $invC->id;
                    $inv_item->item_id = $package->id;
                    $inv_item->item_code = $package->id;
                    $inv_item->item_description = "$package->name";
                    $inv_item->quantity = 1;
                    $inv_item->rate = $package->price + $childTotal;
                    $inv_item->days = $request->number_of_days;
                    $inv_item->amount = ($package->price + $childTotal) * $request->number_of_days;
                    $inv_item->save();

                } else {
                    $invC = new Invoice();
                    $invC->account_id = $request->company_id;
                    $invC->booking_id = $booking->id;
                    $invC->inv_number = rand(1, 1000000);
                    $invC->delivery_date = date('Y-m-d');
                    $invC->tax_date = date('Y-m-d');
                    $invC->to_date = $request->check_in;
                    $invC->from_date = $request->check_out;
                    $invC->invoiced_to = $account->id;
                    $invC->sub_total = $companyTotalTax['subTotal'];
                    $invC->tax_amount = $companyTotalTax['vat'];
                    $invC->levy = $companyTotalTax['levy'];
                    $invC->total = $companyTotalTax['total'];
                    $invC->pos_used = 2;
                    $invC->user_id = Auth::user()->id;
                    $invC->description =  $package->description;
                    $invC->day_id = $day->id;
                    $invC->save();

                    $inv_item = new InvoiceItem();
                    $inv_item->invoice_id = $invC->id;
                    $inv_item->item_id = $package->id;
                    $inv_item->item_code = $package->id;
                    $inv_item->item_description = "$package->name";
                    $inv_item->quantity = 1;
                    $inv_item->rate = $package->price + $childTotal;
                    $inv_item->days = $request->number_of_days;
                    $inv_item->amount = $companyTotal;
                    $inv_item->save();
                }
            }

            // update room and add rooms to booking
            for ($i=0; $i < count($request->room_id); $i++) { 
                $room = Room::find($request->room_id[$i]);
                $room->room_status = 1;
                $room->update();

                $bookingDetails = new BookingDetail();
                $bookingDetails->booking_id = $booking->id;
                $bookingDetails->room_id = $request->room_id[$i];
                $bookingDetails->save();
            }
           

            DB::commit();

            return redirect()->route('reservations.index')->with('success', 'Checkin successful!');
        } catch (\PDOException $th) {
            dd($th->getMessage(), $th->getLine());
            DB::rollBack();
            return redirect()->back()->with('error', 'System error please try again!');
        }
    }


    /**
     * calculate tax
     */
    public function calculateTax($totalAmount)
    {
        $subTotal = $totalAmount;
        $total = $totalAmount;

        $subTotal = round($subTotal / 1.16, 2);
        $vat = round($total - $subTotal, 2);
        $levy = round(0.02 * $subTotal, 2);
        $total_amt = $subTotal + $vat;
        return [
            'subTotal' => $subTotal,
            'vat' => $vat,
            'levy' => $levy,
            'total' => $total_amt,
        ];
    }

    public function accommodationInvoice(Request $request, $account, $accTotal, $package, $invoice_id, $accRate)
    {

        $accommodationTotal = $this->calculateTax($accTotal);
        $room = Room::find($request->room_id);

        $inv_item = new InvoiceItem();
        $inv_item->invoice_id = $invoice_id;
        $inv_item->item_id = $room->number;
        $inv_item->item_code = $room->number;
        $inv_item->item_description = "Accommodation room number: $room->description ($room->number)";
        $inv_item->quantity = $request->pax;
        $inv_item->rate = $accRate;
        $inv_item->days = $request->number_of_days;
        $inv_item->amount = $accommodationTotal['total'];
        $inv_item->save();
    }

    public function mealsInvoice(Request $request, $account, $mealTotal, $invoice_id)
    {
        // TODO remember 
        // create invoice for meal

        $mealPlan = MealPlan::find($request->meal_plan);

        $mealTotal = $this->calculateTax($mealTotal);

        $inv_items = new InvoiceItem();
        $inv_items->invoice_id = $invoice_id;
        $inv_items->item_id = $mealPlan->id;
        $inv_items->item_code = rand(0, 100000);
        $inv_items->item_description = "Meal plan: $mealPlan->name";
        $inv_items->quantity = $request->pax;
        $inv_items->rate = $mealPlan->price;
        $inv_items->days = $request->number_of_days;
        $inv_items->amount = $mealTotal['total'];
        $inv_items->save();
    }

    public function swimmingInvoice(Request $request, $account, $invoice_id)
    {

        $item = Product::find(1);
        // swimming 
        // create invoice for swimming
        $swimmingTotal = $this->calculateTax($item->price * $request->pax);


        $inv_items = new InvoiceItem();
        $inv_items->invoice_id = $invoice_id;
        $inv_items->item_id = $item->id;
        $inv_items->item_code = $item->code;
        $inv_items->item_description = $item->description;
        $inv_items->quantity = $request->pax;
        $inv_items->rate = $item->price;
        $inv_items->days = $request->number_of_days;
        $inv_items->amount = $swimmingTotal['total'];
        $inv_items->save();
        DB::commit();
    }

    public function underAgeInvoice(Request $request, $childPrice, $account, $invoice_id)
    {
        $underAgeToTal = $this->calculateTax($childPrice);


        $inv_items = new InvoiceItem();
        $inv_items->invoice_id = $invoice_id;
        $inv_items->item_code = 'n/a';
        $inv_items->item_description = 'Under 8 years same room as parent';
        $inv_items->quantity = $request->num_of_underage;
        $inv_items->rate = $underAgeToTal['total'];
        $inv_items->days = $request->number_of_days;
        $inv_items->amount = $underAgeToTal['total'];
        $inv_items->save();
        DB::commit();
    }



    public function addBooking()
    {
    }
}
