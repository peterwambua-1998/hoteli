<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($account_id)
    {
        $account = Account::find($account_id);
        $bankAccounts = BankAccount::all();
        $items = Product::all();
        return view('quotation.create', compact('account','bankAccounts','items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required',
            'quotation_validity' => 'required',
            'delivery_date' => 'required',
            'invoiced_to' => 'required',
            'vat_registration_number' => 'required',
            'sub_total' => 'required',
            'total' => 'required',
            'tax_amount' => 'required',
        ]);


        DB::beginTransaction();
        try {
            $inv = new Quotation();
            $inv->account_id = $request->account_id;
            $inv->bank_account_id = $request->bank_account_id;
            $inv->quotation_validity = $request->quotation_validity;
            $inv->inv_number = $request->inv_number;
            $inv->delivery_date = $request->delivery_date;
            $inv->to_date = $request->to_date;
            $inv->from_date = $request->from_date;
            $inv->invoiced_to = $request->invoiced_to;
            $inv->vat_registration_number = $request->vat_registration_number;
            $inv->sub_total = $request->sub_total;
            $inv->tax_amount = $request->tax_amount;
            $inv->levy = $request->levy;
            $inv->total = $request->total;
            $inv->save();

            for ($i=0; $i < count($request->item_description); $i++) { 
                $inv_item = new QuotationItem();
                $inv_item->proforma_invoice_id = $inv->id;
                $inv_item->item_id = $request->item_id[$i];
                $inv_item->item_code = $request->item_code[$i];
                $inv_item->item_description = $request->item_description[$i];
                $inv_item->quantity = $request->quantity[$i];
                $inv_item->rate = $request->rate[$i];
                $inv_item->days = $request->days[$i];
                $inv_item->amount = $request->amount[$i];
                $inv_item->save();
            }

            DB::commit();
            return redirect()->route('accounts.show', $request->account_id)->with('success', 'Record added successfully');

        } catch (\Exception $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $quotation = Quotation::find($id);
        $account = Account::find($quotation->account_id);
        $bankAccounts = BankAccount::all();
        return view('quotation.show', compact('quotation','account','bankAccounts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quotation $quotation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quotation $quotation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quotation $quotation)
    {
        //
    }

    /**
     * convertToInvoice
     */
    public function convertToInvoice(Request $request)
    {
        $proforma = Quotation::find($request->quotation_id);


        DB::beginTransaction();
        try {
            $inv = new Invoice();
            $inv->account_id = $proforma->account_id;
            $inv->quotation_id = $proforma->id;
            $inv->inv_number = $proforma->inv_number;
            $inv->delivery_date = $proforma->delivery_date;
            $inv->tax_date = date('Y-m-d');
            $inv->to_date = $proforma->to_date;
            $inv->from_date = $proforma->from_date;
            $inv->invoiced_to = $proforma->invoiced_to;
            $inv->vat_registration_number = $proforma->vat_registration_number;
            $inv->sub_total = $proforma->sub_total;
            $inv->tax_amount = $proforma->tax_amount;
            $inv->levy = $proforma->levy;
            $inv->total = $proforma->total;
            $inv->user_id = Auth::user()->id;
            $inv->save();

            foreach ($proforma->items as $key => $item) {
                $inv_item = new InvoiceItem();
                $inv_item->invoice_id = $inv->id;
                $inv_item->item_id = $item->item_id;
                $inv_item->item_code = $item->item_code;
                $inv_item->item_description = $item->item_description;
                $inv_item->quantity = $item->quantity;
                $inv_item->rate = $item->rate;
                $inv_item->days = $item->days;
                $inv_item->amount = $item->amount;
                $inv_item->save();
            }

            DB::commit();

            return redirect()->route('invoice.edit', $inv->id)->with('success', 'Invoice generated!');

        } catch (\Exception $th) {
            dd($th->getMessage(), $th->getLine());
            DB::rollBack();
            return redirect()->back()->with('error', 'System error, please try again!');
        }
    } 

    /**
     * download pdf
     */
    // public function downloadPdf(Request $request)
    // {
    //     // $file = file_get_contents(asset('images/alogo.png'));
    //     // $base64Image = base64_encode($file);
    //     $base64Image = '';

    //     $quotation = Quotation::find($request->quotation_id);
    //     $account = Account::find($quotation->account_id);
    //     $bankAccount = $quotation->bankAccount;
    //     $quotationItems = '';

    //     try {
    //         $template = QuotationPdf($quotation, $base64Image, $account, $quotation, $bankAccount);
    //         $pdf = new Mpdf();
    //         $pdf->WriteHTML("<h1>peter</h1>");
    //         return $pdf->OutputHttpDownload($account.'-quotation-'.date('Ymd').'pdf');
    //     } catch (\Exception $th) {
    //         return $th->getMessage();
    //     }
    // }
}
