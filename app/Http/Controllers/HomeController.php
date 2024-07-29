<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\BarStore;
use App\Models\Day;
use App\Models\Invoice;
use App\Models\KitchenStore;
use App\Models\Receipt;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
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

        $activeDay = Day::where('status', '=', 1)->orderBy('created_at')->first();
        $orders = Invoice::whereIn('pos_used', [1, 3, 4])->where('voided', '=', 0)->where('from_accommodation', '=', 0)->orderBy('created_at', 'desc')->get();
        $todayInvoices = Invoice::where('voided', '=', 0)->where('day_id', '=', $activeDay->id)->get();
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

        $balance = $totalPayment - $totalSales;

        return view('home', compact('countOrdersToday', 'totalSales', 'orders', 'totalPayment', 'balance'));
    }

    // sales per dept
    // accounts and their cash inflows
    // expenses

    public function cashInflows()
    {
        $activeDay = Day::where('status', '=', 1)->orderBy('created_at')->first();
        $accounts = BankAccount::all();
        foreach ($accounts as $key => $account) {
            $cashFlow = 0;
            $invoices = Invoice::whereIn('pos_used', [1, 3, 4])->where('voided', '=', 0)->where('day_id', '=', $activeDay->id)->get();
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


    /**
     * shows dept of each invoices as they come
     */
    public function invoicePosting()
    {
        $activeDay = Day::where('status', '=', 1)->orderBy('created_at')->first();
        $barInvoices = Invoice::where('pos_used', '=', 1)->where('voided', '=', 0)->where('day_id', '=', $activeDay->id)->get();
        $barTotal = 0;
        foreach ($barInvoices as $key => $inv) {
            $receiptAmt = 0;
            foreach ($inv->receipt as $key => $receipt) {
                if ($receipt->payment_method != 5 || $receipt->payment_method != 6) {
                    $receiptAmt += $receipt->paid_amount;
                }
            }
            $barTotal += $receiptAmt;
        }
        $restInvoices = Invoice::where('pos_used', '=', 3)->where('voided', '=', 0)->where('day_id', '=', $activeDay->id)->get();
        $restTotal = 0;
        foreach ($restInvoices as $key => $inv) {
            $receiptAmt = 0;
            foreach ($inv->receipt as $key => $receipt) {
                if ($receipt->payment_method != 5 || $receipt->payment_method != 6) {
                    $receiptAmt += $receipt->paid_amount;
                }
            }
            $restTotal += $receiptAmt;
        }
        $poolInvoices = Invoice::where('pos_used', '=', 4)->where('voided', '=', 0)->where('day_id', '=', $activeDay->id)->get();
        $poolTotal = 0;
        foreach ($poolInvoices as $key => $inv) {
            $poolTotal += $inv->total;
        }
        return response(['bar' => $barTotal, 'restaurant' => $restTotal, 'pool' => $poolTotal]);
    }


    public function salesPerDayQuery(Request $request)
    {
        $fromDate = $request->from;
        $toDate =  Carbon::parse($request->to)->endOfDay();

        $to = $request->to;
        $from = $request->from;

        $barFullTotal = 0;
        $restFullTotal = 0;
        $barKitchenFullTotal = 0;

        $cashFlowFullTotal = 0;
        $totalBalance = 0;

        $days = Day::whereBetween('created_at',[$fromDate, $toDate])->get();
        $accounts = BankAccount::all();

        
        foreach ($days as $key => $day) {
            $barInvoices = Invoice::where('pos_used', '=', 1)->where('voided', '=', 0)->where('day_id', '=', $day->id)->get();
            $barTotal = 0;
            foreach ($barInvoices as $key => $inv) {
                $receiptAmt = 0;
                foreach ($inv->receipt as $key => $receipt) {
                    if ($receipt->payment_method != 5 || $receipt->payment_method != 6) {
                        $receiptAmt += $receipt->paid_amount;
                    }
                }
                $barTotal += $receiptAmt;
            }
            $barFullTotal += $barTotal;
            $restInvoices = Invoice::where('pos_used', '=', 3)->where('voided', '=', 0)->where('day_id', '=', $day->id)->get();
            $restTotal = 0;
            foreach ($restInvoices as $key => $inv) {
                $receiptAmt = 0;
                foreach ($inv->receipt as $key => $receipt) {
                    if ($receipt->payment_method != 5 || $receipt->payment_method != 6) {
                        $receiptAmt += $receipt->paid_amount;
                    }
                }
                $restTotal += $receiptAmt;
            }
            $restFullTotal += $restTotal;

            $cashFlowTotal = 0;
            foreach ($accounts as $key => $account) {
                $cashFlow = 0;
                $invoices = Invoice::whereIn('pos_used', [1, 3, 4])->where('voided', '=', 0)->where('day_id', '=', $day->id)->get();
                foreach ($invoices as $key => $invoice) {
                    $receipts = $invoice->receipt;
                    foreach ($receipts as $key => $receipt) {
                        if ($receipt->bank_account_id == $account->id) {
                            $cashFlow += $receipt->paid_amount;
                        }
                    }
                }
                $account->cashFlow += $cashFlow;
                $cashFlowTotal += $cashFlow;
            }

            $cashFlowFullTotal += $cashFlowTotal;

            //
            $barKitchenTotal = $barTotal + $restTotal;
            $balance = ($barTotal + $restTotal) - $cashFlowTotal;


            $barKitchenFullTotal += $barKitchenTotal;

            $totalBalance += $balance;
        }

        return view('sales-report-query', compact('barFullTotal', 'restFullTotal', 'barKitchenFullTotal', 'accounts', 'totalBalance', 'cashFlowFullTotal', 'to','from'));
    }


    public function salesPerDay()
    {
        $activeDay = Day::where('status', '=', 1)->first();
        $barInvoices = Invoice::where('pos_used', '=', 1)->where('voided', '=', 0)->where('day_id', '=', $activeDay->id)->get();
        $barTotal = 0;
        foreach ($barInvoices as $key => $inv) {
            $receiptAmt = 0;
            foreach ($inv->receipt as $key => $receipt) {
                if ($receipt->payment_method != 5 || $receipt->payment_method != 6) {
                    $receiptAmt += $receipt->paid_amount;
                }
            }
            $barTotal += $receiptAmt;
        }

        $restInvoices = Invoice::where('pos_used', '=', 3)->where('voided', '=', 0)->where('day_id', '=', $activeDay->id)->get();
        $restTotal = 0;
        foreach ($restInvoices as $key => $inv) {
            $receiptAmt = 0;
            foreach ($inv->receipt as $key => $receipt) {
                if ($receipt->payment_method != 5 || $receipt->payment_method != 6) {
                    $receiptAmt += $receipt->paid_amount;
                }
            }
            $restTotal += $receiptAmt;
        }

        $cashFlowTotal = 0;
        $accounts = BankAccount::all();
        foreach ($accounts as $key => $account) {
            $cashFlow = 0;
            $invoices = Invoice::whereIn('pos_used', [1, 3, 4])->where('voided', '=', 0)->where('day_id', '=', $activeDay->id)->get();
            foreach ($invoices as $key => $invoice) {
                $receipts = $invoice->receipt;
                foreach ($receipts as $key => $receipt) {
                    if ($receipt->bank_account_id == $account->id) {
                        $cashFlow += $receipt->paid_amount;
                    }
                }
            }
            $account->cashFlow = $cashFlow;
            $cashFlowTotal += $cashFlow;
        }

        //
        $barKitchenTotal = $barTotal + $restTotal;
        $balance = ($barTotal + $restTotal) - $cashFlowTotal;

        return view('sales-report', compact('barTotal', 'restTotal', 'barKitchenTotal', 'accounts', 'balance', 'cashFlowTotal'));
    }
}
