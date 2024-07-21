<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        $products = Product::orderBy('created_at','desc')->get();
        return view('products.index', compact('categories','products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'code' => 'required',
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'buying_price' => 'required',
            'taxable' => 'required'
        ]);

        $product = new Product();
        $product->category_id = $request->category_id;
        $product->code = $request->code;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->buying_price = $request->buying_price;
        $product->taxable = $request->taxable;
        if ($product->save()) {
            return redirect()->back()->with('success','Record added successfully');
        }
        return redirect()->back()->with('error','System error, please try again!');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required',
            'code' => 'required',
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'buying_price' => 'required',
            'taxable' => 'required'
        ]);

        $product = Product::find($id);
        $product->category_id = $request->category_id;
        $product->code = $request->code;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->buying_price = $request->buying_price;
        $product->taxable = $request->taxable;
        if ($product->update()) {
            return redirect()->back()->with('success','Record updated successfully');
        }
        return redirect()->back()->with('error','System error, please try again!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }

    /**
     * get products based on description
     * 
     * used in: proforma, quotation, invoice
     */
    public function queryItems(Request $request)
    {
        $items = Product::where('description', 'LIKE', '%'. $request->input_query .'%')->limit(5)->get();
        return response($items);
    }

    /**
     * contribution margin
     */
    
}
