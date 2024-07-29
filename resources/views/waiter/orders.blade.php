@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />

    @endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('waiter.orders')}}">Orders</a></li>
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



<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
            <h6 class="card-title">{{Auth::user()->name}} Orders Table</h6>
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
                        @if ($order->bal > 0)
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
                                {{number_format($order->total, 2)}}
                            </td>
                            <td>
                                {{number_format($order->bal, 2)}}
                            </td>
                            <td>
                                <?php
                                $date = date_format(date_create("$order->created_at"),"g:i A  (l)");
                                ?>
                                {{$date}}
                            </td>
                            <td>
                                <a href="{{route('waiter.orders.add.select.pos', $order->id)}}">Add-Order</a>
                            </td>
                        </tr>
                        @endif
                       
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
      </div>
    </div>
</div>



@foreach ($orders as $order)
<div class="modal fade" id="moreInfo-{{$order->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="dataTableExample3">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Description</th>
                                    <th>Rate</th>
                                    <th>Qty</th>
                                    <th>amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $number = 1; ?>
                                @foreach ($order->items as $item)
                                <tr>
                                    <td>{{$number}}<?php $number++ ?></td>
                                    <td>{{$item->item_description}}</td>
                                    <td>{{$item->rate}}</td>
                                    <td>{{$item->quantity}}</td>
                                    <td>{{$item->amount}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger" onclick="$('#print-area-{{$order->id}}').print();">Print</button>
                </div>
            </div>
        </div>
</div>
@endforeach

@include('waiter.print')

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
                "ordering": false
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
        });

    </script>
@endpush