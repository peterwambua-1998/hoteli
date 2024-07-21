<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\BarStore;
use App\Models\Booking;
use App\Models\Day;
use App\Models\Discount;
use App\Models\FrontOfficeStore;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\StockAdjustment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FrontOfficeStoreController extends Controller
{
    public function index()
    {
        $items = FrontOfficeStore::all();
        return view('departments.office.store', compact('items'));
    }

    /**
     * Display the specified resource.
     */
    public function adjustStock()
    {
        $frontStoreItems = FrontOfficeStore::all();
        return view('departments.office.adjust', compact('frontStoreItems'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function adjustStockStore(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'reason' => 'required',
        ]);

        DB::beginTransaction();

        try {
            for ($i=0; $i < count($request->item_id); $i++) { 
                $mainStore = FrontOfficeStore::where('item_id','=', $request->item_id[$i])->first();
                $mainStore->quantity = $request->quantity[$i];
                $mainStore->update();

                // add to adjustment table
                $adjustment = new StockAdjustment();
                $adjustment->store_id = 5;
                $adjustment->item_id = $request->item_id[$i];
                $adjustment->adjustment = $request->quantity[$i];
                $adjustment->reason  = $request->reason[$i];
                $adjustment->user_id = Auth::user()->id;
                $adjustment->save();
            }

            DB::commit();
            return redirect()->route('front.office.index')->with('success','Stock adjusted successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'System error please try again');
        }
    }


    public function posPage()
    {
        $items = new Collection();
        $products = Product::limit(15)->get();
        foreach ($products as $key => $product) {
            $barStoreItem = FrontOfficeStore::where('item_id','=', $product->id)->first();
            if ($barStoreItem) {
                $items->push($product);
            }
        }
        $customers = new Collection();
        $reservations = Booking::where('status', '=', 1)->get();
        foreach ($reservations as $key => $reservation) {
            $customer = $reservation->account;
            $reservationDetails = $reservation->booking_items;
            $roomDetails = 'rooms ( ';
            foreach ($reservationDetails as $key => $detail) {
                $room = $detail->room->number;
                $roomDetails .= " $room ";
            }
            $roomDetails .= ' )';
            $customer['room_details'] = $roomDetails;
            $customers->push($customer);
        }

        return view('departments.office.pos', compact('items', 'customers'));
    }


    /**
     * search item
     */
    public function searchItem(Request $request)
    {
        $items = new Collection();
        $products = Product::where('name', 'LIKE', '%' . $request->search_tearm . '%')->limit(8)->get();
        foreach ($products as $key => $product) {
            $barStoreItem = FrontOfficeStore::where('item_id','=', $product->id)->first();
            if ($barStoreItem) {
                $items->push($product);
            }
        }
        return response($items);
    }


    /**
     * store order
     */
    public function storeOrder(Request $request)
    {
        $request->validate([
            'item_id' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $day = Day::where('status', '=', 1)->first();
            if (!$day) {
                return response('add-day');
            }

            $total = [];

            if ($request->discount_id != 0) {
                $discount = Discount::find($request->discount_id); 
                $total_with_discount = $request->total - $discount->amount;
                $total = $this->calculateTax($total_with_discount);
            } else {
                $total = $this->calculateTax($request->total);
            }

            $invoice = new Invoice();
            $invoice->account_id = 1;
            $invoice->inv_number = rand(100, 1000000);
            $invoice->delivery_date = date('Y-m-d');
            $invoice->tax_date = date('Y-m-d');
            $invoice->to_date = date('Y-m-d');
            $invoice->from_date = date('Y-m-d');
            $invoice->invoiced_to = 'Walk In';
            $invoice->vat_registration_number = 'n/a';
            $invoice->sub_total = $total['subTotal'];
            $invoice->tax_amount = $total['vat'];
            $invoice->levy = $total['levy'];
            $invoice->total = $total['total'];
            $invoice->pos_used = 5;
            $invoice->user_id = Auth::user()->id;
            $invoice->table_number = 0;
            $invoice->description = 'Direct sale';
            $invoice->day_id = $day->id;
            $invoice->save();

            // save invoice details
            for ($i=0; $i < count($request->item_id); $i++) { 
                $product = Product::find($request->item_id[$i]);

                $inv_item = new InvoiceItem();
                $inv_item->invoice_id = $invoice->id;
                $inv_item->item_id = $request->item_id[$i];
                $inv_item->item_code = $product->code;
                $inv_item->item_description = $product->description;
                $inv_item->quantity = $request->quantity[$i];
                $inv_item->amount = $request->quantity[$i] * $request->price[$i];
                $inv_item->rate = $product->price;
                $inv_item->days = 1;
                $inv_item->save();

                // check if item has recipe and deduct each recipe item from kitchen store\
                $recipes = $product->recipe;
                if ($recipes) {
                    foreach ($recipes as $key => $recipe) {
                        $recipeProduct = FrontOfficeStore::where('item_id','=', $recipe->product_id)->first();
                        $recipeProduct->quantity = $recipeProduct->quantity - $recipe->quantity;
                        $recipeProduct->update();
                    }
                } 
                $kitchenStoreItem = FrontOfficeStore::where('item_id','=', $request->item_id[$i])->first();
                $kitchenStoreItem->quantity = $kitchenStoreItem->quantity - $request->quantity[$i];
                $kitchenStoreItem->update();
            }

            DB::commit();
            // TODO redirect to order and show user orders
            $items = $invoice->items;
            foreach ($items as $key => $item) {
                $item->name = $item->item->name;
                $item->pr = $item->item->price;
            }

            return response(['status'=>1, 'order' => $invoice, 'items' => $items]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response([$th->getMessage(), $th->getLine()]);
        }
    }


     /**
     * front office orders
     */
    public function orders()
    {
        $accounts = Account::all();
        $orders = Invoice::where('pos_used','=', 5)->where('user_id','=', Auth::user()->id)->where('voided','=', 0)->orderBy('created_at', 'desc')->get();
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
        $bankAccounts = BankAccount::all();
        return view('departments.office.orders', compact('orders', 'bankAccounts', 'accounts'));
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
                return redirect()->back()->with('success','Payment successful');
            } else {
                return redirect()->back()->with('error','System error please try again!');
            }
            
        } else {
            // package and complimentary
            if ($request->payment_method == 5 || $request->payment_method == 6) {
                $complimentaryOrPackage = $this->complimentaryOrPackage($request, $booking, $invoice);
                if ($complimentaryOrPackage == 1) {
                    return redirect()->back()->with('success','Payment successful');
                } else {
                    return redirect()->back()->with('error','System error please try again!');
                }
            } else {
                $cashChequeBank = $this->cashOrBankCheque($request, $booking, $invoice);
                if ($cashChequeBank == 1) {
                    return redirect()->back()->with('success','Payment successful');
                } else {
                    return redirect()->back()->with('error','System error please try again!');
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

        $totalPaid = 0;
        $prevReceipts = $invoice->receipt;

        foreach ($prevReceipts as $key => $item) {
            $totalPaid += $item->paid_amount;
        }

        $balance = $invoice->total - $totalPaid;

        $newTotals = $this->calculateTax($balance);

        $receipt->invoice_id = $invoice->id;
        $receipt->receipt_number = rand(1, 10000000);
        $receipt->payment_method = $request->payment_method;
        $receipt->payment_code = 0;
        $receipt->sub_total = $newTotals['subTotal'];
        $receipt->tax_amount = $newTotals['vat'];
        $receipt->amount = $balance;
        $receipt->paid_amount = $newTotals['total'];
        if ($receipt->save()) {
            return 1;
        }
        return 0;
    }

    /**
     * pay on checkout
     */
    public function payOnCheckout(Request $request, $booking, $invoice)
    {
        DB::beginTransaction();
        try {
            if ($booking->extras_paid_by == 1) {
                $bookingInvoice = Invoice::where('booking_id', '=', $booking->id)->where('account_id', '=', $booking->account_id)->first();
            }

            if ($booking->extras_paid_by == 2) {
                $bookingInvoice = Invoice::where('booking_id', '=', $booking->id)->where('account_id', '=', $booking->company_id)->first();
            }

            $invoiceItems = $invoice->items;

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

                $exitsInKitchenStore = FrontOfficeStore::where('item_id', '=', $product->id)->first();
                if ($exitsInKitchenStore) {
                    $recipes = $product->recipe;
                    if ($recipes) {
                        foreach ($recipes as $key => $recipe) {
                            $recipeProduct = FrontOfficeStore::where('item_id', '=', $recipe->product_id)->first();
                            $recipeProduct->quantity = $recipeProduct->quantity + $recipe->quantity;
                            $recipeProduct->update();
                        }
                    }
                    $kitchenStoreItem = FrontOfficeStore::where('item_id', '=', $item->item_id)->first();
                    $kitchenStoreItem->quantity = $kitchenStoreItem->quantity + $item->quantity;
                    $kitchenStoreItem->update();
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

    
}
