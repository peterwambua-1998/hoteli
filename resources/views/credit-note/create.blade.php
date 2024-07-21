@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('accounts.index')}}">Credit note</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add</li>
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

<form action="{{route('credit-note.store')}}" method="post">
@csrf

<input type="hidden" name="account_id" value="{{$account->id}}"/>
<input type="hidden" name="invoiced_to" value="{{$account->name}}"/>
<input type="hidden" name="invoice_item_id" value="{{$invoice_item->id}}"/>
<input type="hidden" name="invoice_id" value="{{$invoice->id}}"/>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
            {{-- invoice details --}}
            <div class="mb-2">
                <h4 class="mb-4">Credit note</h4>
                <div style="display: flex; justify-content:space-between">
                    <div class="mb-2">
                        <p class="mb-1" style="color: #808080">Invoiced To: <span>{{$account->name}}</span></p>
                        <p style="color: #808080">Company VAT Reg: <span></span></p>
                    </div>
                    
                    <p class="mb-3" style="color: #808080">Inv No: <span id="inv_no">{{$invoice->inv_number}}</span></p>
                </div>
                
            </div>
            {{-- invoice items --}}
            <div>
                <div class="table-responsive">
                    {{-- item --}}
                    <table class="table table-bordered" id="dataTableExample">
                        <thead style="background: rgb(6, 181, 0); ">
                            <tr >
                                <th style="width: 10%; color:black">Code</th>
                                <th style="width: 40%; color:black">Item Description</th>
                                <th style="width: 10%; color:black">Qty</th>
                                <th style="width: 15%; color:black">Rate</th>
                                <th style="width: 10%; color:black">Days</th>
                                <th style="width: 20%; color:black">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="table-row">
                            <tr>
                                <td>
                                    <input type="text" readonly required name="item_code" class="form-control item_code" value="{{$invoice_item->item_code}}" autocomplete="off">
                                </td>
                                <td>
                                    <input type="text" readonly required name="item_description" class="form-control item_description"  value="{{$invoice_item->item_description}}" autocomplete="off">
                                </td>
                                <td>
                                    <input type="text" name="quantity" class="form-control quantity" autocomplete="off">
                                </td>
                                <td>
                                    <input type="text" name="rate" class="form-control rate" autocomplete="off">
                                </td>
                                <td>
                                    <input type="text" name="days" class="form-control days" autocomplete="off">
                                </td>
                                <td>
                                    <input type="text" readonly name="amount" class="form-control amount" autocomplete="off">
                                </td>
                                
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-2">
                    <button type="submit" class="btn btn-primary" id="save" style="width: 20%">
                        Save
                    </button>
                </div>
            </div>
        </div>
      </div>
    </div>
</div>
</form>

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush

@push('custom-scripts')
    <script defer>
        $(document).ready( function () {

            function calculateTotal() {
                $('.days').each((index, el) => {
                    $(el).on('input', (e) => {
                        let parent = $(el).parent().parent();
                        let qty = Number(parent.find('.quantity').val());
                        let rate = Number(parent.find('.rate').val());
                        let days = Number(parent.find('.days').val());
                        let amount_input = parent.find('.amount');
                        let amount = qty * rate * days;
                        amount_input.val(amount);
                    });
                });
            }

            calculateTotal();


            
        });
    </script>
@endpush