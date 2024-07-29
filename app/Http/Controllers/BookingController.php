<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\MealPlan;
use App\Models\Product;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Account::all();
        foreach ($captain as $key => $booking) {
            // details
            $booking['rooms'] = '';
            $details = BookingDetail::where('booking_id', '=', $booking->id)->get();
            foreach ($details as $key => $detail) {
                $booking['rooms'] .= " {$detail->room->description} {$detail->room->number} ";
            }
        }
        return view('bookings.index', compact('captain', 'customers',));
    }

   
    /**
     * create reservation
     */
    public function createReservation($account_id)
    {
        $customer = Account::find($account_id);
        $rooms = Room::where('room_status', '=', 0)->get();
        $roomTypes = RoomType::all();
        $mealPlans = MealPlan::all();
        $swimming = Product::find(1);
        return view('bookings.create', compact('customer', 'rooms', 'roomTypes', 'mealPlans', 'swimming'));
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required'
        ]);

        $customer = Account::find($request->account_id);

        DB::beginTransaction();

        try {
            $numOfPeople = 0;
            for ($i = 0; $i < count($request->room_id); $i++) {
                $numOfPeople += $request->num_of_people[$i];
            }
            $booking = new Booking();
            $booking->account_id = $request->account_id;
            $booking->checkin = $request->check_in;
            $booking->checkout = $request->check_out;
            $booking->meal_plan = $request->meal_plan;
            $booking->number_of_people = $numOfPeople;
            if (date('Y-m-d') == date('Y-m-d', strtotime($request->check_in))) {
                $booking->status = 1;
            } else {
                $booking->status = 0;
            }
            $booking->save();

            for ($i = 0; $i < count($request->room_id); $i++) {
                $details = new BookingDetail();
                $details->booking_id = $booking->id;
                $details->room_id = $request->room_id[$i];
                $details->num_of_people = $request->num_of_people[$i];
                $details->room_price = $request->room_price[$i];
                $details->save();

                if (date('Y-m-d') == date('Y-m-d', strtotime($request->check_in))) {
                    $rooms = Room::find($request->room_id[$i]);
                    $rooms->room_status = 1;
                    $rooms->update();
                }
            }

            $this->accommodationInvoice($request, $customer);
            $this->mealInvoice($request, $customer);
            $this->swimmingInvoice($request, $customer);

            DB::commit();

            return response(1);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response([$th->getMessage(), $th->getLine()]);
        }
    }


    /**
     * Accommodation invoice
     */
    public function accommodationInvoice(Request $request, $customer)
    {
        // create invoice for accommodation
        $accommodationTotal = $this->calculateTax($request->room_total_display);
        $inv = new Invoice();
        $inv->account_id = $request->account_id;
        $inv->inv_number = rand(1, 1000000);
        $inv->delivery_date = date('Y-m-d');
        $inv->tax_date = date('Y-m-d');
        $inv->to_date = $request->check_in;
        $inv->from_date = $request->check_out;
        $inv->invoiced_to = $request->account_id;
        $inv->vat_registration_number = $customer->vat_registration_number;
        $inv->sub_total = $accommodationTotal['subTotal'];
        $inv->tax_amount = $accommodationTotal['vat'];
        $inv->levy = $accommodationTotal['levy'];
        $inv->total = $accommodationTotal['total'];
        $inv->pos_used = 2;
        $inv->user_id = Auth::user()->id;
        $inv->description =  'Accommodation';
        $inv->save();

        for ($i = 0; $i < count($request->room_id); $i++) {
            $room = Room::find($request->room_id[$i]);

            $inv_item = new InvoiceItem();
            $inv_item->invoice_id = $inv->id;
            $inv_item->item_id = $room->number;
            $inv_item->item_code = $room->number;
            $inv_item->item_description = "Accommodation room number: $room->number";
            $inv_item->quantity = $request->num_of_people[$i];
            $inv_item->rate = $room->price;
            $inv_item->days = $request->stay_duration[$i];
            $inv_item->amount = $request->room_total[$i];
            $inv_item->save();
        }
    }

    /**
     * meal invoice
     */
    public function mealInvoice(Request $request, $customer)
    {
        // create invoice for meal
        $mealPlan = MealPlan::find($request->meal_plan);

        $mealTotal = $this->calculateTax($request->meal_total_display);
        $invoice = new Invoice();
        $invoice->account_id = $request->account_id;
        $invoice->inv_number = rand(1, 1000000);
        $invoice->delivery_date = date('Y-m-d');
        $invoice->tax_date = date('Y-m-d');
        $invoice->to_date = $request->check_in;
        $invoice->from_date = $request->check_out;
        $invoice->invoiced_to = $request->account_id;
        $invoice->vat_registration_number = $customer->vat_registration_number;
        $invoice->sub_total = $mealTotal['subTotal'];
        $invoice->tax_amount = $mealTotal['vat'];
        $invoice->levy = $mealTotal['levy'];
        $invoice->total = $mealTotal['total'];
        $invoice->pos_used = 3;
        $invoice->user_id = Auth::user()->id;
        $invoice->description = "Meals:  $mealPlan->name";
        $invoice->save();

        $num_of_people = 0;
        for ($i = 0; $i < count($request->room_id); $i++) {
            $num_of_people += $request->num_of_people[$i];
        }


        $inv_items = new InvoiceItem();
        $inv_items->invoice_id = $invoice->id;
        $inv_items->item_id = $mealPlan->id;
        $inv_items->item_code = rand(0, 100000);
        $inv_items->item_description = "Meal plan: $mealPlan->name";
        $inv_items->quantity = $num_of_people;
        $inv_items->rate = $mealPlan->price;
        $inv_items->days = $request->stay_duration[0];
        $inv_items->amount = $request->meal_total_display;
        $inv_items->save();
    }

    /**
     * 
     */
    public function swimmingInvoice(Request $request, $customer)
    {
        $num_of_people = 0;
        $item = Product::find(1);
        // swimming 
        // create invoice for swimming
        $swimmingTotal = $this->calculateTax($request->swimming_total_display);
        $invoice = new Invoice();
        $invoice->account_id = $request->account_id;
        $invoice->inv_number = rand(1, 1000000);
        $invoice->delivery_date = date('Y-m-d');
        $invoice->tax_date = date('Y-m-d');
        $invoice->to_date = $request->check_in;
        $invoice->from_date = $request->check_out;
        $invoice->invoiced_to = $request->account_id;
        $invoice->vat_registration_number = $customer->vat_registration_number;
        $invoice->sub_total = $swimmingTotal['subTotal'];
        $invoice->tax_amount = $swimmingTotal['vat'];
        $invoice->levy = $swimmingTotal['levy'];
        $invoice->total = $swimmingTotal['total'];
        $invoice->user_id = Auth::user()->id;
        $invoice->description = 'Swimming';
        $invoice->save();

        for ($i = 0; $i < count($request->room_id); $i++) {
            $num_of_people += $request->num_of_people[$i];
        }

        $inv_items = new InvoiceItem();
        $inv_items->invoice_id = $invoice->id;
        $inv_items->item_id = $item->id;
        $inv_items->item_code = $item->code;
        $inv_items->item_description = $item->description;
        $inv_items->quantity = $num_of_people;
        $inv_items->rate = $item->price;
        $inv_items->days = $request->stay_duration[0];
        $inv_items->amount = $request->swimming_total_display;
        $inv_items->save();
    }

    /**
     * activate booking
     */
    public function activateReservation(Request $request)
    {
        DB::beginTransaction();
        try {
            $booking = Booking::find($request->booking_id);
            $booking->status = 1;
            $booking->update();

            $details = $booking->booking_items;

            foreach ($details as $key => $detail) {
                $rooms = Room::find($detail->room_id);
                $rooms->room_status = 1;
                $rooms->update();
            }

            DB::commit();

            return redirect()->back()->with('success', 'Reservation is active!');
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with('error', 'System error please try again!');
        }
    }

    /**
     * checkout page
     */
    public function checkoutBookingPage($id)
    {
        $booking = Booking::find($id);
        $bookingItems = $booking->booking_items;
        foreach ($bookingItems as $key => $item) {
            $room = Room::find($item->room_id);
            $room->room_status = 0;
            $room->update();
        }
        return redirect()->back()->with('success', 'Checkout successful');
    }

    /**
     * checkout submit
     */
    public function checkoutBooking(Request $request)
    {
        $request->validate([
            'amount_paid' => 'required'
        ]);

        try {
            DB::beginTransaction();

            // update room status
            $booking = Booking::find($request->booking_id);
            $bookingItems = $booking->booking_items;
            foreach ($bookingItems as $key => $item) {
                $room = Room::find($item->room_id);
                $room->status = 0;
                $room->update();
            }

            // ensure balances are cleared

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'system error please try again!');
        }
    }


    // public function index()
    // {
    //     $captain = Booking::where('status', '=', 1)->orderBy('created_at', 'desc')->get();
    //     $customers = Account::all();
    //     foreach ($captain as $key => $booking) {
    //         // details
    //         $booking['rooms'] = '';
    //         $details = BookingDetail::where('booking_id', '=', $booking->id)->get();
    //         foreach ($details as $key => $detail) {
    //             $booking['rooms'] .= " {$detail->room->description} {$detail->room->number} ";
    //         }
    //     }
    //     return view('bookings.index', compact('captain', 'customers',));
    // }

}
