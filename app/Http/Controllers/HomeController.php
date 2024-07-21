<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Invoice;
use App\Models\Receipt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (Auth::user()->role != 1) {
            return redirect()->route('select.pos');
        }
        $orders = Invoice::whereIn('pos_used', [1, 3, 4])->where('voided', '=', 0)->where('from_accommodation', '=', 0)->orderBy('created_at', 'desc')->get();
        $todayInvoices = Invoice::where('voided', '=', 0)->whereDate('created_at', Carbon::today())->get();
        $countOrdersToday = 0;


        $totalSales = 0;

        $totalPayment = 0;

        // here we only took invoices cleared completely
        foreach ($todayInvoices as $key => $invoice) {
            $receiptTotal = 0;
            foreach ($invoice->receipt as $key => $receipt) {
                $receiptTotal += $receipt->paid_amount;
            }
            if ($receiptTotal == $invoice->total) {
                $countOrdersToday += 1;
            }

            $totalPayment += $receiptTotal;
            $totalSales += $invoice->total;
        }



        return view('home', compact('countOrdersToday', 'totalSales', 'orders', 'totalPayment'));
    }

    // sales per dept
    // accounts and their cash inflows
    // expenses

    public function cashInflows()
    {
        $accounts = BankAccount::all();
        foreach ($accounts as $key => $account) {
            $cashFlow = 0;
            $invoices = Invoice::whereIn('pos_used', [1, 3, 4])->where('voided', '=', 0)->whereDate('created_at', Carbon::today())->get();
            foreach ($invoices as $key => $invoice) {
                $receipts = $invoice->receipt;
                foreach ($receipts as $key => $receipt) {
                    if ($receipt->bank_account_id == $account->id) {
                        $cashFlow += $receipt->paid_amount;
                    }
                }
                
            }
            $account->cashFlow = $cashFlow;
        }

        return response($accounts);
    }
}
