<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\BarStore;
use App\Models\Booking;
use App\Models\Day;
use App\Models\Invoice;
use App\Models\KitchenStore;
use App\Models\MealPlan;
use App\Models\Package;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\Room;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    public function orders()
    {
        $activeDay = Day::where('status','=',1)->orderBy('created_at')->first();
        $accounts = Account::all();
        $bankAccounts = BankAccount::all();
        $orders = Invoice::whereIn('pos_used', [1, 3, 4])->where('voided', '=', 0)->where('from_accommodation', '=', 0)->orderBy('created_at', 'desc')->get();
        foreach ($orders as $key => $order) {
            $receipt_total = 0;
            $receipts = $order->receipt;
            foreach ($receipts as $key => $receipt) {
                if ($receipt->payment_method == 5 || $receipt->payment_method == 6) {
                    $receipt_total = $order->total;
                } else {
                    $receipt_total += $receipt->amount;
                }
            }
            $balance = $order->total  - $receipt_total;
            $order->bal = $balance;
            // ORDER STATUS 
            if ($receipt_total == $order->total) {
                $order->st = 1;
            }

            if ($receipt_total != $order->total) {
                $order->st = 0;
            }
        }

        return view('cashier.orders', compact('orders', 'bankAccounts', 'accounts'));
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
        // TODO update payment to accounts except cash account
        $request->validate([
            'order_id' => 'required'
        ]);

        $invoice = Invoice::find($request->order_id);
        $booking = Booking::where('account_id', '=', $request->account_id)->where('status', '=', 1)->first();

        if ($request->payment_method == 7) {
            // pay on checkout
            $payOnCheckout = $this->payOnCheckout($request, $booking, $invoice);
            if ($payOnCheckout == 1) {
                return redirect()->back()->with('success', 'Payment Successful');
            } else {
                return redirect()->back()->with('error', 'System error please try again!');
            }
        } else {
            // package and complimentary
            if ($request->payment_method == 5 || $request->payment_method == 6) {
                $complimentary = $this->complimentaryOrPackage($request, $booking, $invoice);
                if ($complimentary == 1) {
                    return redirect()->back()->with('success', 'Payment Successful');
                } else {
                    return redirect()->back()->with('error', 'System error please try again!');
                }
            } else {
                $cashBank = $this->cashOrBankCheque($request, $booking, $invoice);
                if ($cashBank == 1) {
                    return redirect()->back()->with('success', 'Payment Successful');
                } else {
                    return redirect()->back()->with('error', 'System error please try again!');
                }
            }
        }
    }

    /**
     * 1. Bank transfer
     * 3. Cash
     * 4. Cheque
     */
    public function cashOrBankCheque(Request $request, $booking, $invoice)
    {
        DB::beginTransaction();

        try {
            //code...
            $receipt  = new Receipt();
            if ($request->account_id == 0) {
                $receipt->account_id = 1;
            } else {
                if ($booking) {
                    $paidBy = $booking->extras_paid_by;
                    if ($paidBy == 1) {
                        $receipt->account_id = $request->account_id;
                        $invoice->account_id = $request->account_id;
                        $invoice->update();
                    }

                    if ($paidBy == 2) {
                        $receipt->account_id = $booking->company_id;
                        $invoice->account_id = $request->account_id;
                        $invoice->update();
                    }
                }
            }
            $receipt->invoice_id = $invoice->id;
            $receipt->bank_account_id = $request->bank_account_id;
            $receipt->receipt_number = rand(1, 10000000);
            $receipt->payment_method = $request->payment_method;
            $receipt->payment_code = $request->payment_code;
            $receipt->sub_total = $invoice->sub_total;
            $receipt->tax_amount = $invoice->tax_amount;
            $receipt->amount = $request->amount;
            $receipt->paid_amount = $request->amount;
            $receipt->save();

            if ($request->bank_account_id != 1) {
                $bankAccount = BankAccount::find($request->bank_account_id);
                if ($bankAccount) {
                    $bankAccount->available_balance = $bankAccount->available_balance + $request->amount;
                    $bankAccount->update();
                }
            }


            DB::commit();
            return 1;
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return 0;
        }
    }

    /**
     * complimentary and package
     */
    public function complimentaryOrPackage(Request $request, $booking, $invoice)
    {
        DB::beginTransaction();

        try {
            $receipt  = new Receipt();
            if ($request->account_id == 0) {
                $receipt->account_id = 1;
            } else {
                if ($booking) {
                    $paidBy = $booking->extras_paid_by;
                    if ($paidBy == 1) {
                        $receipt->account_id = $request->account_id;
                        $invoice->account_id = $request->account_id;
                        $invoice->update();
                    }

                    if ($paidBy == 2) {
                        $receipt->account_id = $booking->company_id;
                        $invoice->account_id = $request->account_id;
                        $invoice->update();
                    }
                }
            }
            $receipt->invoice_id = $invoice->id;
            $receipt->receipt_number = rand(1, 10000000);
            $receipt->payment_method = $request->payment_method;
            $receipt->payment_code = 0;
            $receipt->sub_total = 0;
            $receipt->tax_amount = 0;
            $receipt->amount = 0;
            $receipt->paid_amount = 0;
            $receipt->save();

            $invoice->sub_total = 0;
            $invoice->tax_amount = 0;
            $invoice->levy = 0;
            $invoice->total = 0;
            $invoice->update();

            DB::commit();

            return 1;
        } catch (\PDOException $th) {
            DB::rollBack();

            return 0;
        }
    }

    /**
     * pay on checkout
     */
    public function payOnCheckout(Request $request, $booking, $invoice)
    {
        DB::beginTransaction();
        try {
            if ($booking) {
                if ($booking->extras_paid_by == 1) {
                    $bookingInvoice = Invoice::where('booking_id', '=', $booking->id)->where('account_id', '=', $booking->account_id)->first();
                }

                if ($booking->extras_paid_by == 2) {
                    $bookingInvoice = Invoice::where('booking_id', '=', $booking->id)->where('account_id', '=', $booking->company_id)->first();
                }


                $invoiceItems = $invoice->items;

                $receipts = $invoice->receipt;

                $paidBy = $booking->extras_paid_by;

                if ($bookingInvoice) {
                    foreach ($invoiceItems as $key => $item) {
                        $item->invoice_id = $bookingInvoice->id;
                        $item->update();
                    }
                } else {
                    return redirect()->back()->with('error', 'Please contact admin booking not found');
                }

                // recalculate totals
                $bookingInvoiceItems = $bookingInvoice->items;
                $totalAmt = 0;
                foreach ($bookingInvoiceItems as $key => $item) {
                    $totalAmt += $item->amount;
                }

                // calculate taxes
                $bookingInvoiceTaxes = $this->calculateTax($totalAmt);

                $bookingInvoice->sub_total = $bookingInvoiceTaxes['subTotal'];
                $bookingInvoice->tax_amount = $bookingInvoiceTaxes['vat'];
                $bookingInvoice->levy = $bookingInvoiceTaxes['levy'];
                $bookingInvoice->total = $bookingInvoiceTaxes['total'];
                $bookingInvoice->update();

                $invoice->delete();
            } else {
                $invoice->account_id = $request->account_id;
                $invoice->update();

                // 
            }

            DB::commit();

            return 1;
        } catch (\PDOException $th) {
            //throw $th;
            DB::rollBack();

            return 0;
        }
    }


    /**
     * void order
     */
    public function voidOrder(Request $request)
    {
        DB::beginTransaction();

        try {
            $invoice = Invoice::find($request->invoice_id);
            $invoice->voided = 1;
            $invoice->update();

            $items = $invoice->items;

            foreach ($items as $key => $item) {
                $product = Product::find($item->item_id);

                $exitsInKitchenStore = KitchenStore::where('item_id', '=', $product->id)->first();
                if ($exitsInKitchenStore) {
                    $recipes = $product->recipe;
                    if ($recipes) {
                        foreach ($recipes as $key => $recipe) {
                            $recipeProduct = KitchenStore::where('item_id', '=', $recipe->product_id)->first();
                            $recipeProduct->quantity = $recipeProduct->quantity + $recipe->quantity;
                            $recipeProduct->update();
                        }
                    }
                    $kitchenStoreItem = KitchenStore::where('item_id', '=', $item->item_id)->first();
                    $kitchenStoreItem->quantity = $kitchenStoreItem->quantity + $item->quantity;
                    $kitchenStoreItem->update();
                }


                $exitsInBarStore = BarStore::where('item_id', '=', $product->id)->first();
                if ($exitsInBarStore) {
                    $recipes = $product->recipe;
                    if ($recipes) {
                        foreach ($recipes as $key => $recipe) {
                            $recipeProduct = BarStore::where('item_id', '=', $recipe->product_id)->first();
                            $recipeProduct->quantity = $recipeProduct->quantity + $recipe->quantity;
                            $recipeProduct->update();
                        }
                    }
                    $barStoreItem = BarStore::where('item_id', '=', $item->item_id)->first();
                    $barStoreItem->quantity = $barStoreItem->quantity + $item->quantity;
                    $barStoreItem->update();
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Order marked void!');
        } catch (\PDOException $th) {
            //throw $th;
            DB::rollBack();
            return redirect()->back()->with('error', 'System error please try again!');
        }
    }

    /**
     * end Day
     */
    public function endDay(Request $request)
    {
    }


    /**
     * system cash
     */
    public function systemCash()
    {
        // day
        $day = Day::where('status', '=', 1)->first();

        $invoices = Invoice::whereIn('pos_used', [1, 3, 4])->where('voided', '=', 0)->where('day_id', '=', $day->id)->get();
        $cash = 0;
        foreach ($invoices as $key => $invoice) {
            $receipts = $invoice->receipt;
            foreach ($receipts as $key => $receipt) {
                if ($receipt->payment_method == 1) {
                    $cash += $receipt->paid_amount;
                }
            }
        }
        dd($cash);
    }
}
