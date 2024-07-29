<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Booking;
use App\Models\Day;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\KitchenStore;
use App\Models\Product;
use App\Models\Receipt;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class PosKitchenController extends Controller
{
    /**
     * page to show pos
     */
    public function posPage()
    {
        $items = new Collection();
        $products = Product::all();
        foreach ($products as $key => $product) {
            $kitchenStoreItem = KitchenStore::where('item_id','=', $product->id)->first();
            if ($kitchenStoreItem) {
                $items->push($product);
            }
        }
        $customers = new Collection();
        // $reservations = CaptainReservation::where('status', '=', 1)->get();
        // foreach ($reservations as $key => $reservation) {
        //     $customer = $reservation->customer;
        //     $reservationDetails = $reservation->reservations;
        //     $roomDetails = 'rooms ( ';
        //     foreach ($reservationDetails as $key => $detail) {
        //         $room = $detail->room->number;
        //         $roomDetails .= " $room ";
        //     }
        //     $roomDetails .= ' )';
        //     $customer['room_details'] = $roomDetails;
        //     $customers->push($customer);
        // }
        // return view('orders.create', compact('items', 'customers'));
        return view('departments.kitchen.pos', compact('items', 'customers'));
    }

    /**
     * search item
     */
    public function searchItem(Request $request)
    {
        $items = new Collection();
        $products = Product::where('name', 'LIKE', '%' . $request->search_tearm . '%')->limit(8)->get();
        foreach ($products as $key => $product) {
            $barStoreItem = KitchenStore::where('item_id','=', $product->id)->first();
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
                return response('add day');
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
            $invoice->sub_total = $request->sub_total;
            $invoice->tax_amount = $request->vat;
            $invoice->levy = $request->levy;
            $invoice->total = $request->total;
            $invoice->pos_used = 3;
            $invoice->user_id = Auth::user()->id;
            $invoice->description = 'Restaurant order';
            $invoice->table_number = 0;
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
                $inv_item->amount = $request->quantity[$i] * $product->price;
                $inv_item->rate = $product->price;
                $inv_item->days = 1;
                $inv_item->message = $request->message[$i];

                $inv_item->save();

                // check if item has recipe and deduct each recipe item from kitchen store\
                $recipes = $product->recipe;
                if ($recipes) {
                    foreach ($recipes as $key => $recipe) {
                        $recipeProduct = KitchenStore::where('item_id','=', $recipe->product_id)->first();
                        $recipeProduct->quantity = $recipeProduct->quantity - $recipe->quantity;
                        $recipeProduct->update();
                    }
                } 
                
                $kitchenStoreItem = KitchenStore::where('item_id','=', $request->item_id[$i])->first();
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
            
            return response(['status' => 1, 'order' => $invoice, 'items' => $items]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response([$th->getMessage(), $th->getLine()]);
        }
    }


    /**
     * kitchen orders
     */
    public function orders()
    {
        $orders = Invoice::where('pos_used','=', 3)->get();
        foreach ($orders as $key => $order) {
            $receipt_total = 0;
            $receipts = $order->receipt;
            foreach ($receipts as $key => $receipt) {
                $receipt_total += $receipt->amount;
            }
            $balance = $order->total  - $receipt_total;
            $order->bal = $balance;
        }
        $bankAccounts = BankAccount::all();
        return view('departments.kitchen.orders', compact('orders', 'bankAccounts'));
    }

    /**
     * print resource
     */
    public function ordersPrint($id)
    {
        $order = Invoice::find($id);
        $order_details = $order->items;
        return view('departments.kitchen.print', compact('order', 'order_details'));
    }

    /**
     * pay resource
     */
    public function ordersPayment(Request $request)
    {
        $invoice = Invoice::find($request->order_id);

        $receipt  = new Receipt();
        if ($invoice->customer_id == null) {
            $receipt->account_id = 1;
        } else {
            $receipt->account_id = $invoice->account_id;
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
        if ($receipt->save()) {
            return redirect()->back()->with('success', 'Order payment was successful');
        } 

        return redirect()->back()->with('error', 'System error, please try again.');
    }

    public function addToExistingOrderPage($id)
    {
        $order = Invoice::find($id);
        $items = new Collection();
        $products = Product::limit(8)->get();
        foreach ($products as $key => $product) {
            $barStoreItem = KitchenStore::where('item_id','=', $product->id)->first();
            if ($barStoreItem) {
                $items->push($product);
            }
        }
        if ($order) {
            return view('departments.kitchen.posAddOrder', compact('order', 'items'));
        } 
        return redirect()->back()->with('error', 'System error, please try again.');
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
     * add on top of existing order
     */
    public function addToExistingOrderSave(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required',
            'item_id' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $invoice = Invoice::find($request->invoice_id);

            $itemsToPrint = new Collection();

            if ($invoice) {
                for ($i=0; $i < count($request->item_id); $i++) { 
                    $product = Product::find($request->item_id[$i]);


                    $inv_item = new InvoiceItem();
                    $inv_item->invoice_id = $invoice->id;
                    $inv_item->item_id = $request->item_id[$i];
                    $inv_item->item_code = $product->code;
                    $inv_item->item_description = $product->description;
                    $inv_item->quantity = $request->quantity[$i];
                    $inv_item->amount = $request->quantity[$i] * $product->price;
                    $inv_item->rate = $product->price;
                    $inv_item->days = 1;
                    $inv_item->message = $request->message[$i];
                    $inv_item->save();

                    $p = new stdClass();
                    $p->invoice_id = $invoice->id;
                    $p->item_id = $request->item_id[$i];
                    $p->item_code = $product->code;
                    $p->item_description = $product->description;
                    $p->quantity = $request->quantity[$i];
                    $p->amount = $request->quantity[$i] * $request->price[$i];
                    $p->rate = $product->price;
                    $p->days = 1;
                    $p->name = $product->name;
                    $p->message = $request->message[$i];


                    $itemsToPrint->push($p);

                    // check if item has recipe and deduct each recipe item from kitchen store\
                    $recipes = $product->recipe;
                    if ($recipes) {
                        foreach ($recipes as $key => $recipe) {
                            $recipeProduct = KitchenStore::where('item_id','=', $recipe->product_id)->first();
                            $recipeProduct->quantity = $recipeProduct->quantity - $recipe->quantity;
                            $recipeProduct->update();
                        }
                    } 
                    $kitchenStoreItem = KitchenStore::where('item_id','=', $request->item_id[$i])->first();
                    $kitchenStoreItem->quantity = $kitchenStoreItem->quantity - $request->quantity[$i];
                    $kitchenStoreItem->update();
                }
            }

            // cal new total
            $invoiceItems = $invoice->items;
            $total = 0;
            foreach ($invoiceItems as $key => $item) {
                $total += $item->amount;
            }

            $taxCal = $this->calculateTax($total);
        
            $invoice->sub_total = $taxCal['subTotal'];
            $invoice->tax_amount = $taxCal['vat'];
            $invoice->levy = $taxCal['levy'];
            $invoice->total = $taxCal['total'];
            $invoice->update();

            $items = $invoice->items;
            foreach ($items as $key => $item) {
                $item->name = $item->item->name;
                $item->pr = $item->item->price;
            }

            DB::commit();
            return response(['status'=>1, 'order' => $invoice, 'items' => $items, 'itemToPrint' => $itemsToPrint]);
        } catch (\PDOException $th) {
            return response([$th->getMessage(), $th->getLine()]);
        }
    }

}
