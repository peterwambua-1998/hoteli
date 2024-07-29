<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaiterController extends Controller
{
    /**
     * select pos
     */
    public function selectPos()
    {
        return view('departments.change-pos');
    }
    

    /**
     * Waiter orders
     */
    public function orders()
    {
        $orders = Invoice::where('user_id','=', Auth::user()->id)->where('booking_id', '=', null)->where('voided','=', 0)->orderBy('created_at', 'desc')->get();
        foreach ($orders as $key => $order) {
            $receipt_total = 0;
            $receipts = $order->receipt;
            foreach ($receipts as $key => $receipt) {
                $receipt_total += $receipt->amount;
            }
            $balance = $order->total  - $receipt_total;
            $order->bal = $balance;
        }
        return view('waiter.orders', compact('orders'));
    }


    /**
     * add order to order
     */
    public function addToOrderSelectPos($id)
    {
        $order = Invoice::find($id);
        if ($order) {
            return view('departments.add-order-select-pos', compact('order'));
        } 
    }

    /**
     * add order to order
     */
    public function addToOrderSelectPosSave()
    {
        
    }
}
