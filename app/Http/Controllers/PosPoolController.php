<?php

namespace App\Http\Controllers;

use App\Models\BarStore;
use App\Models\Day;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\KitchenStore;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosPoolController extends Controller
{
    public function posPage()
    {
        $items = new Collection();
        $products = Product::limit(15)->get();
        foreach ($products as $key => $product) {
            $kitchenStoreItem = KitchenStore::where('item_id', '=', $product->id)->first();
            if ($kitchenStoreItem) {
                $items->push($kitchenStoreItem);
            }
            $barStoreItem = BarStore::where('item_id', '=', $product->id)->first();
            if ($barStoreItem) {
                $items->push($product);
            }
        }

        return view('departments.pool.pos', compact('items'));
    }

    public function searchItem(Request $request)
    {
        $items = new Collection();
        $products = Product::where('name', 'LIKE', '%' . $request->search_tearm . '%')->limit(8)->get();
        foreach ($products as $key => $product) {
            $kitchenStoreItem = KitchenStore::where('item_id', '=', $product->id)->first();
            if ($kitchenStoreItem) {
                $items->push($kitchenStoreItem);
            }
            $barStoreItem = BarStore::where('item_id', '=', $product->id)->first();
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
                return redirect()->back()->with('error', 'Please add day!');
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
            $invoice->pos_used = 4;
            $invoice->user_id = Auth::user()->id;
            $invoice->table_number = 0;
            $invoice->description = 'Pool order';
            $invoice->day_id = $day->id;
            $invoice->save();

            // save invoice details
            for ($i = 0; $i < count($request->item_id); $i++) {
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

                $barStoreItem = BarStore::where('item_id', '=', $product->id)->first();
                if ($barStoreItem) {
                    // check if item has recipe and deduct each recipe item from kitchen store
                    $recipes = $product->recipe;
                    if ($recipes) {
                        foreach ($recipes as $key => $recipe) {
                            $recipeProduct = BarStore::where('item_id', '=', $recipe->product_id)->first();
                            $recipeProduct->quantity = $recipeProduct->quantity - $recipe->quantity;
                            $recipeProduct->update();
                        }
                    }
                    $kitchenStoreItem = BarStore::where('item_id', '=', $request->item_id[$i])->first();
                    $kitchenStoreItem->quantity = $kitchenStoreItem->quantity - $request->quantity[$i];
                    $kitchenStoreItem->update();
                }
                $kitchenStoreItem = KitchenStore::where('item_id', '=', $product->id)->first();
                if ($kitchenStoreItem) {
                    $recipes = $product->recipe;
                    if ($recipes) {
                        foreach ($recipes as $key => $recipe) {
                            $recipeProduct = KitchenStore::where('item_id', '=', $recipe->product_id)->first();
                            $recipeProduct->quantity = $recipeProduct->quantity - $recipe->quantity;
                            $recipeProduct->update();
                        }
                    }
                    $kitchenStoreItem = KitchenStore::where('item_id', '=', $request->item_id[$i])->first();
                    $kitchenStoreItem->quantity = $kitchenStoreItem->quantity - $request->quantity[$i];
                    $kitchenStoreItem->update();
                }
            }

            DB::commit();
            // TODO redirect to order and show user orders
            return response(['status' => 1, 'order_id' => $invoice->id]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response([$th->getMessage(), $th->getLine()]);
        }
    }


    public function addToExistingOrderPage($id)
    {
        $order = Invoice::find($id);
        $items = new Collection();
        $products = Product::limit(8)->get();
        foreach ($products as $key => $product) {
            $barStoreItem = KitchenStore::where('item_id', '=', $product->id)->first();
            if ($barStoreItem) {
                $items->push($product);
            }
            $barStoreItem = BarStore::where('item_id', '=', $product->id)->first();
            if ($barStoreItem) {
                $items->push($product);
            }
        }
        if ($order) {
            return view('departments.pool.posAddOrder', compact('order', 'items'));
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

            if ($invoice) {
                for ($i = 0; $i < count($request->item_id); $i++) {
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

                    $barStoreItem = BarStore::where('item_id', '=', $request->item_id[$i])->first();
                    if ($barStoreItem) {
                        // check if item has recipe and deduct each recipe item from kitchen store
                        $recipes = $product->recipe;
                        if ($recipes) {
                            foreach ($recipes as $key => $recipe) {
                                $recipeProduct = BarStore::where('item_id', '=', $recipe->product_id)->first();
                                $recipeProduct->quantity = $recipeProduct->quantity - $recipe->quantity;
                                $recipeProduct->update();
                            }
                        }
                        $barStoreItem->quantity = $barStoreItem->quantity - $request->quantity[$i];
                        $barStoreItem->update();
                    }
                    $kitchenStoreItem = KitchenStore::where('item_id', '=', $request->item_id[$i])->first();
                    if ($kitchenStoreItem) {
                        $recipes = $product->recipe;
                        if ($recipes) {
                            foreach ($recipes as $key => $recipe) {
                                $recipeProduct = KitchenStore::where('item_id', '=', $recipe->product_id)->first();
                                $recipeProduct->quantity = $recipeProduct->quantity - $recipe->quantity;
                                $recipeProduct->update();
                            }
                        }
                        $kitchenStoreItem->quantity = $kitchenStoreItem->quantity - $request->quantity[$i];
                        $kitchenStoreItem->update();
                    }
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

            DB::commit();
            return response(['status' => 1, 'order_id' => $invoice->id]);
        } catch (\PDOException $th) {
            return response([$th->getMessage(), $th->getLine()]);
        }
    }
}
