@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('accounts.index')}}">Quotation</a></li>
      <li class="breadcrumb-item active" aria-current="page">Details</li>
    </ol>
    <div style="display:flex; gap: 10px;">
        <form action="{{route('quotation.download.pdf')}}" method="post">
            @csrf
            <input type="hidden" name="quotation_id" value="{{$quotation->id}}">
            <button class="btn btn-outline-success" type="submit" >Download PDF</button>
        </form>
        <form action="{{route('quotation.convert')}}" method="post">
            @csrf
            <input type="hidden" name="quotation_id" value="{{$quotation->id}}">
            <button type="submit" class="btn btn-outline-primary"  href="{{route('quotation.convert', $quotation->id)}}">Generate Invoice</a>
        </form>
        <a href="{{route('accounts.show', $account->id)}}"><button class="btn btn-warning">Back</button></a>
    </div>
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
            
            <div class="invoice-wrapper" id="print-area">
                <div class="invoice">
                    <div class="invoice-container">
                        <div class="invoice-head">
                            
                            <div class="invoice-head-top" style="display: flex; justify-content: space-between;">
                                <div class="invoice-head-top-left text-start">
                                    <img src="{{asset('images/alogo.png')}}" alt="logo-image" srcset="">
                                </div>
                                <div class="invoice-head-top-right text-end" style="align-self: flex-end;">
                                    <h3>Quotation</h3>
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
                                            {{-- <li style="font-size: 12px">NO OF PAX: {{$quotation->num_of_pax}}</li> --}}
                                            {{-- <li class="text-bold">United Kingdom</li> --}}
                                        </ul>
                                    </div>
        
                                    <div class="invoice-head-right">
                                        <table>
                                            <tr>
                                                <td>Quotation No</td>
                                                <td>{{$quotation->inv_number}}</td>
                                            </tr>
                                            <tr>
                                                <td>Delivery Date</td>
                                                <td>{{$quotation->delivery_date}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="text-center">Company VAT Reg</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"  class="text-center">{{$account->vat_registration_number}}</td>
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
                                            @foreach ($quotation->items as $item)
                                            <tr class="invoice-content">
                                                <td class="bordered">{{$item->item_code}}</td>
                                                <td class="bordered">{{$item->item_description}}</td>
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
                                                <td class="bordered text-end">{{number_format($quotation->sub_total, 2)}}</td>
                                               
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td colspan="2" class="bordered text-bold">Vat (16%)</td>
                                                <td class="bordered text-end">{{number_format($quotation->tax_amount, 2)}}</td>
                                               
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td colspan="2" class="bordered text-bold">Total Amount</td>
                                                <td class="bordered text-end">{{number_format($quotation->total, 2)}}</td>
                                               
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div style="">
                            <div class="invoice-bank-info" >
                                <div style="background: #92D14F; padding: 5px;">
                                    <p class="text-bold">Quote Validity period - {{$quotation->quotation_validity}} days</p>
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

                           
                        </div>

                        {{-- policy --}}
                        <div class="mb-3">
                            <p style="font-weight: bold; font-size: 12px;">NB : FOOD AND DRINKS FROM OUTSIDE NOT ALLOWED</p>
                            <table style="border: 1px solid black; font-size: 12px;">
                                <tbody>
                                    <tr style="border-bottom: 1px solid black; border-right: 1px solid black;">
                                        <td style="border-right: 1px solid black;">SWIMMING ( COMPLIMENTARY FOR RESIDENTS)</td>
                                        <td>400</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid black; ">
                                        <td style="border-right: 1px solid black;">NATURE WALK (1-10 PAX)</td>
                                        <td>400</td>
                                    </tr>
                                    <tr>
                                        <td style="border-right: 1px solid black;">ZIPLINE</td>
                                        <td>400</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- terms --}}
                        <div class="mb-3">
                            <p style="font-weight: bold; font-size: 12px;">Terms & Conditions</p>
                            <p style="font-weight: 100; font-size: 10px;">A function's attendance must be specified by 2:00pm, 48hrs prior to the event. The number will be considered a 					
                                Guarantee & will not be subject to reduction. The hotel will set up & prepare based on the guaranteed number. 					
                                The client shall be responsible for the guarantee or the actual number of the attendees whichever is greater.					
                                If a guarantee is not given to the hotel by the specified time, the guarantee will be the number stated on the last					
                                communication. The guatantee shall be by either LPO, email or 50% deposit payment.</p>
                        </div>
                        
                        <div class="invoice-foot text-center">
        
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
  <script src="http://cdn.jsdelivr.net/g/filesaver.js"></script>
@endpush

@push('custom-scripts')
    <script defer>
        $(document).ready(function () {
            $('#download-pdf').on('click', (e) => {
                e.preventDefault();
                let data = new FormData;
                data.append('_token', '{{csrf_token()}}');
                data.append('quotation_id', '{{$quotation->id}}');
                $.ajax({
                    url: '{{route("quotation.download.pdf")}}',
                    type: 'POST',
                    contentType: false,
                    cache: false,
                    processData: false,
                    data: data,
                    error: (err) => {
                        console.log(err);
                    },
                    success: (response) => {
                       console.log(response);
                    }
                })
            })
        });
    </script>
@endpush
