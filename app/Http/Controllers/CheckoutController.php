<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Receipt;
use App\Models\Room;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * 
     * add discount
     */
    public function addDiscountCheckout(Request $request)
    {
        $request->validate([
            'discount_amount' => 'required'
        ]);

        $invoice = Invoice::find($request->invoice_id);
        if ($invoice) {
            $invoice->discount_amount = $request->discount_amount;
            $invoice->update();
            return redirect()->back()->with('success', 'discount applied');

        }

        return redirect()->back()->with('error', 'System error');
    }




    // TODO ensure you consider debit note and credit note
    public function checkoutPage($id, $account_id)
    {
        // personal booking
        $booking = Booking::where('id', '=', $id)->first();
        $bankAccounts = BankAccount::all();
        if ($booking) {
            $booking->invoices = new Collection();
            $invoices = Invoice::where('booking_id', '=', $booking->id)->where('account_id','=', $account_id)->get();
            foreach ($invoices as $key => $order) {
                $receipt_total = 0;
                $receipts = $order->receipt;
                foreach ($receipts as $key => $receipt) {
                    if ($receipt->payment_method == 5 || $receipt->payment_method == 6) {
                        $receipt_total = $order->total;
                    } else {
                        $receipt_total += $receipt->amount;
                    }
                }
                $order->payment_receipts = $receipts;
                $balance = $order->total  - $receipt_total;
                // minus discount from balance
                $order->bal = $balance - $order->discount_amount;
                // ORDER STATUS 
                if ($receipt_total == $order->total) {
                    $order->st = 1;
                }

                if ($receipt_total != $order->total) {
                    $order->st = 0;
                }

                $booking->invoices->push($order);
            }
            return view('bookings.checkout', compact('invoices', 'bankAccounts', 'booking'));
        }
    }

    public function print($id)
    {
        // personal booking
        $bankAccounts = BankAccount::where('id', '!=', 1)->get();
        $invoice = Invoice::where('id', '=', $id)->first();
        if ($invoice) {
            return view('bookings.print', compact('invoice', 'bankAccounts'));
        } else {
            return redirect()->back()->with('error', 'System error please again');
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

    /**
     * Pay order
     */
    public function payOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required'
        ]);
        $invoice = Invoice::find($request->order_id);
        $booking = Booking::where('account_id', '=', $request->account_id)->where('status', '=', 1)->first();
        DB::beginTransaction();
        try {
            if ($request->payment_method == 5 || $request->payment_method == 6) {
                $receipt  = new Receipt();
                $receipt->account_id = $booking->account_id;
                $receipt->invoice_id = $invoice->id;
                $receipt->receipt_number = rand(1, 10000000);
                $receipt->payment_method = $request->payment_method;
                $receipt->payment_code = 0;
                $receipt->sub_total = $invoice->sub_total;
                $receipt->tax_amount = $invoice->tax_amount;
                $receipt->amount = $invoice->total;
                $receipt->paid_amount = $invoice->total;
                $receipt->save();
            } else {
                $taxAmount = $this->calculateTax($request->amount);
                $receipt  = new Receipt();
                $receipt->account_id = $booking->account_id;
                $receipt->invoice_id = $invoice->id;
                $receipt->bank_account_id = $request->bank_account_id;
                $receipt->receipt_number = rand(1, 10000000);
                $receipt->payment_method = $request->payment_method;
                $receipt->payment_code = $request->payment_code;
                $receipt->sub_total = $taxAmount['subTotal'];
                $receipt->tax_amount = $taxAmount['vat'];
                $receipt->amount = $invoice->total;
                $receipt->paid_amount = $taxAmount['total'];
                $receipt->save();
            }

            // updated bank account
            $bankAccount = BankAccount::find($request->bank_account_id);
            if ($bankAccount) {
                $bankAccount->available_balance = $bankAccount->available_balance + $request->amount;
                $bankAccount->update();
            }

            $invoiceReceipts = $invoice->receipt;
            $receiptTotal = 0;

            foreach ($invoiceReceipts as $key => $receipt) {
                $receiptTotal += $receipt->paid_amount;
            }

            if ($receiptTotal == ($invoice->total - $invoice->discount_amount)) {
                 // clear room
                $bookingRooms = $booking->booking_items;
                foreach ($bookingRooms as $key => $rm) {
                    $room = Room::where('id', '=', $rm->room_id)->first();
                    $room->room_status = 0;
                    $room->update();
                }

                // update booking
                $booking->status = 0;
                $booking->update();
            }

            DB::commit();

            return redirect()->route('reservations.index')->with([
                'success' => 'Invoice payment was successful',
            ]);

        } catch (\PDOException $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'System error, please try again.');
        }
    }


    public function checkoutUnderCompany(Request $request)
    {
        $request->validate([
            'booking_id' => 'required'
        ]);


        DB::beginTransaction();

        try {
            $booking = Booking::find($request->booking_id);
            if ($booking) {
                foreach ($booking->booking_items as $key => $item) {
                    $room = Room::where('id', '=', $item->room_id)->first();
                    $room->room_status = 0;
                    $room->update();
                }
            }

            $booking->status = 0;
            $booking->update();

            DB::commit();

            return redirect()->route('reservations.index')->with('succuss','Checkout successful');
        } catch (\PDOException $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'System error, please try again.');
        }
        
    }
}
