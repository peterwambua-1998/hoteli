@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between;">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('bill.index')}}">Bill</a></li>
      <li class="breadcrumb-item active" aria-current="page">Details</li>
    </ol>
    <div>
        <a href="#" data-bs-toggle="modal" data-bs-target="#receiptModal"  class="btn btn-outline-success">Generate Receipt</a>
        <a href="{{route('bill.index')}}" class="btn btn-warning">Back</a>
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
    <input type="checkbox" class="form-check-input" id="checkInlineCheckedDisabled" disabled=""  @if(count($creditNotes) > 0) checked="" @endif>
    <label class="form-check-label" for="checkInlineCheckedDisabled">
      Credit Note
    </label>
</div>

<div class="form-check form-check-inline">
    <input type="checkbox" class="form-check-input" id="checkInlineCheckedDisabled" disabled=""  @if(count($debitNotes) > 0) checked="" @endif>
    <label class="form-check-label" for="checkInlineCheckedDisabled">
      Debit Note
    </label>
</div>

<div class="mt-3 mb-3" style="display: flex; gap: 10%; font-size: 18px;">
    <p><span style="font-weight: 200">Bill total:</span> <span class="font-bold">{{number_format($billTotal, 2)}}</span></p>
    <p><span style="font-weight: 200">Paid total:</span> <span>{{number_format($receiptTotal, 2)}}</span></p>
    <p><span style="font-weight: 200">Balance:</span> <span>{{number_format($balance)}}</span></p>
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
                                    <h3>Bill</h3>
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
                                            <li class="text-bold">Supplier:</li>
                                            <li class="text-bold">{{$bill->supplier->name}}</li>
                                            <li class="text-bold">{{$bill->supplier->email}}</li>
                                            <li class="text-bold">{{$bill->supplier->telephone}}</li>
                                            {{-- <li class="text-bold">United Kingdom</li> --}}
                                        </ul>
                                    </div>
        
                                    <div class="invoice-head-right">
                                        <table>
                                            <tr>
                                                <td>Date Created</td>
                                                <td>{{$bill->created_at}}</td>
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
                                                <td class="text-bold bordered text-end">Amount</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($bill->items as $item)
                                            <tr class="invoice-content">
                                                <?php 
                                                    $product = App\Models\Product::find($item->item_id);
                                                ?>
                                                <td class="bordered">{{$product->code}}</td>
                                                <td class="bordered" id="dropdownMenuButton-{{$item->id}}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <a href="#" style="color: black;">{{$product->description}}</a>
                                                    <div class="dropdown-menu" style="background: #cbd5e1" aria-labelledby="dropdownMenuButton-{{$item->id}}">
                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#credit-note-{{$item->id}}">Credit note</a>
                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#debit-note-{{$item->id}}">Debit note</a>
                                                    </div>
                                                </td>
                                                <td class="bordered">{{$item->quantity}}</td>
                                                <td class="bordered">{{$product->price}}</td>
                                                <td class="text-end bordered">{{number_format($item->amount, 2)}}</td>
                                            </tr>
                                            @endforeach

                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td colspan="2" class="bordered text-bold">Sub Total</td>
                                                <td class="bordered text-end">{{number_format($bill->sub_total, 2)}}</td>
                                               
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td colspan="2" class="bordered text-bold">Vat (16%)</td>
                                                <td class="bordered text-end">{{number_format($bill->vat, 2)}}</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td colspan="2" class="bordered text-bold">Total Amount</td>
                                                <td class="bordered text-end">{{number_format($bill->total, 2)}}</td>
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

{{-- receipt --}}
<div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('bill.receipt.store')}}" method="post">
        @csrf
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Receipt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <div class="mt-1 mb-5" style="display: flex; gap: 10%; font-size: 18px;">
                    <p><span style="font-weight: 200">Bill total:</span> <span class="font-bold">{{number_format($billTotal, 2)}}</span></p>
                    <p><span style="font-weight: 200">Paid total:</span> <span>{{number_format($receiptTotal, 2)}}</span></p>
                    <p><span style="font-weight: 200">Balance:</span> <span>{{number_format($balance)}}</span></p>
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
                <input type="hidden" name="bill_id" value="{{$bill->id}}">
                <input type="hidden" name="supplier_id" value="{{$bill->supplier->id}}">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </div>
        </div>
    </form>
</div>
{{-- receipt --}}

{{-- credit notes --}}
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Credit notes</h6>
                <p class="text-muted mb-3"></p> 

                <div>
                    <div class="invoice-wrapper-two" id="print-area">
                        <div class="invoice-two" >
                            <div class="invoice-container" style="height: fit-content">
                                <div class="overflow-view">
                                    <div class="invoice-body">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <td class="text-bold bordered">Item Code</td>
                                                    <td class="text-bold bordered">Item Description</td>
                                                    <td class="text-bold bordered">Qty</td>
                                                    <td class="text-bold bordered">Rate</td>
                                                    <td class="text-bold bordered text-end">Amount</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($creditNotes as $note)
                                                    <tr class="invoice-content">
                                                        <?php  
                                                            $product = App\Models\Product::where('id','=', $note->item_id)->first();
                                                        ?>
                                                        <td class="bordered">{{$product->code}}</td>
                                                        <td class="bordered" >{{$product->description}}</td>
                                                        <td class="bordered">{{$note->quantity}}</td>
                                                        <td class="bordered">{{$note->rate}}</td>
                                                        <td class="text-end bordered">{{number_format($note->amount, 2)}}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- debit notes --}}
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body" style="height: fit-content">
                <h6 class="card-title">Debit notes</h6>
                <p class="text-muted mb-3"></p> 

                <div>
                    <div class="invoice-wrapper-two" id="print-area">
                        <div class="invoice-two" >
                            <div class="invoice-container" >
                                <div class="overflow-view">
                                    <div class="invoice-body">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <td class="text-bold bordered">Item Code</td>
                                                    <td class="text-bold bordered">Item Description</td>
                                                    <td class="text-bold bordered">Qty</td>
                                                    <td class="text-bold bordered">Rate</td>
                                                    <td class="text-bold bordered text-end">Amount</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($debitNotes as $note)
                                                    <tr class="invoice-content">
                                                        <?php  
                                                            $product = App\Models\Product::where('id','=', $note->item_id)->first();
                                                        ?>
                                                        <td class="bordered">{{$product->code}}</td>
                                                        <td class="bordered">{{$product->description}}</td>
                                                        <td class="bordered">{{$note->quantity}}</td>
                                                        <td class="bordered">{{$note->rate}}</td>
                                                        <td class="text-end bordered">{{number_format($note->amount, 2)}}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@foreach ($bill->items as $item)
<div class="modal fade" id="credit-note-{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('bill.credit-note.store')}}" method="post">
    @csrf

    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Credit note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="table-responsive">
                        {{-- item --}}
                        <table class="table table-bordered" id="dataTableExample">
                            <thead style="background: rgb(6, 181, 0); ">
                                <tr >
                                    <th style="width: 10%; color:black">Code</th>
                                    <th style="width: 40%; color:black">Item Description</th>
                                    <th style="width: 15%; color:black">Rate</th>
                                    <th style="width: 10%; color:black">Qty</th>
                                    <th style="width: 20%; color:black">Amount</th>
                                </tr>
                            </thead>
                            <tbody id="table-row">
                                <tr>
                                    <?php 
                                        $product = App\Models\Product::find($item->item_id);
                                    ?>
                                    <td>
                                        <input type="text" readonly required name="item_code" class="form-control item_code" value="{{$product->code}}" autocomplete="off">
                                        <input type="hidden" name="item_id" value="{{$product->id}}">
                                    </td>
                                    <td>
                                        <input type="text" readonly required name="item_description" class="form-control item_description"  value="{{$product->description}}" autocomplete="off">
                                    </td>
                                    <td>
                                        <input type="text" name="rate" class="form-control rate" autocomplete="off" value="{{$item->rate}}">
                                    </td>
                                    <td>
                                        <input type="text" name="quantity" class="form-control quantity" autocomplete="off">
                                    </td>
                                    <td>
                                        <input type="text" readonly name="amount" class="form-control amount" autocomplete="off">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>

    <input type="hidden" name="supplier_id" value="{{$bill->supplier->id}}"/>
    <input type="hidden" name="bill_id" value="{{$bill->id}}"/>
    <input type="hidden" name="bill_item_id" value="{{$item->id}}"/>

    </form>
</div>
@endforeach

@foreach ($bill->items as $item)
<div class="modal fade" id="debit-note-{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('bill.debit-note.store')}}" method="post">
    @csrf
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Debit note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="table-responsive">
                        {{-- item --}}
                        <table class="table table-bordered" id="dataTableExample">
                            <thead style="background: rgb(6, 181, 0); ">
                                <tr >
                                    <th style="width: 10%; color:black">Code</th>
                                    <th style="width: 40%; color:black">Item Description</th>
                                    <th style="width: 15%; color:black">Rate</th>
                                    <th style="width: 10%; color:black">Qty</th>
                                    <th style="width: 20%; color:black">Amount</th>
                                </tr>
                            </thead>
                            <tbody id="table-row">
                                <tr>
                                    <?php 
                                        $product = App\Models\Product::find($item->item_id);
                                    ?>
                                    <td>
                                        <input type="text" readonly required name="item_code" class="form-control item_code" value="{{$product->code}}" autocomplete="off">
                                        <input type="hidden" name="item_id" value="{{$product->id}}">
                                    </td>
                                    <td>
                                        <input type="text" readonly required name="item_description" class="form-control item_description"  value="{{$product->description}}" autocomplete="off">
                                    </td>
                                    <td>
                                        <input type="text" name="rate" class="form-control rate" autocomplete="off" value="{{$item->rate}}">
                                    </td>
                                    <td>
                                        <input type="text" name="quantity" class="form-control quantity" autocomplete="off">
                                    </td>
                                    <td>
                                        <input type="text" readonly name="amount" class="form-control amount" autocomplete="off">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>

    <input type="hidden" name="supplier_id" value="{{$bill->supplier->id}}"/>
    <input type="hidden" name="bill_id" value="{{$bill->id}}"/>
    <input type="hidden" name="bill_item_id" value="{{$item->id}}"/>

    </form>
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

            function calculateTotal() {
                $('.quantity').each((index, el) => {
                    $(el).on('input', (e) => {
                        let parent = $(el).parent().parent();
                        let qty = Number(parent.find('.quantity').val());
                        let rate = Number(parent.find('.rate').val());
                        let amount_input = parent.find('.amount');
                        let amount = qty * rate;
                        amount_input.val(amount);
                    });
                });
            }

            calculateTotal();
        });
    </script>
@endpush