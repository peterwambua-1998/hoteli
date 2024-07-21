@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    
@endpush
@section('content')

<nav class="page-breadcrumb" >
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('purchase.order.index')}}">Purchase Order</a></li>
      <li class="breadcrumb-item active" aria-current="page">Details</li>
    </ol>
    
</nav>

<div style="display:flex; justify-content: space-between;">
    @if ($purchaseOrder->status == 2)
    <div class=" pt-2 pb-2 rounded" >
        <p style="font-size: 18px; font-weight:bold; color:#dc2626">Rejected</p>
    </div>
    @endif

    @if ($purchaseOrder->status == 1)
    <div class=" pt-2 pb-2 rounded" >
        <p style="font-size: 18px; font-weight:bold; color:#65a30d">Received</p>
    </div>
    @endif

    @if ($purchaseOrder->status == 0)
    <div class=" pt-2 pb-2 rounded" >
        <p style="font-size: 18px; font-weight:bold; color:#ca8a04">Pending</p>
    </div>
    @endif
    
    <div style="display:flex; gap: 10px;">
        <a href="{{route('goods.receive.create', $purchaseOrder->id)}}"><button class="btn btn-primary">Add Goods Received Note</button></a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal"><button class="btn btn-danger">Reject Order</button></a>
        <a href="{{route('purchase.order.index')}}"><button class="btn btn-warning">Back</button></a>
    </div>
</div>

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
            
            <div class="invoice-wrapper-two" id="print-area">
                <div class="invoice-two">
                    <div class="invoice-container">
                        <div class="invoice-head">
        
                            <div class="invoice-head-top" style="display: flex; justify-content: space-between;">
                                <div class="invoice-head-top-left text-start">
                                    <img src="{{asset('images/alogo.png')}}" alt="logo-image" srcset="">
                                </div>
                                <div class="invoice-head-top-right text-end" style="align-self: flex-end;">
                                    <h3>Purchase Order</h3>
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
                                            <li class="text-bold">{{$purchaseOrder->supplier->name}}</li>
                                            <li class="text-bold">{{$purchaseOrder->supplier->email}}</li>
                                            <li class="text-bold">{{$purchaseOrder->supplier->telephone}}</li>
                                            {{-- <li class="text-bold">United Kingdom</li> --}}
                                        </ul>
                                    </div>
        
                                    <div class="invoice-head-right">
                                        <table>
                                            <tr>
                                                <td>Date Issued</td>
                                                <td>{{$purchaseOrder->date_issue}}</td>
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
                                            @foreach ($purchaseOrder->items as $item)
                                            <tr class="invoice-content">
                                                <?php 
                                                    $product = App\Models\Product::find($item->item_id);
                                                ?>
                                                <td class="bordered">{{$product->code}}</td>
                                                <td class="bordered">{{$product->description}}</td>
                                                <td class="bordered">{{$item->quantity}}</td>
                                                <td class="bordered">{{$product->buying_price}}</td>
                                                <td class="text-end bordered">{{number_format($item->amount, 2)}}</td>
                                            </tr>
                                            @endforeach

                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td colspan="2" class="bordered text-bold">Sub Total</td>
                                                <td class="bordered text-end">{{number_format($purchaseOrder->sub_total, 2)}}</td>
                                               
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td colspan="2" class="bordered text-bold">Vat (16%)</td>
                                                <td class="bordered text-end">{{number_format($purchaseOrder->vat, 2)}}</td>
                                               
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td colspan="2" class="bordered text-bold">Total Amount</td>
                                                <td class="bordered text-end">{{number_format($purchaseOrder->total, 2)}}</td>
                                               
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

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('purchase.order.reject')}}" method="post">
        @csrf
        <input type="hidden" name="purchase_order_id" value="{{$purchaseOrder->id}}">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Reject Purchase Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea class="form-control" name="reason" id="reason" rows="5" spellcheck="false"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
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

