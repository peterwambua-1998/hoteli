<?php

namespace App\Http\Controllers;

use App\Imports\CategoryImport;
use App\Imports\ProductImport;
use App\Models\BarStore;
use App\Models\MainStore;
use App\Models\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function categoryPage()
    {
        return view('imports.category');
    }

    public function categorySave(Request $request)
    {
        Excel::import(new CategoryImport(), $request->file);
        return redirect()->back()->with('success', 'All good!');
    }

    public function productPage()
    {
        return view('imports.product');
    }

    public function productSave(Request $request)
    {
        Excel::import(new ProductImport(), $request->file);
        return redirect()->back()->with('success', 'All good!');
    }

    public function transferToMainStor()
    {
        $products = Product::where('id', '!=', 1)->get();
        foreach ($products as $key => $product) {
            $mainStore = new MainStore();
            $mainStore->item_id = $product->id;
            $mainStore->quantity = 100;
            $mainStore->save();

            $barStore = new BarStore();
            $barStore->item_id = $product->id;
            $barStore->quantity = 100;
            $barStore->save();
        }

        dd('hurray');
    }
}
