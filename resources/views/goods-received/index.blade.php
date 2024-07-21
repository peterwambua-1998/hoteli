@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('goods.receive.index')}}">Goods Receive Note</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    <div>
        <a href="{{route('goods.receive.generate')}}" class="btn btn-primary">Create GRN</a>
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
            <h6 class="card-title">Goods Receive Note Table</h6>
            <p class="text-muted mb-3"></p> 
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTableExample">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>GRN Number</th>
                            <th>PO Number</th>
                            <th>Sub Total</th>
                            <th>Vat</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $number = 1; ?>
                        @foreach ($note as $o)
                        <tr>
                            <td>{{$number}}<?php $number++; ?></td>
                            <td>{{$o->grn_number}}</td>
                            <td>
                                @if ($o->purchaseOrder)
                                    {{$o->purchaseOrder->po_number}}
                                @else
                                   N/A 
                                @endif
                                
                            </td>
                            <td>{{number_format($o->sub_total, 2)}}</td>
                            <td>{{number_format($o->vat, 2)}}</td>
                            <td>{{number_format($o->total, 2)}}</td>
                            <td style="display: flex; gap: 16px;">
                               <a href="{{route('goods.receive.show', $o->id)}}" style="color: green;">show</a>
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
        });
    </script>
@endpush