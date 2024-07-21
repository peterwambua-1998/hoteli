@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('main.store.requisition')}}">Material Requisitions</a></li>
      <li class="breadcrumb-item active" aria-current="page">issue</li>
    </ol>
    <div>
        <a href="{{route('main.store.requisition')}}" class="btn btn-warning">Back</a>
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

<form action="{{route('requisition.issue.store')}}" method="post">
@csrf

<input type="hidden" name="requisition_id" value="{{$requisition->id}}" />

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
            {{-- invoice details --}}
            <div class="mb-2">
                <h4 class="mb-2">Material Requisitions Details</h4>
                <h6 class="mb-4"><span>{{$requisition->department_name}}</span> Department</h6>
            </div>
            {{-- invoice items --}}
            <div>
                {{-- item --}}
                <table class="table table-bordered" id="dataTableExample">
                    <thead style="background: rgb(6, 181, 0); color:black;">
                        <tr>
                            <th style="width: 10%; color:black">Code</th>
                            <th style="width: 35%; color:black">Item Description</th>
                            <th style="width: 10%; color:black">Qty Ordered</th>
                            <th style="width: 10%; color:black">Qty Received</th>
                        </tr>
                    </thead>
                    <tbody id="table-row">
                        @foreach ($requisition->items as $item)
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
                                <input type="text" name="qty_ordered[]" class="form-control qty_ordered" autocomplete="off" value="{{$item->quantity}}" readonly>
                            </td>
                            <td>
                                <input type="text" name="quantity_issued[]" class="form-control qty_received" autocomplete="off">
                            </td>
                            
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- <div class="mb-3">
                    <label for="note" class="form-label">Note</label>
                    <textarea placeholder="note" class="form-control" name="note" id="note" rows="5" spellcheck="false"></textarea>
                </div> --}}
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

