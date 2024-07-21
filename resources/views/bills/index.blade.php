@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('accounts.index')}}">Accounts</a></li>
      <li class="breadcrumb-item active" aria-current="page">Details</li>
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

{{-- <div class="row mt-2">
  <div class="col-md-12">
    <div style="display: flex; justify-content: space-between;">
      <p>Running Balance: <span id="running_balance"></span></p>
      <a href="{{route('accounts.statement', $account->id)}}" class="btn btn-success">statement</a>
    </div>
  </div>
</div> --}}


<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
            <h6 class="card-title">Bills Table</h6>
            <p class="text-muted mb-3"></p> 
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTableExample3">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Bill Ref No</th>
                            <th>DebitNote</th>
                            <th>CreditNote</th>
                            <th>Subtotal</th>
                            <th>Vat</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $number = 1; ?>
                        @foreach ($bills as $pr)
                        <?php
                            $creditNotess = App\Models\BillCreditNote::where('bill_id', '=', $pr->id)->get();
                            $debitNotess = App\Models\BillDebitNote::where('bill_id', '=', $pr->id)->get();
                        ?>
                        <tr>
                            <td>{{$number}}<?php $number++; ?></td>
                            <td>{{$pr->bill_number}}</td>
                            <td><input type="checkbox" class="form-check-input" id="checkInlineCheckedDisabled" disabled="" @if(count($creditNotess) > 0) checked="" @endif></td>
                            <td><input type="checkbox" class="form-check-input" id="checkInlineCheckedDisabled" disabled=""  @if(count($debitNotess) > 0) checked="" @endif></td>
                            <td>{{number_format($pr->sub_total, 2)}}</td>
                            <td>{{number_format($pr->vat, 2)}}</td>
                            <td>{{number_format($pr->total, 2)}}</td>
                            <td style="display: flex; gap: 20px;">
                                <a style="color:green" href="{{route('bill.show', $pr->id)}}">show</a>
                                {{-- <a style="color:blue" href="#" data-bs-toggle="modal" data-bs-target="#receipt-{{$pr->id}}">Receipt</a> --}}
                                
                                {{-- <a href="#" data-bs-toggle="modal" data-bs-target="#withholding-{{$pr->id}}">withholding</a> --}}
                                {{-- <a href="#">Download</a> --}}
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


@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
  <script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>
@endpush

@push('custom-scripts')
  <script src="{{ asset('assets/js/dropify.js') }}"></script>
  <script>
  $(document).ready( function () {
    $('#dataTableExample1').DataTable({
        language: { searchPlaceholder: "Search records", search: "",},
    });
   

    // const formatter = new Intl.NumberFormat('en-US', {
    //     style: 'decimal',
    //     minimumFractionDigits: 2,
    //     maximumFractionDigits: 2,
    // });

    // $.ajax({
    //   type: 'get',
    //   url: '/running-balance/',
    //   processData: false,
    //   cache: false,
    //   contentType: false,
    //   error: (err) => {
    //     console.log(err);
    //   },
    //   success: (response) => {
    //     console.log(response);
    //     $('#running_balance').text(formatter.format(response))
    //   }
    // })
    
  });

</script>
@endpush