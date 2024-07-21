@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between;">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('goods.receive.index')}}">Goods Received Note</a></li>
      <li class="breadcrumb-item active" aria-current="page">Details</li>
    </ol>
    <div>
        <form action="{{route('bill.store')}}" method="post">
            @csrf
            <input type="hidden" name="note_id" value="{{$note->id}}">

            <button  class="btn btn-outline-success">Genete Bill</button>
        </form>
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
                                    <h3>Goods Received Note</h3>
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
                                            <li class="text-bold">{{$note->supplier->name}}</li>
                                            <li class="text-bold">{{$note->supplier->email}}</li>
                                            <li class="text-bold">{{$note->supplier->telephone}}</li>
                                            {{-- <li class="text-bold">United Kingdom</li> --}}
                                        </ul>
                                    </div>
        
                                    <div class="invoice-head-right">
                                        <table>
                                            <tr>
                                                <td>Date Created</td>
                                                <td>{{$note->created_at}}</td>
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
                                            @foreach ($note->items as $item)
                                            <tr class="invoice-content">
                                                <?php 
                                                    $product = App\Models\Product::find($item->item_id);
                                                ?>
                                                <td class="bordered">{{$product->code}}</td>
                                                <td class="bordered">{{$product->description}}</td>
                                                <td class="bordered">{{$item->qty_received}}</td>
                                                <td class="bordered">{{$product->price}}</td>
                                                <td class="text-end bordered">{{number_format($item->amount, 2)}}</td>
                                            </tr>
                                            @endforeach

                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td colspan="2" class="bordered text-bold">Sub Total</td>
                                                <td class="bordered text-end">{{number_format($note->sub_total, 2)}}</td>
                                               
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td colspan="2" class="bordered text-bold">Vat (16%)</td>
                                                <td class="bordered text-end">{{number_format($note->vat, 2)}}</td>
                                               
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td colspan="2" class="bordered text-bold">Total Amount</td>
                                                <td class="bordered text-end">{{number_format($note->total, 2)}}</td>
                                               
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



@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush

