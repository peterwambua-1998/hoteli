<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\KitchenStore;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\StockAdjustment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KitchenStoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = KitchenStore::all();
        return view('kitchen-store.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kitchen-store.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $note)
    {
        for ($i = 0; $i < count($request->item_id); $i++) {
            $storeItemExists = KitchenStore::where('item_id', '=', $request->item_id[$i])->first();
            if ($storeItemExists) {
                $storeItemExists->quantity = $storeItemExists->quantity + $request->qty_received[$i];
                $storeItemExists->update();
            } else {
                $store = new KitchenStore();
                $store->good_receive_note_id = $note->id;
                $store->item_id = $request->item_id[$i];
                $store->quantity = $request->qty_received[$i];
                $store->save();
            }
        }
    }

    /** 
     * 
    */
    public function createMenuItem()
    {
        $categories = Category::all();
        return view('kitchen-store.recipe', compact('categories'));
    }

    /**
     * get products based on description
     * 
     * used in: proforma, quotation, invoice
     */
    public function queryItems(Request $request)
    {
        $items = new Collection();
        $products = Product::where('description', 'LIKE', '%'. $request->input_query .'%')->limit(5)->get();
        foreach ($products as $key => $product) {
            $kitchenStoreItem = KitchenStore::where('item_id','=', $product->id)->first();
            if ($kitchenStoreItem) {
                $items->push($product);
            }
        }
        return response($items);
    }

    /**
     * 
     */
    public function storeMenuItem(Request $request)
    {
        
        DB::beginTransaction();

        try {
            // we create the item
            $item = new Product();
            $item->category_id = $request->category_id;
            $item->code = $request->code;
            $item->name = $request->name;
            $item->description = $request->description;
            $item->price = $request->price;
            $item->buying_price = $request->buying_price;
            $item->save();

            // add item to kitchen store
            $store = new KitchenStore();
            $store->item_id = $item->id;
            $store->quantity = $request->item_quantity;
            $store->save();


            
            if (count($request->product_id) > 0) {
                for ($i = 0; $i < count($request->product_id); $i++) {
                    if ($request->product_id[$i] != null) {
                        $recipe = new Recipe();
                        $recipe->item_id = $item->id;
                        $recipe->product_id = $request->product_id[$i];
                        $recipe->quantity = $request->product_quantity[$i];
                        $recipe->user_id = Auth::user()->id;
                        $recipe->save();
                    }
                }
            }
           

            DB::commit();
            return redirect()->route('kitchen.store.index')->with('success','Record added successfully');

        } catch (\PDOException $th) {
            DB::rollBack();
            dd($th->getMessage());
            return redirect()->back()->with('error','System error, please try again!');
        }
    }


    /**
     * Display the specified resource.
     */
    public function adjustStock()
    {
        $kitchenStoreItems = KitchenStore::all();
        return view('kitchen-store.adjust', compact('kitchenStoreItems'));
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
                $mainStore = KitchenStore::where('item_id','=', $request->item_id[$i])->first();
                $mainStore->quantity = $request->quantity[$i];
                $mainStore->update();

                // add to adjustment table
                $adjustment = new StockAdjustment();
                $adjustment->store_id = 2;
                $adjustment->item_id = $request->item_id[$i];
                $adjustment->adjustment = $request->quantity[$i];
                $adjustment->reason  = $request->reason[$i];
                $adjustment->user_id = Auth::user()->id;
                $adjustment->save();
            }

            DB::commit();
            return redirect()->route('kitchen.store.index')->with('success','Stock adjusted successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'System error please try again');
        }
    }



}
