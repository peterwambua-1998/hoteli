@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-around">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('packages.index')}}">Packages</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    <div>
        <a class="btn btn-primary" style="width: 100%;" href="{{route('packages.create')}}"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px;" name="add-circle-outline"></ion-icon> Create Package </a>
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
            <h6 class="card-title">Packages Table</h6>
            <p class="text-muted mb-3"></p> 
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTableExample">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $number = 1; ?>
                        @foreach ($packages as $p)
                        <tr >
                            <td data-bs-toggle="modal" data-bs-target="#exampleModal-{{$p->id}}">{{$number}}<?php $number++; ?></td>
                            <td data-bs-toggle="modal" data-bs-target="#exampleModal-{{$p->id}}">{{$p->name}}</td>
                            <td data-bs-toggle="modal" data-bs-target="#exampleModal-{{$p->id}}">{{$p->price}}</td>
                            <td>
                                <a href="#" class="ml-2" data-bs-toggle="modal" data-bs-target="#edit-{{$p->id}}">Edit</a>
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



@foreach ($packages as $p)
<div class="modal fade" id="exampleModal-{{$p->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Package Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <p>Name: <span style="font-weight: bold">{{$p->name}}</span></p>
                    <p>Description: <span style="font-weight: bold">{{$p->description}}</span></p>
                    <p>Price: <span style="font-weight: bold">{{number_format($p->price, 2)}}</span></p>
                </div>
                
            
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Amount</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $num = 1 ?>
                            @foreach ($p->items as $item)
                            <tr>
                                <?php $name = App\Models\PackageFacility::where('id','=', $item->package_facility_id)->first() ?>
                                <td>
                                    @if ($name)
                                        {{$name->name}}
                                    @else
                                        Not found Contact Peter
                                    @endif
                                </td>
                                <td>{{number_format($item->price, 2)}}</td>
                                <td>
                                    @if ($item->main_or_extra_item == 1)
                                        Main
                                    @endif

                                    @if ($item->main_or_extra_item == 0)
                                        Extra
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                           
                        </tbody>
                    </table>
                </div>
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
        } );

    </script>
@endpush