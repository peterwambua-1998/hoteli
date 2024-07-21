@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />

    @endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('bar.orders')}}">Bar Order</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    {{-- <div>
        <a class="btn btn-primary" href="{{route('orders.create')}}" style="width: 100%;"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px;" name="add-circle-outline"></ion-icon> Add Order</a>
    </div> --}}
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


{{-- cards --}}
{{-- <div class="row" >
    <div class="col-md-12 grid-margin stretch-card">
        <div class="col-md-4 px-2" >
            <div class="px-2 py-3" style="border: 1px solid #94a3b8; border-radius: 0.25rem; background:#f1f5f9">
                <div style="display:flex; justify-content:space-between;">
                    <h5>Daily Order</h5>
                    <p id="daily_count" style="color: white; font-weight: bold; padding-left: 10px; padding-right: 10px; padding-top: 5px; padding-bottom: 5px; border-radius: 5%; background: green;">0</p>
                </div>
                <div>
                    <p id="daily_total">KSH 0</p>
                </div>
            </div>
           
        </div>
        <div class="col-md-4 px-2" >
            <div class="px-2 py-3" style="border: 1px solid #94a3b8; border-radius: 0.25rem; background:#f1f5f9">
                <div style="display:flex; justify-content:space-between;">
                    <h5>Monthly Order</h5>
                    <p id="monthly_count" style="color: white; font-weight: bold; padding-left: 10px; padding-right: 10px; padding-top: 5px; padding-bottom: 5px; border-radius: 5%; background: green;">0</p>
                </div>
                <div>
                    <p id="monthly_total">KSH 0</p>
                </div>
            </div>
           
        </div>
        <div class="col-md-4 px-2" >
            <div class="px-2 py-3" style="border: 1px solid #94a3b8; border-radius: 0.25rem; background:#f1f5f9">
                <div style="display:flex; justify-content:space-between;">
                    <h5>Yearly Order</h5>
                    <p id="yearly_count" style="color: white; font-weight: bold; padding-left: 10px; padding-right: 10px; padding-top: 5px; padding-bottom: 5px; border-radius: 5%; background: green;">0</p>
                </div>
                <div>
                    <p id="yearly_total">KSH 0</p>
                </div>
            </div>
           
        </div>
    </div>
</div> --}}
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
            <h6 class="card-title">Bar Orders Table</h6>
            <p class="text-muted mb-3"></p> 
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTableExample">
                    <thead>
                        <tr>
                            <th>
                                #
                            </th>
                            <th>
                                Order
                            </th>
                            <th>
                                Customer
                            </th>
                            <th>
                                Table
                            </th>
                            <th>
                                Amount (KSH)
                            </th>
                            <th>Balance</th>
                           
                            <th>Created at</th>
                            <th >
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $num = 1; ?>
                        @foreach ($orders as $order)
                        <tr data-bs-toggle="modal" data-bs-target="#moreInfo-{{$order->id}}">
                            <td>
                                {{$num}}<?php $num++ ?>
                            </td>
                            <td>
                                {{$order->inv_number}}
                            </td>
                            <td>
                                @if ($order->customer_id != 1)
                                    {{$order->customer->name}}
                                @else
                                    Walk in
                                @endif
                            </td>
                            <td>
                                {{ $order->table_number ? $order->table_number : 'n/a'}}
                            </td>
                            <td>
                                {{number_format($order->total, 2)}}
                            </td>
                            <td>
                                {{number_format($order->bal, 2)}}
                            </td>
                            <td>
                                <?php
                                $date = date_format(date_create("$order->created_at"),"Y/m/d H:i:s");
                                ?>
                                {{$date}}
                            </td>
                            <td>
                                <a href="{{route('bar.add_existing.page', $order->id)}}">Add-Order</a>
                                
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


@foreach ($orders as $order)
<div class="modal fade" id="exampleModal-{{$order->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('kitchen.order.payment')}}" method="post">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <h5>Balance: <span class="ml-3">{{$order->bal}}</span></h5>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment method</label>
                        <select class="form-select mb-3" name="payment_method">
                        <option selected value="0">Choose payment method...</option>
                        <option value="1">Cash</option>
                        <option value="3">Bank transfer</option>
                        <option value="4">Cheque</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="payment_code" class="form-label">Reference number</label>
                        <input type="text" class="form-control" name="payment_code" id="payment_code" autocomplete="off" placeholder="Ex: RF121212">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Bank Account</label>
                        <select class="form-select mb-3" name="bank_account_id">
                        <option selected value="0">Choose bank account...</option>
                        @foreach ($bankAccounts as $bankAccount)
                        <option value="{{$bankAccount->id}}">{{$bankAccount->bank_name}}</option>
                        @endforeach
                        
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="text" class="form-control" name="amount" id="amount" autocomplete="off" placeholder="Ex: 5000">
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="order_id" value="{{$order->id}}">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endforeach

@foreach ($orders as $order)
<div class="modal fade" id="moreInfo-{{$order->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Void Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="dataTableExample3">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Description</th>
                                    <th>Qty</th>
                                    <th>Rate</th>
                                    <th>amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $number = 1; ?>
                                @foreach ($order->items as $item)
                                    <tr>
                                    <td>{{$number}}<?php $number++ ?></td>
                                    <td>{{$item->item_description}}</td>
                                    <td>{{$item->quantity}}</td>
                                    <td>{{$item->rate}}</td>
                                    <td>{{$item->amount}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="invoice_id" value="{{$order->id}}">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button type="submit" class="btn btn-danger">Yes</button>
                </div>
            </div>
        </div>
</div>
@endforeach


@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush

@push('custom-scripts')
    <script defer>
        $(document).ready( function () {
           
            $('#dataTableExample').DataTable({
                language: { searchPlaceholder: "Search records", search: "",},
            });

            // $.ajax({
            //     url: '',
            //     type: 'GET',
            //     processData: false,
            //     contentType: false,
            //     cache: false,
            //     error: (err) => {
            //         console.log(err);
            //     },
            //     success: (response) => {
            //         $('#daily_count').html(response['daily_orders_count']);
            //         $('#daily_total').html('KSH '+response['daily_amount']);

            //         $('#monthly_count').html(response['monthly_orders_count']);
            //         $('#monthly_total').html('KSH '+response['monthly_amount']);

            //         $('#yearly_count').html(response['yearly_orders_count']);
            //         $('#yearly_total').html('KSH '+response['yearly_amount']);
            //     }
            // })
        });

    </script>
@endpush