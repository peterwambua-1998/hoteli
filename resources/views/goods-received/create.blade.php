@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('purchase.order.index')}}">Goods Receive Note</a></li>
      <li class="breadcrumb-item active" aria-current="page">create</li>
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

<form action="{{route('goods.receive.store')}}" method="post">
@csrf

<input type="hidden" name="grn_number" id="po_number" />
<input type="hidden" name="purchase_order_id" value="{{$purchaseOrder->id}}" />

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
            {{-- invoice details --}}
            <div class="mb-2">
                <h4 class="mb-4">Goods Receive Note Details</h4>
                <div style="display: flex; justify-content:space-between">
                    
                    <p class="mb-3" style="color: #808080">GRN No: <span id="inv_no">{{rand(0, 1000000)}}</span></p>
                </div>
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="exampleInputUsername1" class="form-label">Date Received</label>
                        <input required type="datetime-local" class="form-control" name="date_issue" id="exampleInputUsername1" autocomplete="off">
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="send_to" class="form-label">Select Store</label>
                        <select name="send_to" class="form-select">
                            <option value="1" seleceted>Main store</option>
                            <option value="2">kitchen store</option>
                            <option value="3">bar store</option>
                            <option value="4">Maintenance store</option>
                        </select>
                    </div>
                </div>
            </div>
            {{-- invoice items --}}
            <div>
                {{-- item --}}
                <table class="table table-bordered" id="dataTableExample">
                    <thead style="background: rgb(6, 181, 0); color:black;">
                        <tr>
                            <th style="width: 10%; color:black">Code</th>
                            <th style="width: 35%; color:black">Item Description</th>
                            <th style="width: 15%; color:black">Unit Price</th>
                            <th style="width: 10%; color:black">Qty Ordered</th>
                            <th style="width: 10%; color:black">Qty Received</th>
                            <th style="width: 25%; color:black">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="table-row">
                        @foreach ($purchaseOrder->items as $item)
                        <?php 
                            $product = App\Models\Product::find($item->item_id);
                        ?>
                        <tr>
                            <td>
                                <input type="text" required name="item_code[]" class="form-control item_code" autocomplete="off" value="{{$product->code}}" readonly>
                                <input type="hidden" name="item_id[]" class="item_id" value="{{$item->item_id}}" />
                            </td>
                            <td>
                                <input type="text" required name="item_description[]" class="form-control item_description" value="{{$product->description}}" autocomplete="off">
                            </td>
                            <td>
                                <input type="text" name="price[]" class="form-control price" autocomplete="off" value="{{$product->buying_price}}">
                            </td>
                            <td>
                                <input type="text" name="qty_ordered[]" class="form-control qty_ordered" autocomplete="off" value="{{$item->quantity}}" readonly>
                            </td>
                            <td>
                                <input type="text" name="qty_received[]" class="form-control qty_received" autocomplete="off">
                            </td>
                            <td>
                                <input type="text" readonly name="amount[]" class="form-control amount" autocomplete="off">
                                <input type="hidden" class="taxable" value="{{$product->taxable}}" />
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                

                <div class="mt-1" style="display: flex; flex-direction:row-reverse">
                    <div style="width: 40%; display: flex;">
                        <p style="width: 40%; font-size: 16px;">Sub Total</p>
                        <input type="text" readonly name="sub_total" class="form-control sub_total" autocomplete="off">
                    </div>
                </div>
                <div class="mt-1" style="display: flex; flex-direction:row-reverse">
                    <div style="width: 40%; display: flex;">
                        <p style="width: 40%; font-size: 16px;">Vat 16%</p>
                        <input type="text" readonly name="vat" class="form-control vat" autocomplete="off">
                    </div>
                </div>
                <div class="mt-1" style="display: flex; flex-direction:row-reverse">
                    <div style="width: 40%; display: flex;">
                        <p style="width: 40%; font-size: 16px;">Total</p>
                        <input type="text" readonly name="total" class="form-control total" autocomplete="off">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="note" class="form-label">Note</label>
                    <textarea placeholder="note" class="form-control" name="note" id="note" rows="5" spellcheck="false"></textarea>
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
            let po_number = $('#inv_no').text();
            $('#po_number').val(po_number);
            // add elements 
            function calculateTotal() {
                $('.qty_received').each((index, el) => {
                    $(el).on('input', (e) => {
                        let parent = $(el).parent().parent();
                        let qty = Number(parent.find('.qty_received').val());
                        let rate = Number(parent.find('.price').val());
                        let amount_input = parent.find('.amount');
                        let amount = qty * rate;
                        amount_input.val(amount);
                        calFullTotal();
                    });
                });
            }

            calculateTotal();

            function calFullTotal() {
                let sub_total = 0;
                let sub_total_non_taxable = 0
                
                let total = 0;
                let total_taxable = 0;
                let vat = 0;
                $('.amount').each((index, el) => {
                    let parent = $(el).parent();
                    let isTaxable = parent.find('.taxable').val(); 
                    if (isTaxable == 1) {
                        sub_total += Number($(el).val());
                        total += Number($(el).val());

                    } else {
                        sub_total_non_taxable += Number($(el).val());
                        total_taxable += Number($(el).val());

                    }
                });

                sub_total = Number(sub_total / 1.16).toFixed(2);
                console.log(sub_total, sub_total_non_taxable);
                let sub_total_display = Number(sub_total) + Number(sub_total_non_taxable);
                $('.sub_total').val(Number(sub_total_display).toFixed(2));
                vat = Number(total - Number(sub_total)).toFixed(2);
                $('.vat').val(vat);
                $('.total').val(parseFloat(sub_total) + parseFloat(vat) + parseFloat(sub_total_non_taxable));
            }
        });
    </script>
@endpush