@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <style>
        .items-hover:hover {
            cursor: pointer;
        }
    </style>
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('maintenance.requisition.view')}}">Material Requisition</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    <div>
        <a href="{{route('maintenance.requisition.create')}}" class="btn btn-primary">Request Items</a>
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
            <h6 class="card-title">Maintenance Material Requisition Table</h6>
            <p class="text-muted mb-3"></p> 
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTableExample">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Store</th>
                            <th>Status</th>
                            <th>Items</th>
                            <th>Date Requested</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $number = 1; ?>
                        @foreach ($requisitions as $o)
                        <tr>
                            <td>{{$number}}<?php $number++; ?></td>
                            <td>
                                Main store
                            </td>
                            <td>
                                @if ($o->status == 0)
                                    <span class="badge bg-danger">pending</span> 
                                @endif
                                @if ($o->status == 1)
                                    <span class="badge bg-success">issued</span>
                                @endif
                            </td>
                            <td>
                                <span data-bs-toggle="modal" data-bs-target="#exampleModal-{{$o->id}}" class="badge bg-primary items-hover">{{$o->items->count()}}</span>
                            </td>
                            <td>{{$o->created_at->format('j F Y, g:i A')}}</td>
                            <td style="display: flex; gap: 16px;">
                               <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal-{{$o->id}}" style="color: green;">show items</a>
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

@foreach ($requisitions as $r)
<div class="modal fade" id="exampleModal-{{$r->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Requested Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                @foreach ($r->items as $item)
                    <table class="table table-bordered">
                        <thead style="background: green;">
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $n = 1 ?>
                            <tr>
                                <td>{{$n}}<?php $n++ ?></td>
                                <td>{{$item->item->name}}</td>
                                <td>{{$item->quantity}}</td>
                            </tr>
                        </tbody>
                    </table>
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
        });
    </script>
@endpush