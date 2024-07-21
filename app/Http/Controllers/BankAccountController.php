<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\BillReceipt;
use App\Models\Invoice;
use App\Models\Receipt;
use Illuminate\Http\Request;
use stdClass;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // TODO add logic for bill and bill refund
    public function index()
    {
        $bankAccounts = BankAccount::orderBy('created_at', 'desc')->get();
        foreach ($bankAccounts as $key => $bankAccount) {
            $amount = 0;
            $refund_amount = 0;
            
            $receipts = $bankAccount->receipt;
            foreach ($receipts as $key => $receipt) {
                $amount += $receipt->paid_amount;
                foreach ($receipt->refund as $key => $refund) {
                    $refund_amount += $refund->amount;
                }
            }

            $billReceipt = $bankAccount->billReceipt;
            $bill_amount = 0;
            $bill_refund_amount = 0;
            foreach ($billReceipt as $key => $receipt) {
                $bill_amount += $receipt->paid_amount;
                foreach ($receipt->refund as $key => $refund) {
                    $bill_refund_amount += $refund->amount;
                }
            }
            
            $bankAccount->total_amount = $amount + $bankAccount->available_balance - $refund_amount;

            $bankAccount->total_amount = $bankAccount->total_amount - $bill_amount  + $bill_refund_amount;
        }

        return view('bank-accounts.index', compact('bankAccounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bank_name' => 'required',
            'account_name' => 'required',
            'account_number' => 'required',
            'branch' => 'required',
        ]);

        $bankAccount = new BankAccount();
        $bankAccount->bank_name = $request->bank_name;
        $bankAccount->account_name = $request->account_name;
        $bankAccount->account_number = $request->account_number;
        $bankAccount->branch = $request->branch;
        $bankAccount->available_balance = $request->available_balance;
        if ($bankAccount->save()) {
            return redirect()->back()->with('success', 'Record created successfully');
        }
        return redirect()->back()->with('error', 'System error please try again!');
    }

   
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'bank_name' => 'required',
            'account_name' => 'required',
            'account_number' => 'required',
            'branch' => 'required',
        ]);

        $bankAccount = BankAccount::find($id);
        $bankAccount->bank_name = $request->bank_name;
        $bankAccount->account_name = $request->account_name;
        $bankAccount->account_number = $request->account_number;
        $bankAccount->branch = $request->branch;
        if ($bankAccount->update()) {
            return redirect()->back()->with('success', 'Record created successfully');
        }
        return redirect()->back()->with('error', 'System error please try again!');
    }


    /**
     * 
     */
    public function analytics()
    {
        // TODO Refund for receipt
        $analysis = [];
        $bankAccounts = BankAccount::orderBy('created_at', 'desc')->get();
        foreach ($bankAccounts as $key => $bankAccount) {
            $amount =  0;
            $refund_amount =  0;
            
            $receipts = $bankAccount->receipt;
            foreach ($receipts as $key => $receipt) {
                $amount += $receipt->paid_amount;
                foreach ($receipt->refund as $key => $refund) {
                    $refund_amount += $refund->amount;
                }
            }
            
            $billReceipt = $bankAccount->billReceipt;
            $bill_amount = 0;
            $bill_refund_amount = 0;
            foreach ($billReceipt as $key => $receipt) {
                $bill_amount += $receipt->paid_amount;
                foreach ($receipt->refund as $key => $refund) {
                    $bill_refund_amount += $refund->amount;
                }
            }
            
            $bankAccount->total_amount = $amount + $bankAccount->available_balance - $refund_amount;

            $bankAccount->total_amount = $bankAccount->total_amount - $bill_amount  + $bill_refund_amount;

            $std = new stdClass;
            $std->bank_name = $bankAccount->bank_name;
            $std->amount = $bankAccount->total_amount;

            array_push($analysis, $std);
        }
        return response($analysis);
    }

    /**
     * bank statment show
     */
    public function bankStatementView($id)
    {
        $bankAccount = BankAccount::find($id);
        return view('bank-accounts.statement', compact('bankAccount'));
    }

    /**
     * statement
     * 
     * show: Bill receipts
     * show: invoice receipt
     */
    public function bankStatement($id)
    {
        $bankAccount = BankAccount::find($id);
        $prevDate = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-01'))));

        // get month start and end date
        $monthStart = date('Y-m-d');
        $monthEnd = date('Y-m-t');
        $monthStartIncrease = date('Y-m-01');

        // get balance brought forward

        $balanceBr = 0;

        $prevInvoiceReceipt = $bankAccount->available_balance;
        $prevBillReceipt = 0;

        $invoiceReceipt = $bankAccount->receipt->where('created_at','<=', $prevDate);
        $billReceipt = $bankAccount->billReceipt->where('created_at','<=', $prevDate);

        foreach ($invoiceReceipt as $key => $receipt) {
            $prevInvoiceReceipt += $receipt->paid_amount;
        }

        foreach ($billReceipt as $key => $receipt) {
            $prevBillReceipt += $receipt->paid_amount;
        }

        $balanceBr = $prevInvoiceReceipt - $prevBillReceipt;

        $balance = $balanceBr;

        $closingBalance = 0;

        $template = '';

        while ($monthStartIncrease <= $monthEnd) {
            $billReceipt = BillReceipt::where('bank_account_id','=', $bankAccount->id)->whereDate('created_at', $monthStartIncrease)->get();
            $invoiceReceipt = Receipt::where('bank_account_id','=', $bankAccount->id)->whereDate('created_at', $monthStartIncrease)->get();

            foreach ($invoiceReceipt as $key => $receipt) {
                $refund_amount =  0;
                foreach ($receipt->refund as $key => $refund) {
                    $refund_amount += $refund->amount;
                }
                $balance += $receipt->paid_amount;
                $closingBalance = $balance;

                $template.= 
                "
                <tr>
                    <td class='bordered'>$monthStartIncrease</td>
                    <td class='bordered'>$receipt->receipt_number</td>
                    <td class='bordered'>Revenue</td>
                    <td class='bordered'></td>
                    <td class='bordered'>$receipt->paid_amount</td>
                    <td class='bordered'>$balance</td>
                </tr>
                ";

                $balance -= $refund_amount;

                foreach ($receipt->refund as $key => $refund) {
                    $closingBalance = $balance;

                    $template.= 
                    "
                    <tr>
                        <td class='bordered'>$monthStartIncrease</td>
                        <td class='bordered'>$receipt->receipt_number</td>
                        <td class='bordered'>Revenue Refund</td>
                        <td class='bordered'>$refund->amount</td>
                        <td class='bordered'></td>
                        <td class='bordered'>$balance</td>
                    </tr>
                    ";
                }

                
            }

            foreach ($billReceipt as $key => $receipt) {
                // TODO add logic for bill and bill refund
                $refund_amount =  0;
                foreach ($receipt->refund as $key => $refund) {
                    $refund_amount += $refund->amount;
                }
                $balance -= $receipt->paid_amount;
                $closingBalance = $balance;

                $template .= 
                "
                <tr>
                    <td class='bordered'>$monthStartIncrease</td>
                    <td class='bordered'>$receipt->receipt_number</td>
                    <td class='bordered'>Bill</td>
                    <td class='bordered'>$receipt->paid_amount</td>
                    <td class='bordered'></td>
                    <td class='bordered'>$balance</td>
                </tr>
                ";
                $balance += $refund_amount;
                foreach ($receipt->refund as $key => $refund) {
                    $closingBalance = $balance;
                    $template .= 
                    "
                    <tr>
                        <td class='bordered'>$monthStartIncrease</td>
                        <td class='bordered'>$receipt->receipt_number</td>
                        <td class='bordered'>Bill Refund</td>
                        <td class='bordered'></td>
                        <td class='bordered'>$refund->amount</td>
                        <td class='bordered'>$balance</td>
                    </tr>
                    ";
                }
               
            }

            $monthStartIncrease = date('Y-m-d', strtotime('+1 day', strtotime($monthStartIncrease)));
        }

        return response([
            'template'=>$template, 
            'closingBalance' => $closingBalance, 
            'openingBalance' => $balanceBr,
            'startDate' => $monthStart,
            'endDate' => $monthEnd,
            'balance' => $balance
        ]);
    }
}
