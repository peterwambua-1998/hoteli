@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-around">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('products.index')}}">Items</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    <div>
        <a class="btn btn-primary" style="width: 100%;" href="#" data-bs-toggle="modal" data-bs-target="#exampleModal"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px;" name="add-circle-outline"></ion-icon> Add Item</a>
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
            <h6 class="card-title">Items Table</h6>
            <p class="text-muted mb-3"></p> 
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTableExample">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Taxable</th>
                            <th>Buying Price</th>
                            <th>Selling Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $number = 1; ?>
                        @foreach ($products as $item)
                        <tr>
                            <td>{{$number}}<?php $number++; ?></td>
                            <td>{{$item->code}}</td>
                            <td>{{$item->name}}</td>
                            <td>{{$item->description}}</td>
                            <td>
                                @if ($item->taxable == 1)
                                    Yes
                                @endif

                                @if ($item->taxable == 0)
                                    No
                                @endif
                            </td>
                            <td>{{number_format($item->buying_price, 2)}}</td>
                            <td>{{number_format($item->price, 2)}}</td>
                            <td>
                                <a href="#" class="ml-2" data-bs-toggle="modal" data-bs-target="#edit-{{$item->id}}">Edit</a>
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

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('products.store')}}" method="post">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label for="type" class="form-label">Item Category</label>
                        <select class="form-select mb-3" name="category_id">
                            <option selected="0">Choose categoty...</option>
                            @foreach ($categories as $cat)
                                <option value="{{$cat->id}}">{{$cat->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label">Item Code</label>
                        <input type="text" required name="code" class="form-control" id="code" autocomplete="off" placeholder="Ex: 123">
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Item Name</label>
                        <input type="text" required name="name" class="form-control" id="name" autocomplete="off" placeholder="Ex: Name">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Item Description</label>
                        <input type="text" required name="description" class="form-control" id="description" autocomplete="off" placeholder="Ex: Description">
                    </div>

                    <div class="mb-3">
                        <label for="buying_price" class="form-label">Buying Price</label>
                        <input type="text" required name="buying_price" class="form-control" id="buying_price" autocomplete="off" placeholder="Ex: 200">
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Selling Price</label>
                        <input type="text" required name="price" class="form-control" id="price" autocomplete="off" placeholder="Ex: 200">
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Taxable</label>
                        <select class="form-select mb-3" name="taxable">
                            <option selected value="1">Choose yes or no</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>  
        </div>
    </form>
</div>

@foreach ($products as $item)
<div class="modal fade" id="edit-{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('products.update', $item->id)}}" method="post">
        @csrf
        @method('PATCH')
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="type" class="form-label">Item Category</label>
                        <select class="form-select mb-3" name="category_id">
                            <option selected="0">Choose categoty...</option>
                            @foreach ($categories as $cat)
                                <option @if($cat->id == $item->category_id) selected @endif value="{{$cat->id}}">{{$cat->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label">Item Code</label>
                        <input type="text" required value="{{$item->code}}" name="code" class="form-control" id="type" autocomplete="off" placeholder="Ex: 123">
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Item Name</label>
                        <input type="text" required value="{{$item->name}}" name="name" class="form-control" id="type" autocomplete="off" placeholder="Ex: Name">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Item Description</label>
                        <input type="text" required  value="{{$item->description}}" name="description" class="form-control" id="type" autocomplete="off" placeholder="Ex: Description">
                    </div>

                    <div class="mb-3">
                        <label for="buying_price" class="form-label">Buying Price</label>
                        <input type="text"  value="{{$item->buying_price}}" required name="buying_price" class="form-control" id="buying_price" autocomplete="off" placeholder="Ex: 200">
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Selling Price</label>
                        <input type="text" required value="{{$item->price}}" name="price" class="form-control" id="type" autocomplete="off" placeholder="Ex: 200">
                    </div>

                    <label for="type" class="form-label">Taxable</label>
                    <select class="form-select mb-3" name="taxable">
                        <option selected value="1">Choose yes or no</option>
                        <option @if($item->taxable == 1) selected @endif value="1">Yes</option>
                        <option @if($item->taxable == 0) selected @endif value="0">No</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>  
        </div>
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
            $('#dataTableExample').DataTable({
                language: { searchPlaceholder: "Search records", search: "",},
            });
        } );

    </script>
@endpush