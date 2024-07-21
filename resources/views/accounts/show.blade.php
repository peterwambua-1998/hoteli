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

<div class="row mt-2">
  <div class="col-md-12">
    <div style="display: flex; justify-content: space-between;">
      <p>Running Balance: <span id="running_balance"></span></p>
      <a href="{{route('accounts.statement', $account->id)}}" class="btn btn-success">statement</a>
    </div>
  </div>
</div>


<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
            <h6 class="card-title">Account:  {{$account->name}}</h6>
            <p class="text-muted mb-3"></p> 
            <ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
                @if ($account->type == 1)
                <li class="nav-item">
                  <a class="nav-link active" id="home-line-tab" data-bs-toggle="tab" data-bs-target="#home" role="tab" aria-controls="home" aria-selected="true">LPO</a>
                </li>
                @endif
                <li class="nav-item">
                  <a class="nav-link" id="profile-line-tab" data-bs-toggle="tab" data-bs-target="#profile" role="tab" aria-controls="profile" aria-selected="false">Quotations</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="contact-line-tab" data-bs-toggle="tab" data-bs-target="#contact" role="tab" aria-controls="contact" aria-selected="false">Proforma</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="note-line-tab" data-bs-toggle="tab" data-bs-target="#note" role="tab" aria-controls="note" aria-selected="false">Invoices</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="receipt-line-tab" data-bs-toggle="tab" data-bs-target="#receipt" role="tab" aria-controls="receipt" aria-selected="false">Receipts</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="debitNotes-line-tab" data-bs-toggle="tab" data-bs-target="#debitNotes" role="tab" aria-controls="debitNotes" aria-selected="false">Debit Notes</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="creditNotes-line-tab" data-bs-toggle="tab" data-bs-target="#creditNotes" role="tab" aria-controls="creditNotes" aria-selected="false">Credit Notes</a>
                </li>
              </ul>
              <div class="tab-content mt-3" id="lineTabContent">
                @if ($account->type == 1)
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-line-tab">
                  @include('accounts.show-includes.lpo')
                </div>
                @endif
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-line-tab">
                  @include('accounts.show-includes.quotation')
                </div>
                <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-line-tab">
                  @include('accounts.show-includes.proforma')
                </div>
                <div class="tab-pane fade" id="note" role="tabpanel" aria-labelledby="note-line-tab">
                  @include('accounts.show-includes.invoice')
                </div>
                <div class="tab-pane fade" id="receipt" role="tabpanel" aria-labelledby="receipt-line-tab">
                  @include('accounts.show-includes.receipt')
                </div>
                <div class="tab-pane fade" id="debitNotes" role="tabpanel" aria-labelledby="debitNotes-line-tab">
                  @include('accounts.show-includes.debitnotes')
                </div>
                <div class="tab-pane fade" id="creditNotes" role="tabpanel" aria-labelledby="creditNotes-line-tab">
                  @include('accounts.show-includes.credit-notes')
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
  <script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>
@endpush

@push('custom-scripts')
  <script src="{{ asset('assets/js/dropify.js') }}"></script>
  <script>
  $(document).ready( function () {
    $('#dataTableExample1').DataTable({
        language: { searchPlaceholder: "Search records", search: "",},
    });
    $('#dataTableExample2').DataTable({
        language: { searchPlaceholder: "Search records", search: "",},
    });
    $('#dataTableExample3').DataTable({
        language: { searchPlaceholder: "Search records", search: "",},
    });
    $('#dataTableExample4').DataTable({
        language: { searchPlaceholder: "Search records", search: "",},
    });
    $('#dataTableExample5').DataTable({
        language: { searchPlaceholder: "Search records", search: "",},
    });
    $('#dataTableExample6').DataTable({
        language: { searchPlaceholder: "Search records", search: "",},
    });
    $('#dataTableExample7').DataTable({
        language: { searchPlaceholder: "Search records", search: "",},
    });

    const formatter = new Intl.NumberFormat('en-US', {
        style: 'decimal',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    $.ajax({
      type: 'get',
      url: '/running-balance/{{$account->id}}',
      processData: false,
      cache: false,
      contentType: false,
      error: (err) => {
        console.log(err);
      },
      success: (response) => {
        console.log(response);
        $('#running_balance').text(formatter.format(response))
      }
    })
    
  });

</script>
@endpush