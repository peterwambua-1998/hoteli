@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('bank-account.index')}}">Bank Account</a></li>
      <li class="breadcrumb-item active" aria-current="page">Statement</li>
    </ol>
   
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
    <div class="p-2 col-md-12" style="display: flex; gap: 10px; width: 100%;">
        <button type="button" class="btn btn-outline-primary" style="width: 15%">Today</button>
        <button type="button" class="btn btn-outline-primary" style="width: 15%">This Week</button>
        <button type="button" class="btn btn-outline-primary" style="width: 15%">This Month</button>
        <div style="display: flex; gap: 10px; width: 70%">
            <div style="width: 100%">
                <label for="">To</label>
                <input type="date" class="form-control" style="width: 100%">
            </div>
            <div style="width: 100%">
                <label for="">From</label>
                <input type="date" class="form-control" style="width: 100%">
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
            <h6 class="card-title">{{$bankAccount->name}} Statement</h6>
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
                                            <li class="text-bold">Bank: {{$bankAccount->bank_name}}</li>
                                            <li class="text-bold">Statement Period:  <span id="period"></span></li>
                                        </ul>
                                    </div>
        
                                    <div class="invoice-head-right">
                                        <table>
                                            <tr>
                                                <td colspan="2" class="text-center">Opening Balance</td>
                                                <td colspan="2"  class="text-center" id="opening-balance"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="text-center">Closing Balance</td>
                                                <td colspan="2"  class="text-center" id="closing-balance"></td>
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
                                                <td class="text-bold bordered">Date</td>
                                                <td class="text-bold bordered">Ref</td>
                                                <td class="text-bold bordered">Description</td>
                                                <td class="text-bold bordered">Credit</td>
                                                <td class="text-bold bordered">Debit</td>
                                                <td class="text-bold bordered text-end">Balance</td>
                                            </tr>
                                        </thead>
                                        <tbody id="tody">
                                            
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


@push('custom-scripts')
    <script defer>
        $(document).ready( function () {

            $.ajax({
                type: "GET",
                url: "{{route('bank.statement.data', $bankAccount->id)}}",
                processData: false,
                contentType: false,
                cache: false,
                error: function(data){
                    console.log(data);
                },
                success: function (response) {
                    console.log(response);
                    $('#tody').html(response.template);
                    $('#period').text(response.startDate + ' - ' + response.endDate);
                    $('#opening-balance').text(response.openingBalance);
                    $('#closing-balance').text(response.closingBalance);
                }
            });
        });
        
    </script>
@endpush
