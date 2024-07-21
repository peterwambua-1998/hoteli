@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('accounts.index')}}">Statment</a></li>
      <li class="breadcrumb-item active" aria-current="page">Details</li>
    </ol>
    <a href="{{route('accounts.show', $account->id)}}"><button class="btn btn-warning">Back</button></a>
</nav>



@if (Session::has('success'))
    <div class="alert alert-success" role="alert" id="success">
       {{Session::get('success')}}
    </div>
@endif

@if (Session::has('error'))
    <div class="alert alert-danger" role="alert" id="danger">
        {{Session::get('error')}}
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
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
            <h6 class="card-title">Statment of accounts </h6>
            <p class="text-muted mb-3"></p> 

            <div class="invoice-wrapper-two" id="print-area">
                <div class="invoice-two">
                    <div class="invoice-container">
                        <div class="invoice-head">
        
                            <div class="invoice-head-top" style="display: flex; justify-content: space-between;">
                                <div class="invoice-head-top-left text-start">
                                    <img src="{{asset('images/alogo.png')}}" alt="logo-image" srcset="">
                                </div>
                                <div class="invoice-head-top-right text-end" style="align-self: flex-end;">
                                    <h3>Statement</h3>
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
                                        <ul>
                                            <li class="text-bold">Invoiced To:</li>
                                            <li class="text-bold">{{$account->name}}</li>
                                            <li class="text-bold">{{$account->location}}</li>
                                            <li class="text-bold">{{$account->telephone}}</li>
                                            {{-- <li class="text-bold">United Kingdom</li> --}}
                                        </ul>
                                    </div>
        
                                    <div class="invoice-head-right">
                                        <table>
                                            <tr>
                                                <td colspan="2" class="text-center">Start Date</td>
                                                <td colspan="2"  class="text-center">{{$monthStart}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="text-center">End Date</td>
                                                <td colspan="2"  class="text-center">{{$monthEnd}}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    
                                </div>
                            </div>
                            
                            <div class="invoice-head-bottom">
                                <div class="invoice-head-left" >
                                </div>
                                <div class="invoice-head-right">
                                <table >
                                    <tr>
                                        <td class="text-center">Opening Balance</td>
                                        <td class="text-center">{{number_format($balanceBr, 2)}}</td>
                                    </tr>
                                </table>
                                </div> 
                            </div> 
                            
                            <div class="overflow-view">
                                <div class="invoice-body">
                                    
                                    <table>
                                        <thead>
                                            <tr>
                                                <td class="text-bold bordered">Date</td>
                                                <td class="text-bold bordered">Ref</td>
                                                <td class="text-bold bordered">Description</td>
                                                <td class="text-bold bordered">Credit</td>
                                                <td class="text-bold bordered">Debit</td>
                                                <td class="text-bold bordered text-end">Amount Due</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($invoices as $invoice)
                                            <tr class="invoice-content">
                                                <td class="bordered">{{$invoice->delivery_date}}</td>
                                                <td class="bordered">{{$invoice->inv_number}}</td>
                                                <td class="bordered">Invoice</td>
                                                <td class="bordered">{{number_format($invoice->total, 2)}}</td>
                                                <td class="bordered"></td>
                                                <td class="text-end bordered">{{number_format($invoice->total, 2)}}</td>
                                            </tr>
                                                

                                                @foreach ($invoice->creditNote as $credit)
                                                <tr class="invoice-content">
                                                    <td class="bordered">{{$credit->created_at}}</td>
                                                    <td class="bordered">{{$credit->note_number}}</td>
                                                    <td class="bordered">Credit note</td>
                                                    <td class="bordered">{{number_format($credit->total, 2)}}</td>
                                                    <td class="bordered"></td>
                                                    <td class="text-end bordered">- {{number_format($credit->total, 2)}}</td>
                                                </tr>
                                                @endforeach


                                                @foreach ($invoice->debitNote as $debit)
                                                <tr class="invoice-content">
                                                    <td class="bordered">{{$debit->created_at}}</td>
                                                    <td class="bordered">{{$debit->note_number}}</td>
                                                    <td class="bordered">Debit note</td>
                                                    <td class="bordered">{{number_format($debit->total, 2)}}</td>
                                                    <td class="bordered"></td>
                                                    <td class="text-end bordered"> {{number_format($debit->total, 2)}}</td>
                                                </tr>
                                                @endforeach

                                                @foreach ($invoice->receipt as $r)
                                                <tr class="invoice-content">
                                                    <td class="bordered">{{$r->created_at}}</td>
                                                    <td class="bordered">{{$r->receipt_number}}</td>
                                                    <td class="bordered">Payment</td>
                                                    <td class="bordered"></td>
                                                    <td class="bordered">{{number_format($r->paid_amount, 2)}}</td>
                                                    <td class="text-end bordered">- {{number_format($r->paid_amount, 2)}}</td>
                                                </tr>
                                                    @foreach ($r->refund as $refund)
                                                    <tr class="invoice-content">
                                                        <td class="bordered">{{$r->created_at}}</td>
                                                        <td class="bordered">{{$r->receipt_number}}</td>
                                                        <td class="bordered">Refund</td>
                                                        <td class="bordered">{{number_format($refund->amount, 2)}}</td>
                                                        <td class="bordered"></td>
                                                        <td class="text-end bordered">- {{number_format($refund->amount, 2)}}</td>
                                                    </tr>
                                                    @endforeach
                                                @endforeach
                                            @endforeach

                                            
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td colspan="2" class="bordered text-bold">Balance Due</td>
                                                <td class="bordered text-end">{{number_format($balance, 2)}}</td>
                                               
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                       
                       
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
</div>


@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush

