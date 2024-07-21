@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  @endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb" style="width: 75%">
      <li class="breadcrumb-item"><a href="{{route('rooms.index')}}">Rooms</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    <div>
        <a class="btn btn-primary" style="width: 100%;" href="#" data-bs-toggle="modal" data-bs-target="#exampleModal"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px;" name="add-circle-outline"></ion-icon> Add Room</a>
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
            <h6 class="card-title">Rooms Table</h6>
            <p class="text-muted mb-3"></p> 
            <div class="table-responsive">
                
                <table class="table table-bordered table-striped" id="dataTableExample">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Number</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Capacity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $number = 1; ?>
                        @foreach ($rooms as $room)
                        <tr>
                            <td>{{$number}}<?php $number++; ?></td>
                            <td>{{$room->number}}</td>
                            <td>{{$room->description}}</td>
                            <td>{{$room->type->type}}</td>
                            <td>
                                @if ($room->room_status == 1)
                                <span class="badge bg-success">Occupied</span>
                                @endif

                                @if ($room->room_status == 0)
                                <span class="badge bg-info">Vacant</span>
                                @endif
                            </td>
                            <td>{{$room->capacity}}</td>
                            <td>
                                <a href="#" class="ml-5" data-bs-toggle="modal" data-bs-target="#edit-{{$room->id}}">Edit</a>
                                <a href="#">Delete</a>
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
    <form action="{{route('rooms.store')}}" method="post">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="type" class="form-label">Room Number</label>
                        <input type="text" required name="number" class="form-control" id="type" autocomplete="off" placeholder="Ex: 10">
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Room Type</label>
                        <select class="form-select mb-3" name="room_type">
                            <option selected="0">Choose Room Type</option>
                            @foreach ($roomTypes as $roomType)
                                <option value="{{$roomType->id}}">{{$roomType->type}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Room Description</label>
                        <input type="text" required name="description" class="form-control" id="description" autocomplete="off" placeholder="Description">
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Room Status</label>
                        <select class="form-select mb-3" name="room_status">
                            <option selected="0">Choose Room Status</option>
                            <option value="0">Vacant</option>
                            <option value="1">Occupied</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Room Capacity</label>
                        <input type="text" required name="capacity" class="form-control" id="type" autocomplete="off" placeholder="Ex: 2">
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

@foreach ($rooms as $room)
<div class="modal fade" id="edit-{{$room->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('rooms.update', $room->id)}}" method="post">
        @csrf
        @method('PATCH')
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="type" class="form-label">Room Number</label>
                        <input type="text" value="{{$room->number}}" required name="number" class="form-control" id="type" autocomplete="off" placeholder="Ex: 10">
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Room Type</label>
                        <select class="form-select mb-3" name="room_type">
                            <option selected="0">Choose Room Type</option>
                            @foreach ($roomTypes as $roomType)
                                <option @if ($roomType->id == $room->type->id) selected @endif value="{{$roomType->id}}">{{$roomType->type}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Room Description</label>
                        <input type="text" value="{{$room->description}}" required name="description" class="form-control" id="description" autocomplete="off" placeholder="Description">
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Room Status</label>
                        <select class="form-select mb-3" name="room_status">
                            <option selected>Choose Room Status</option>
                            <option @if($room->room_status == 0) selected @endif value="0">Vacant</option>
                            <option @if($room->room_status == 1) selected @endif value="1">Occupied</option>
                            
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Room Capacity</label>
                        <input type="text" value="{{$room->capacity}}" required name="capacity" class="form-control" id="type" autocomplete="off" placeholder="Ex: 2">
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
@endforeach


@endsection
@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush

@push('custom-scripts')
    <script defer>
        $(document).ready(function () {
            $('#dataTableExample').DataTable({
                language: { searchPlaceholder: "Search records", search: "",},
            });

        });
    </script>
@endpush