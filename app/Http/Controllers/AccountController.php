<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\CreditNote;
use App\Models\CreditNoteItem;
use App\Models\DebitNote;
use App\Models\DebitNoteItem;
use App\Models\Invoice;
use App\Models\Lpo;
use App\Models\ProformaInvoice;
use App\Models\Quotation;
use App\Models\Receipt;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = Account::all();
        return view('accounts.index', compact('accounts'));
    }

    /**
     * query
     */
    public function query(Request $request)
    {
        $items = Account::where('name', 'LIKE', '%'. $request->input_query .'%')->limit(5)->get();
        return response($items);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->has('form_accommodation')) {
            if ($request->form_accommodation == 2) {
                return redirect()->route('reservations.add', $request->customer_id);
            } 
        }

        $request->validate([
            'type' => 'required',
            'name' => 'required',
            'email' => 'required',
            'telephone' => 'required',
            'location' => 'required',
        ]);

        $account = new Account();
        $account->type = $request->type;
        $account->name = $request->name;
        $account->email = $request->email;
        $account->telephone = $request->telephone;
        $account->location = $request->location;
        $account->vat_registration_number = $request->vat_registration_number;
        $account->profession = $request->profession;
        $account->id_number = $request->id_number;
        if ($account->save()) {
            if ($request->has('form_accommodation')) {
                if ($request->form_accommodation == 1) {
                    return redirect()->route('reservations.add', $account->id)->with('success', 'Record created successfully');
                }
            }
            return redirect()->back()->with('success', 'Record created successfully');
        }
        return redirect()->back()->with('error', 'System error please try again');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $account = Account::find($id);
        $proforma = ProformaInvoice::where('account_id','=', $id)->orderBy('created_at', 'desc')->get();
        $invoice = Invoice::where('account_id','=', $id)->where('voided', '=', 0)->orderBy('created_at', 'desc')->get();
        $allDebitNotes = new Collection();
        $allCreditNotes = new Collection();
        $refund = new Collection();
        foreach ($invoice as $key => $inv) {
            $debitNotes = new Collection();
            $creditNotes = new Collection();

            $inv_amount = $inv->total;
            $receipt_total = 0;
            $receipts = $inv->receipt;
            foreach ($receipts as $key => $receipt) {
                $receipt_total += $receipt->paid_amount;
            }
            $balance = $inv_amount - $receipt_total;
            $inv->balance =  $balance;
            if ($inv->debitNote->count() > 0) {
                foreach ($inv->debitNote->sortByDesc('created_at') as $key => $note) {
                    $debitNotes->push($note);
                    $allDebitNotes->push($note);
                }
            }
            if ($inv->creditNote->count() > 0) {
                foreach ($inv->creditNote->sortByDesc('created_at') as $key => $note) {
                    $creditNotes->push($note);
                    $allCreditNotes->push($note);
                }
            }

            $inv->creditN = $creditNotes;
            $inv->debitN = $debitNotes;
        }
        $quotation = Quotation::where('account_id','=', $id)->orderBy('created_at', 'desc')->get();
        $lpos = Lpo::where('account_id','=', $id)->orderBy('created_at', 'desc')->get();
        $receipts = Receipt::where('account_id','=', $id)->orderBy('created_at', 'desc')->get();
        foreach ($receipts as $key => $receipt) {
            $refunds = $receipt->refund;
            $amt = 0;
            foreach ($refunds as $key => $item) {
                $amt+=$item->amount;
            }
            $receipt->refund_amount = $amt;
        }

        $bankAccounts = BankAccount::all();
        return view('accounts.show', compact('allDebitNotes','allCreditNotes','bankAccounts','proforma', 'invoice', 'account', 'quotation', 'lpos','receipts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required',
            'name' => 'required',
            'email' => 'required',
            'telephone' => 'required',
            'location' => 'required',
        ]);

        $account = Account::find($id);
        $account->type = $request->type;
        $account->name = $request->name;
        $account->email = $request->email;
        $account->telephone = $request->telephone;
        $account->location = $request->location;
        if ($account->save()) {
            return redirect()->back()->with('success', 'Record updated successfully');
        }
        return redirect()->back()->with('error', 'System error please try again');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        //
    }

    /**
     * running balance
     */
    public function runningBalance($id)
    {
        // TODO ask if there is discount
        $account = Account::find($id);

        // invoices 
        $invoices = Invoice::where('account_id','=', $account->id)->where('voided', '=', 0)->get();

        $runningBalance = 0;

        foreach ($invoices as $invoice) {
            // invoice items 
            $invoiceItems = $invoice->items;

            // total 
            $invoiceTotal = 0;

            foreach ($invoiceItems as $item) {
                $item_total = $item->amount;
                // credit note is to add
                $creditNoteItem = CreditNoteItem::where('invoice_id','=', $invoice->id)
                ->where('item_id','=', $item->item_id)->get();

                foreach ($creditNoteItem as $key => $creditNote) {
                    $item_total -= $creditNote->amount;
                }
    
                // debit note is to add
                $debitNoteItem = DebitNoteItem::where('invoice_id','=', $invoice->id)
                ->where('item_id','=', $item->item_id)->get();

                foreach ($debitNoteItem as $key => $debitNote) {
                    $item_total += $debitNote->amount;
                }
    
                $invoiceTotal += $item_total;
            }

            // get receipts
            $receipts = $invoice->receipt;

            $receiptsTotal = 0;

            foreach ($receipts as $key => $receipt) {
                $receiptsTotal += $receipt->paid_amount;
            }

            // get running balance
            $runningBalance += $invoiceTotal - $receiptsTotal;
        }

        return response($runningBalance);
    }

    /**
     *  balance brought forward
     */
    public function balanceBroughtForward($id, $startDate)
    {
        $prevDate = date('Y-m-d', strtotime('-1 day', strtotime($startDate)));
        $account = Account::find($id);

        //invoices
        $invoices = Invoice::where('account_id','=', $account->id)->where('created_at', '<=', $prevDate)->where('voided', '=', 0)->get();
    
        $balanceBr = 0;

        foreach ($invoices as $invoice) {
            // invoice items 
            $invoiceItems = $invoice->items;

            // total 
            $invoiceTotal = 0;

            foreach ($invoiceItems as $item) {
                $item_total = $item->amount;
                // credit note is to minus
                $creditNoteItem = CreditNoteItem::where('invoice_id','=', $invoice->id)
                ->where('item_id','=', $item->item_id)->get();

                foreach ($creditNoteItem as $key => $creditNote) {
                    $item_total -= $creditNote->amount;
                }
    
                // debit note is to add
                $debitNoteItem = DebitNoteItem::where('invoice_id','=', $invoice->id)
                ->where('item_id','=', $item->item_id)->get();

                foreach ($debitNoteItem as $key => $debitNote) {
                    $item_total += $debitNote->amount;
                }

                $invoiceTotal += $item_total;
            }

            // get receipts
            $receipts = $invoice->receipt;

            $receiptsTotal = 0;

            foreach ($receipts as $key => $receipt) {
                $refund_amount = 0;
                $refunds = $receipt->refund;
                if ($refunds) {
                    foreach ($refunds as $key => $refund) {
                        $refund_amount += $refund->amount;
                    }
                }
                $receiptsTotal += $receipt->paid_amount- - $refund_amount;
            }

            // get running balance
            $balanceBr += $invoiceTotal - $receiptsTotal;
        }

        return  $balanceBr;
    }

    /**
     * statement of account
     */
    public function statementOfAccount($id)
    {
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');
        // balance br
        $balanceBr = $this->balanceBroughtForward($id, $monthStart);

        // account
        $account = Account::find($id);

        //invoices
        $invoices = Invoice::where('account_id','=', $account->id)->whereBetween('created_at', [$monthStart, $monthEnd])->where('voided', '=', 0)->get();

        $balance = 0;

        foreach ($invoices as $invoice) {
            // invoice items 
            $invoiceItems = $invoice->items;

            // total 
            $invoiceTotal = 0;

            foreach ($invoiceItems as $item) {
                $item_total = $item->amount;
                // credit note is to add
                $creditNoteItem = CreditNoteItem::where('invoice_id','=', $invoice->id)
                ->where('item_id','=', $item->item_id)->get();

                foreach ($creditNoteItem as $key => $creditNote) {
                    $item_total -= $creditNote->amount;
                }
    
                // debit note is to add
                $debitNoteItem = DebitNoteItem::where('invoice_id','=', $invoice->id)
                ->where('item_id','=', $item->item_id)->get();

                foreach ($debitNoteItem as $key => $debitNote) {
                    $item_total += $debitNote->amount;
                }

    
                $invoiceTotal += $item_total;
            }

            // get receipts
            $receipts = $invoice->receipt;

            $receiptsTotal = 0;


            foreach ($receipts as $key => $receipt) {
                
                $receiptsTotal += $receipt->paid_amount;
                // check for refund
            }

            // get running balance
            $balance += $invoiceTotal - $receiptsTotal;
        }

        $balance += $balanceBr;

        return view('accounts.statement', compact('invoices', 'account', 'balance', 'balanceBr', 'monthStart', 'monthEnd'));
    }

    /**
     * filter statement
     */
    public function queryStatement(Request $request, $id)
    {
        $monthStart = '';
        $monthEnd = '';
    }
}
