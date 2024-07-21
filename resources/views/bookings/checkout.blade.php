@extends('layouts.app')

@push('plugin-styles')
    <script src="{{ asset('js/intlTelInput.js') }}"></script>
    <script src="{{ asset('js/utils.js') }}"></script>
    <link href="{{ asset('css/intlTelInput.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet" />
    <style>
        .my-nav {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .issue {
            color: #ff3366;
        }
    </style>
@endpush

@section('content')

<nav class="page-breadcrumb my-nav" >
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('reservations.index')}}">Checkout</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add</li>
    </ol>
    
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('reservations.index')}}" class="btn btn-warning">Back</a>
    </div>
</nav>

@if (Session::has('success'))
<div class="alert alert-success" role="alert" id="success">
    {{Session::get('success')}}
</div>
@endif

@if (Session::has('unsuccess'))
<div class="alert alert-danger" role="alert" id="danger">
    {{Session::get('unsuccess')}}
</div> 
@endif

@if ($errors->any())
<div class="alert alert-danger">
<ul>
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
</ul>
</div>
@endif

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Checkout Customer</h4>
                <p>Customer: <span style="font-weight: bold">{{$booking->account->name}}</span></p>
                <hr>
                
                {{-- <h6>Invoices</h6>
                @foreach ($invoices as $invoice)
                    <div>
                        <p>Description: Something</p>
                        <p>Amount: <span>{{number_format($invoice->total, 2)}}</span></p>
                    </div>
                @endforeach

                <h6>Receipts</h6>
                @foreach ($invoices as $invoice)
                    <p>Inv Num: {{$invoice->inv_number}}</p>
                    @foreach ($invoice->receipt as $receipt)
                        
                    @endforeach 
                @endforeach --}}
                @if ($booking->invoices->count() > 0)
                    @foreach ($booking->invoices as $invoice)
                    <div>
                        <button class="btn btn-info" onclick="$('#print-area').print();">Print</button>
                        <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#pay-{{$invoice->id}}">Receive Payment</a>
                        
                    </div>
                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                
                                <div class="invoice-wrapper-two" id="print-area">
                                    <style>
                                        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
                                
                                        .text-center {
                                            text-align: center !important;
                                        }
                                        *,
                                        *::after,
                                        *::before {
                                            padding: 0;
                                            margin: 0;
                                            box-sizing: border-box;
                                            font-size: 13px;
                                        }
                                
                                        :root {
                                            --blue-color: #000000;
                                            --dark-color: #000000;
                                            --white-color: #fff;
                                        }
                                
                                
                                        .invoice-wrapper {
                                            min-height: 100vh;
                                            background-color: rgba(0, 0, 0, 0.1);
                                            padding-top: 20px;
                                            padding-bottom: 20px;
                                        }
                                
                                        .invoice-head-right table {
                                            border-collapse: collapse;
                                        }
                                
                                        .invoice-bank-info {
                                            padding: 30px 0 20px 0;
                                        }
                                
                                        .footer-contact-info {
                                            padding: 10px 0px 10px 0px;
                                            background-color: #fff;
                                            font-style: italic;
                                            color: #000000;
                                            margin-bottom: 10px;
                                        }
                                
                                        .invoice-head-right table td {
                                            border: 2px solid #000000;
                                            padding: 0 6px;
                                        }
                                
                                        .invoice {
                                            max-width: 850px;
                                            margin-right: auto;
                                            margin-left: auto;
                                            background-color: #fff;
                                            padding: 70px;
                                            border: 1px solid rgba(0, 0, 0, 0.12);
                                            border-radius: 5px;
                                            min-height: 920px;
                                        }
                                
                                        ul {
                                            list-style-type: none;
                                        }
                                
                                        ul li {
                                            margin: 2px 0;
                                        }
                                
                                        .invoice-head-main p{
                                            text-align: center;
                                            padding: 2px;
                                        }
                                
                                        .invoice-head-top-left img {
                                            width: 170px;
                                        }
                                
                                        .invoice-head-top-right h3 {
                                            font-weight: 500;
                                            font-size: 27px;
                                            color: var(--blue-color);
                                        }
                                
                                        .invoice-head-bottom {
                                            display: flex;
                                            justify-content: space-between;
                                        }
                                
                                        .invoice-head-middle, .invoice-head-bottom {
                                            padding: 16px 0;
                                        }
                                
                                        .invoice-body {
                                            border-radius: 4px;
                                            overflow: hidden;
                                        }
                                
                                        .invoice-body table {
                                            border-collapse: collapse;
                                            width: 100%;
                                        }
                                
                                        .invoice-body table .bordered {
                                            border: 2px solid black;
                                            padding: 5px 10px;
                                        }
                                
                                        .invoice-body table .invoice-content {
                                            vertical-align: text-top;
                                            height: fit-content;
                                        }
                                
                                        .invoice-body table thead {
                                            background-color: #92D14F;
                                        }
                                
                                        .invoice-body-info-item {
                                            display: grid;
                                            grid-template-columns: 80% 20%;
                                        }
                                
                                        .invoice-body-info-item .info-item-td {
                                            padding: 8px;
                                            
                                        }
                                
                                        .invoice-btns {
                                            margin-top: 20px;
                                            display: flex;
                                            justify-content: center;
                                        }
                                
                                        .invoice-btn {
                                            padding: 3px 9px;
                                            color: var(--dark-color);
                                            font-family: inherit;
                                            border: 1px solid rgba(0, 0, 0, 0.1);
                                            cursor: pointer;
                                        }
                                
                                        @media screen and (max-width: 576px) {
                                            .invoice-head-bottom {
                                                display: grid;
                                            }
                                
                                            .invoice-head-right table{
                                            margin: 10px 0 0 0;
                                            }
                                
                                            .overflow-view {
                                                overflow-x: scroll;
                                            }
                                
                                            .invoice-body {
                                                min-width: 600px;
                                            }
                                        }
                                
                                        .invoice-wrapper {
                                        background-color: rgba(0, 0, 0, 0.1);
                                        padding-top: 20px;
                                        padding-bottom: 20px;
                                        }
                                
                                        .invoice-wrapper-two {
                                        background-color: #fff;
                                        padding-top: 20px;
                                        padding-bottom: 20px;
                                        }
                                
                                        .invoice-head-right table {
                                        border-collapse: collapse;
                                        }
                                
                                        .invoice-bank-info {
                                        padding: 30px 0 20px 0;
                                        }
                                
                                        .footer-contact-info {
                                        padding: 10px 0px 10px 0px;
                                        background-color: #92D14F;
                                        font-style: italic;
                                        color: #000000;
                                        margin-bottom: 10px;
                                        }
                                
                                        .invoice-head-right table td {
                                        border: 2px solid #000000;
                                        padding: 0 6px;
                                        }
                                
                                        .invoice {
                                        max-width: 850px;
                                        margin-right: auto;
                                        margin-left: auto;
                                        background-color: white;
                                        padding: 70px;
                                        border: 1px solid rgba(0, 0, 0, 0.12);
                                        border-radius: 5px;
                                        }
                                
                                        .invoice-two {
                                        max-width: 850px;
                                        margin-right: auto;
                                        margin-left: auto;
                                        background-color: white;
                                        padding: 70px;
                                        border: 1px solid rgba(0, 0, 0, 0.12);
                                        border-radius: 5px;
                                        }
                                
                                        .invoice-head-main p{
                                        text-align: center;
                                        padding: 2px;
                                        }
                                
                                        .invoice-head-top-left img {
                                        width: 170px;
                                        }
                                
                                        .invoice-head-top-right h3 {
                                        font-weight: 500;
                                        font-size: 27px;
                                        color: var(--blue-color);
                                        }
                                
                                        .invoice-head-bottom {
                                        display: flex;
                                        justify-content: space-between;
                                        }
                                
                                        .invoice-head-middle, .invoice-head-bottom {
                                        padding: 16px 0;
                                        }
                                
                                        .invoice-body {
                                        border-radius: 4px;
                                        overflow: hidden;
                                        }
                                
                                        .invoice-body table {
                                        border-collapse: collapse;
                                        width: 100%;
                                        }
                                
                                        .invoice-body table .bordered {
                                        border: 2px solid black;
                                        padding: 5px 10px;
                                        }
                                
                                        .invoice-body table .invoice-content {
                                        vertical-align: text-top;
                                        }
                                
                                        .invoice-body table thead {
                                        background-color: #92D14F;
                                        }
                                
                                        .invoice-body-info-item {
                                        display: grid;
                                        grid-template-columns: 80% 20%;
                                        }
                                
                                        .invoice-body-info-item .info-item-td {
                                        padding: 8px;
                                        
                                        }
                                
                                        .invoice-btns {
                                        margin-top: 20px;
                                        display: flex;
                                        justify-content: center;
                                        }
                                
                                        .invoice-btn {
                                        padding: 3px 9px;
                                        color: var(--dark-color);
                                        font-family: inherit;
                                        border: 1px solid rgba(0, 0, 0, 0.1);
                                        cursor: pointer;
                                        }
                                
                                        @media screen and (max-width: 576px) {
                                        .invoice-head-bottom {
                                            display: grid;
                                        }
                                
                                        .invoice-head-right table{
                                            margin: 10px 0 0 0;
                                        }
                                
                                        .overflow-view {
                                            overflow-x: scroll;
                                        }
                                
                                        .invoice-body {
                                            min-width: 600px;
                                        }
                                        }
                                
                                    </style>
                                    <div class="invoice-two">
                                        <div class="invoice-container">
                                            <div class="invoice-head">
                            
                                                <div class="invoice-head-top" style="display: flex; justify-content: space-between;">
                                                    {{-- <div class="invoice-head-top-left text-start">
                                                        <img src="{{asset('images/alogo.png')}}" alt="logo-image" srcset="">
                                                    </div> --}}
                                                    <div class="invoice-head-top-right text-end" style="align-self: flex-end;">
                                                        <h3>Invoice</h3>
                                                    </div>
                                                </div>
                            
                                                <div class="hr"></div>
                                                <div class="invoice-head-middle">
                            
                                                    <div class="invoice-head-main">
                                                        <p>KISIMANI ECO RESORT AND SPA LTD</p>
                                                        <p>P.O Box 56049-00200 Nairobi, Kenya</p>
                                                        <p>Tel: 0715-120-280, 0733-808-200</p>
                                                    </div>
                                        
                                                    <div class="hr"></div>
                                                    <div class="invoice-head-bottom">
                            
                                                        <div class="invoice-head-left">
                                                            @if ($invoice->account_id)
                                                            <ul>
                                                                <li class="text-bold">Invoiced To:</li>
                                                                <li class="text-bold">{{$invoice->customer->name}}</li>
                                                                <li class="text-bold">{{$invoice->customer->location}}</li>
                                                                <li class="text-bold">{{$invoice->customer->telephone}}</li>
                                                                {{-- <li class="text-bold">United Kingdom</li> --}}
                                                            </ul>
                                                            @endif
                                                        </div>
                            
                                                        <div class="invoice-head-right">
                                                            <table>
                                                                <tr>
                                                                    <td>Tax Date</td>
                                                                    <td>{{$invoice->tax_date}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Invoice No</td>
                                                                    <td>{{$invoice->inv_number}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Delivery Date</td>
                                                                    <td>{{$invoice->delivery_date}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2" class="text-center">Company VAT Reg</td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2"  class="text-center">{{$invoice->customer->vat_registration_number}}</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                            
                                                <div class="overflow-view">
                                                    <div class="invoice-body">
                                                        <table>
                                                            <thead>
                                                                <tr>
                                                                    <td class="text-bold bordered">Item Code</td>
                                                                    <td class="text-bold bordered">Item Description</td>
                                                                    <td class="text-bold bordered">Qty</td>
                                                                    <td class="text-bold bordered">Rate</td>
                                                                    <td class="text-bold bordered">Days</td>
                                                                    <td class="text-bold bordered text-end">Amount</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($invoice->items as $item)
                                                                <tr class="invoice-content">
                                                                    <td class="bordered">{{$item->item_code}}</td>
                                                                    <td class="bordered" id="dropdownMenuButton-{{$item->id}}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        {{$item->item_description}}
                                                                        
                                                                    </td>
                                                                    <td class="bordered">{{$item->quantity}}</td>
                                                                    <td class="bordered">{{$item->rate}}</td>
                                                                    <td class="bordered">{{$item->days}}</td>
                                                                    <td class="text-end bordered">{{number_format($item->amount, 2)}}</td>
                                                                </tr>
                                                                @endforeach
                    
                                                                <tr>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td colspan="2" class="bordered text-bold">Sub Total</td>
                                                                    <td class="bordered text-end">{{number_format($invoice->sub_total, 2)}}</td>
                                                                
                                                                </tr>
                                                                <tr>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td colspan="2" class="bordered text-bold">Vat (16%)</td>
                                                                    <td class="bordered text-end">{{number_format($invoice->tax_amount, 2)}}</td>
                                                                
                                                                </tr>
                                                                <tr>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td colspan="2" class="bordered text-bold">Total Amount</td>
                                                                    <td class="bordered text-end">{{number_format($invoice->total, 2)}}</td>
                                                                
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            @foreach ($bankAccounts as $bankAccount)
                                            <div class="invoice-bank-info">
                                                <p>Bank: <span class="text-bold">{{$bankAccount->bank_name}}</span></p>
                                                <p>Account Name: <span class="text-bold">{{$bankAccount->account_name}}</span></p>
                                                <p>Account No: <span class="text-bold">{{$bankAccount->account_number}}</span></p>
                                                <p>Branch: <span class="text-bold">{{$bankAccount->branch}}</span></p>
                                            </div>
                                            @endforeach
                                        
                                            <div class="invoice-foot text-center mt-3">
                                                <div class="footer-contact-info">
                                                    <p><span class="text-bold">Email; kisimaniresort@gmail.com, resortisiolo@gmail.com.</span></p>
                                                    <p><span class="text-bold">Blending Nature With Modern Hospitality</span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                @else
                    @if ($booking->acc_paid_by == 2)
                        <?php $acc = App\Models\Account::where('id','=', $booking->company_id)->first()  ?>
                       <p>Check in under Company @if ($acc) ({{$acc->name}}) @endif</p> 
                    @endif
                    <form action="{{route('checkout.under.company')}}" method="post">
                        @csrf
                        <input type="hidden" name="booking_id" value="{{$booking->id}}">
                        <button type="submit" class="btn btn-success">Checkout</button>

                    </form>
                @endif


            </div>
        </div>
    </div>
</div>

@if ($booking->invoices->count() > 0)
    @include('bookings.receipt')
@endif

@foreach ($invoices as $order)
<div class="modal fade" id="pay-{{$order->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('reservation.invoice.pay')}}" method="post" id="cashier-form">
        @csrf
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background: green; color: #fff;">
                    <h5 class="modal-title" id="exampleModalLabel">Invoice payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body row">
                    <div class="mb-3 col-md-12" style="border-bottom: 1px solid rgba(0, 0, 0, 0.21); padding-bottom: 5px;">
                        <h5 class="mb-2">Balance: <span class="ml-3">{{$order->bal}}</span></h5>
                    </div>

                    <div class="mb-4 col-md-3">
                        <label for="account_id" class="form-label">Account</label>
                        <input type="text" class="form-control" value="{{$order->customer->name}}" readonly>
                        <input type="hidden" name="account_id" value="{{$booking->account_id}}">
                    </div>

                    <div class="mb-4 col-md-3">
                        <label class="form-label">Payment method</label>
                        <select class="form-select  payment_method" name="payment_method" >
                            <option selected value="0">Choose payment method...</option>
                            <option value="1">Cash</option>
                            <option value="3">Bank transfer</option>
                            <option value="4">Cheque</option>
                            <option value="5">Package</option>
                            <option value="6">Complimentary</option>
                        </select>
                        <span class="text-danger payment_method_error"></span>

                    </div>

                    <div class="mb-4 ref_div col-md-3">
                        <label for="payment_code" class="form-label">Reference number</label>
                        <input type="text" class="form-control payment_code" name="payment_code" id="payment_code" autocomplete="off" placeholder="Ex: RF121212">
                        <span class="text-danger payment_code_error"></span>
                    
                    </div>

                    <div class="mb-4 account_div col-md-3">
                        <label class="form-label">Bank Account</label>
                        <select class="form-select mb-3 bank_account_id" name="bank_account_id">
                        <option selected value="0">Choose bank account...</option>
                        @foreach ($bankAccounts as $bankAccount)
                        <option value="{{$bankAccount->id}}">{{$bankAccount->bank_name}}</option>
                        @endforeach
                        
                        </select>
                        <span class="text-danger bank_account_error"></span>

                    </div>

                    <div class="mb-4 amt_div col-md-3" >
                        <label for="amount" class="form-label">Amount</label>
                        <input type="text" class="form-control amount" name="amount" id="amount" autocomplete="off" placeholder="Ex: 5000">
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="order_id" value="{{$order->id}}">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success save-btn">Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endforeach

    

@endsection

@push('plugin-scripts')
<script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script src="{{ asset('assets/js/dropify.js') }}"></script>
<script>
    

    $.fn.extend({
        print: function() {
            var frameName = 'printIframe';
            var doc = window.frames[frameName];
            if (!doc) {
                $('<iframe>').hide().attr('name', frameName).appendTo(document.body);
                doc = window.frames[frameName];
            }
            doc.document.body.innerHTML = this.html();
            doc.window.print();
            return this;
        }
    });
    
    $(document).ready( function () {
        $('.payment_method').each((index, element) => {
        $(element).on('change', () => {
            let payment_method = $(element).find(':selected').val();
            let parent = $(element).parent();

            let ref_div = $(parent).next();
            let account_div = $(parent).next().next();
            let amt_div = $(parent).next().next().next();

            if (payment_method == 5 || payment_method == 6 || payment_method == 7) {
                $(ref_div).hide();
                $(account_div).hide();
                $(amt_div).hide();
            } else {
                $(ref_div).show();
                $(account_div).show();
                $(amt_div).show();
            } 
        })
    });

    $('.payment_method').each((i, element) => {
        $(element).on('change', () => {
            let parent = $(element).parent().parent();
            let payment_code = $(parent).find('.payment_code');
            if ($(element).find(':selected').val() == 1) {
                $(payment_code).val('0000');
            } else {
                $(payment_code).val();
                
            }
        })
    });

    $('.save-btn').each((i, element) => {
        $(element).on('click', (e) => {
            e.preventDefault();

            let form = $(element).parent().parent().parent().parent();


            let parent = $(element).parent().parent().find('.modal-body');
            let account_id = $(parent).find('.account_id');
            
            let payment_method = $(parent).find('.payment_method');
            let payment_method_error = $(parent).find('.payment_method_error');

            let payment_code = $(parent).find('.payment_code');
            let payment_code_error = $(parent).find('.payment_code_error');

            // bank account input and error span 
            let bank_account_id = $(parent).find('.bank_account_id');
            let bank_account_error = $(parent).find('.bank_account_error');

            let amount = $(parent).find('.amount');
            let amount_error = $(parent).find('.amount_error');

            //console.log(payment_method, payment_code, bank_account_id, amount);
            // amount should be > 0 if its cash cheque and bank transfer
            if ($(payment_method).find(':selected').val() == 0) {
                $(payment_method).focus();
                $(payment_method_error).text('field required');
                return;
            } else {
                $(payment_method_error).text('');
            }
            
            if ($(payment_method).val() == 1) {

                if ($(bank_account_id).find(':selected').val() == 0) {
                    $(bank_account_id).focus();
                    $(bank_account_error).text('field required');
                    return;
                } else {
                    $(bank_account_error).text('');
                }

                if(!$(amount).val()) {
                    $(amount).focus();
                    $(amount_error).text('field required');
                    return;
                } else {
                    $(amount_error).text('');

                }

                $(form).submit();
                
            }
            

            if ($(payment_method).val() == 3 || $(payment_method).val() == 4) {
                
                if (!$(payment_code).val()) {
                    $(payment_code).focus();
                    $(payment_code_error).text('field required');
                    return;
                } else {
                    $(payment_code_error).text('');
                }

                if ($(bank_account_id).find(':selected').val() == 0) {
                    $(bank_account_id).focus();
                    $(bank_account_error).text('field required');
                    return;
                } else {
                    $(bank_account_error).text('');
                }

                if(!$(amount).val()) {
                    $(amount).focus();
                    $(amount_error).text('field required');
                    return;
                } else {
                    $(amount_error).text('');
                }

                $(form).submit();
                
            }

            if ($(payment_method).val() == 5 || $(payment_method).val() == 6 || $(payment_method).val() == 7) {
                $(form).submit();
                
            }
            
        })
    })
    })
    
</script>
@endpush