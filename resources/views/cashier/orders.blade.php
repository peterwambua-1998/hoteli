@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('cashier.orders.index')}}">Orders</a></li>
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


<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
            <h6 class="card-title">Orders Table</h6>
            <p class="text-muted mb-3"></p> 
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTableExample3">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order No</th>
                            <th>Customer</th>
                            <th>To Pay</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th>User</th>
                            <th>vat</th>
                            <th>levy</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $number = 1; ?>
                        @foreach ($orders as $o)
                        @if ($o->st == 0)
                            <tr data-bs-toggle="modal" data-bs-target="#moreInfo-{{$o->id}}">
                                
                                <td>{{$number}}<?php $number++; ?></td>
                                <td>{{$o->inv_number}}</td>
                                <td>
                                    @if ($o->customer_id != 1)
                                        {{$o->customer->name}}
                                    @else
                                        Walk in
                                    @endif
                                </td>
                                <td>
                                    @if ($o->booking)
                                        @if ($o->booking->extras_paid_by == 1)
                                            Guest
                                        @else
                                            Company
                                        @endif
                                    @else
                                        Walk in
                                    @endif
                                </td>
                                <td>{{$o->description}}</td>
                                <td>{{$o->created_at->format('j F Y, g:i A')}}</td>
                                <td>{{$o->user->name}}</td>
                                <td>{{number_format($o->tax_amount, 2)}}</td>
                                <td>{{number_format($o->levy, 2)}}</td>
                                <td>{{number_format($o->total, 2)}}</td>
                                <td style="display: flex; gap: 20px;">
                                    <a href="#" style="color: green" data-bs-toggle="modal" data-bs-target="#exampleModal-{{$o->id}}">Pay</a>
                                    <a href="#" style="color: blue;" data-bs-toggle="modal" data-bs-target="#void-{{$o->id}}">Void</a>
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
<div class="modal fade" id="exampleModal-{{$order->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('cashier.orders.pay')}}" method="post" id="cashier-form">
        @csrf
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background: green; color: #fff;">
                    <h5 class="modal-title" id="exampleModalLabel">Pay order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body row">
                    <div class="mb-3 col-md-12" style="border-bottom: 1px solid rgba(0, 0, 0, 0.21); padding-bottom: 5px;">
                        <h5 class="mb-2">Balance: <span class="ml-3">{{$order->bal}}</span></h5>
                    </div>

                    <div class="mb-4 col-md-3">
                        <label for="account_id" class="form-label">Account</label>
                        <select name="account_id" class="account_id form-select">
                            <option value="0">Choose account...</option>
                            @foreach ($accounts as $account)
                                <option value="{{$account->id}}">{{$account->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4 col-md-3">
                        <label class="form-label">Payment method</label>
                        <select class="form-select payment_method" name="payment_method" >
                            <option selected value="0">Choose payment method...</option>
                            <option value="1">Cash</option>
                            <option value="3">Bank transfer</option>
                            <option value="4">Cheque</option>
                            <option value="5">Package</option>
                            <option value="6">Complimentary</option>
                            <option value="7">Pay on Checkout</option>
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
                        <span class="text-danger amount_error"></span>
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


@foreach ($orders as $order)
<div class="modal fade" id="void-{{$order->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('cashier.orders.void')}}" method="post">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Void Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <h6>Are you sure you want to void order: {{$order->inv_number}} ?</h6>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="invoice_id" value="{{$order->id}}">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button type="submit" class="btn btn-danger">Yes</button>
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
  <script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>
@endpush

@push('custom-scripts')
  <script src="{{ asset('assets/js/dropify.js') }}"></script>
  <script>
  $(document).ready( function () {
    $('#dataTableExample1').DataTable({
        language: { searchPlaceholder: "Search records", search: "",},
    });
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
    
  });
</script>
@endpush