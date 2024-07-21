@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    
@endpush
@section('content')

<?php 
$refund = false;
foreach ($receipt->refundRequest as $key => $item) {
  if ($item->approved == 1) {
    $refund = true;
  }
}

?>

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('accounts.index')}}">Receipt</a></li>
      <li class="breadcrumb-item active" aria-current="page">Details</li>
    </ol>
    <div style="display: flex; gap: 10px; width: 50%;">
        <a class="btn btn-primary" style="width: 100%;" href="#"  data-bs-toggle="modal" data-bs-target="#refundRequest"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px;" name="add-circle-outline"></ion-icon> Refund Request</a>
        @if ($refund)
        <a class="btn btn-info" style="width: 100%;" href="#"  data-bs-toggle="modal" data-bs-target="#refund"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px;" name="add-circle-outline"></ion-icon> Refund</a>
        @endif
        <a class="btn btn-success" style="width: 100%;" href="#"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px;" name="add-circle-outline"></ion-icon> Print</a>
        <a  href="{{route('accounts.show', $account->id)}}" class="btn btn-warning">Back</a>
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


<div class="form-check form-check-inline">
  <input type="checkbox" class="form-check-input" id="checkInlineCheckedDisabled" disabled=""  @if($receipt->withholding == 1) checked="" @endif>
  <label class="form-check-label" for="checkInlineCheckedDisabled">
    Withholding
  </label>
</div>


<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
          
          <div class="invoice-wrapper-two" id="print-area">
              <div class="invoice-two">
                  <div class="invoice-container">
                      <div class="invoice-head">
      
                          <div class="invoice-head-top" style="display: flex; justify-content: space-between;">
                            <div class="invoice-head-top-left text-start">
                                <img src="{{asset('images/alogo.png')}}" alt="logo-image" srcset="">
                            </div>
                            <div class="invoice-head-top-right text-end" style="align-self: flex-end;">
                                <h3>Receipt</h3>
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
                                          <li class="text-bold">Billed To:</li>
                                          <li class="text-bold">{{$account->name}}</li>
                                          <li class="text-bold">{{$account->location}}</li>
                                          <li class="text-bold">{{$account->telephone}}</li>
                                          {{-- <li class="text-bold">United Kingdom</li> --}}
                                      </ul>
                                  </div>
      
                                  <div class="invoice-head-right">
                                      <table>
                                          <tr>
                                              <td>Receipt No</td>
                                              <td>{{$receipt->receipt_number}}</td>
                                          </tr>
                                          <tr>
                                            <td>Payment Methods</td>
                                            <td>
                                              @if ($receipt->payment_method == 1)
                                                  Cash
                                              @endif

                                              @if ($receipt->payment_method == 2)
                                                  Mpesa
                                              @endif

                                              @if ($receipt->payment_method == 3)
                                                  Bank Transfer
                                              @endif

                                              @if ($receipt->payment_method == 4)
                                                  Cheque
                                              @endif
                                            </td>
                                          </tr>
                                          <tr>
                                            <td>Payment Account</td>
                                            <td>
                                              @if ($receipt->payment_method == 1)
                                                  Cash
                                              @endif

                                              @if ($receipt->payment_method == 2)
                                                  Mpesa
                                              @endif

                                              @if ($receipt->payment_method == 3)
                                                  {{$receipt->bankAccount->bank_name}}
                                              @endif

                                              @if ($receipt->payment_method == 4)
                                                  Cheque
                                              @endif
                                            </td>
                                          </tr>
                                          <tr>
                                              <td>Reference No</td>
                                              <td>{{$receipt->payment_code}}</td>
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
                                              <td class="text-bold bordered">Subtotal</td>
                                              <td class="text-bold bordered">Vat</td>
                                              <td class="text-bold bordered">Total</td>
                                              <td class="text-bold bordered">Paid</td>
                                              <td class="text-bold bordered">Balance</td>
                                          </tr>
                                      </thead>
                                      <tbody>
                                          <tr class="invoice-content">
                                              <td class="bordered">{{number_format($receipt->sub_total, 2)}}</td>
                                              <td class="bordered">{{number_format($receipt->tax_amount, 2)}}</td>
                                              <td class="bordered">{{number_format($receipt->amount, 2)}}</td>
                                              <td class="bordered">{{number_format($receipt->paid_amount, 2)}}</td>
                                              <td class="bordered">{{number_format($receipt->balance, 2)}}</td>
                                          </tr>
                                      </tbody>
                                  </table>
                              </div>
                          </div>
                      </div>
                      <div class="invoice-foot text-center mt-5">
      
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

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
          <h6 class="card-title">Refund Requests</h6>
          <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTableExample">
                <thead>
                    <tr>
                      <th>Status</th>
                      <th>Amount</th>
                      <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($receipt->refundRequest as $item)
                <tr>
                  <td>
                    @if ($item->approved == 0)
                        <span class="badge bg-danger">pending</span>
                    @endif

                    @if ($item->approved == 1)
                    <span class="badge bg-success">approved</span>
                    @endif

                    @if ($item->approved == 2)
                    <span class="badge bg-warning">rejected</span>
                    @endif
                  </td>
                  <td>{{number_format($item->amount, 2)}}</td>
                  <td>
                    @if ($item->approved == 0)
                        n/a
                    @endif

                    @if ($item->approved == 1)
                        n/a
                    @endif

                    @if ($item->approved == 2)
                        {{$item->reason}}
                    @endif
                  </td>
                </tr>
                @endforeach
                </tbody>
            </table>
          </div>
        </div>
    </div>
  </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="{{route('receipt.withHolding')}}" method="post">
      @csrf
      <input type="hidden" name="receipt_id" value="{{$receipt->id}}">
      <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Add Withholding</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
          </div>
          <div class="modal-body">
              <div class="mb-3">
                  <label class="form-label">Add Withholding</label>
                  <select class="form-select mb-3" name="withholding">
                    <option selected value="5">Add Withholding...</option>
                    <option @if($receipt->withholding == 1) selected @endif value="1">Yes</option>
                    <option @if($receipt->withholding == 0) selected @endif value="0">No</option>
                  </select>
                </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save changes</button>
          </div>
      </div>
      </div>
  </form>
</div>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
          <h6 class="card-title">Refunds</h6>
          <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTableExample">
                <thead>
                    <tr>
                      <th style="width: 50%">Amount</th>
                      <th tyle="width: 50%">Date</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($receipt->refund as $item)
                <tr>
                  <td>{{number_format($item->amount, 2)}}</td>
                  <td>
                    {{$item->created_at}}
                  </td>
                </tr>
                @endforeach
                </tbody>
            </table>
          </div>
        </div>
    </div>
  </div>
</div>

<div class="modal fade" id="refundRequest" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="{{route('refund-request.store')}}" method="post">
  @csrf
    <input type="hidden" name="receipt_id" value="{{$receipt->id}}" />
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Refund Request</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="amount" class="form-label">Enter Amount</label>
            <input type="text" class="form-control" id="amount" name="amount" autocomplete="off" placeholder="Ex: 200">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </form>
</div>

<div class="modal fade" id="refund" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="{{route('refund.store')}}" method="post">
  @csrf
    <input type="hidden" name="receipt_id" value="{{$receipt->id}}" />
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Refund</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="amount" class="form-label">Enter Amount</label>
            <input type="text" class="form-control" id="amount" name="amount" autocomplete="off" placeholder="Ex: 200">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush

