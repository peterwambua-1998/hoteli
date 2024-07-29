<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class CashierReports extends Controller
{
    public function accountSales()
    {
        $day = Day::where('status', '=', 1)->first();
        if (!$day) {
            return redirect()->back()->with('error','kindly add day');
        }
        $cashAccount = new Collection();
        $bankTransferAccount = new Collection();
        $invoices = Invoice::where('day_id', '=', $day->id)->where('voided', '=', 0)->get();
        foreach ($invoices as $key => $invoice) {
            $receipts = $invoice->receipt;
            foreach ($receipts as $key => $receipt) {
                if ($receipt->payment_method == 1) {
                    $cashAccount->push($receipt);
                }

                if ($receipt->payment_method == 3) {
                    $bankTransferAccount->push($receipt);
                }
            }
        }

        return view('cashier.report', compact('cashAccount', 'bankTransferAccount'));
    }
}
