@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('bar.store.index')}}">Stock Adjustment</a></li>
      <li class="breadcrumb-item active" aria-current="page">Edit</li>
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

<form action="{{route('front.store.adjust.store')}}" method="post">
@csrf

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
            {{-- invoice details --}}
            <div class="mb-2">
                <h4 class="mb-4">Front Office Store Stock Adjustment</h4>
                
            </div>
            {{-- invoice items --}}
            <div>
                <div class="table-responsive">
                    {{-- item --}}
                    <table class="table table-bordered" id="dataTableExample">
                        <thead style="background: rgb(6, 181, 0); color:black;">
                            <tr>
                                <th style="width: 10%; color:black">Code</th>
                                <th style="width: 20%; color:black">Item Description</th>
                                <th style="width: 5%; color:black">System Stock</th>
                                <th style="width: 5%; color:black">Physical Count</th>
                                <th style="width: 15%; color:black">Reason</th>
                            </tr>
                        </thead>
                        <tbody id="table-row">
                            @foreach ($frontStoreItems as $key => $item)
                            <?php
                                $product = App\Models\Product::find($item->item_id);
                            ?>
                            <tr>
                                <td>
                                    <input type="text" required name="item_code[]" class="form-control item_code" autocomplete="off" value="{{$product->code}}">
                                    <input type="hidden" name="item_id[]" value="{{$item->item_id}}" />
                                </td>
                                <td>
                                    <input type="text" required name="item_description[]" class="form-control item_description" autocomplete="off" value="{{$product->description}}">
                                </td>
                                <td>
                                    <input type="text" name="system_count[]" class="form-control system_count" autocomplete="off" value="{{$item->quantity}}">
                                </td>
                                <td>
                                    <input type="text" name="quantity[]" class="form-control quantity" autocomplete="off" required>
                                </td>
                                <td>
                                    <input type="text" name="reason[]" class="form-control reason" autocomplete="off" required>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-2">
                    <button type="submit" class="btn btn-primary" id="save-btn" style="width: 20%">
                        Save Changes
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
