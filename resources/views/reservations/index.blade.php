@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-around">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('b.index')}}">Reservations</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    <div>
        <a class="btn btn-primary" style="width: 100%;" href="#" data-bs-toggle="modal" data-bs-target="#exampleModal"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px;" name="add-circle-outline"></ion-icon> Add Reservation</a>
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
            <h6 class="card-title">Reservations Table</h6>
            <p class="text-muted mb-3"></p> 
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTableExample">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Arrival</th>
                            <th>Departure</th>
                            <th>Package</th>
                            <th>Pax</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $number = 1; ?>
                        @foreach ($reservations as $r)
                        <tr>
                            <td>{{$number}}<?php $number++; ?></td>
                           
                            <td>
                                @if ($r->type == 1)
                                {{$r->surname}}
                                @endif

                                @if ($r->type == 2)
                                {{$r->org_name}}
                                @endif
                            </td>
                            <td>
                                @if ($r->type == 1)
                                    single
                                @endif

                                @if ($r->type == 2)
                                    group
                                @endif
                            </td>
                            <td>
                                @if ($r->status == 1)
                                <span class="badge bg-success">Active</span>
                                @endif

                                @if ($r->status == 2)
                                <span class="badge bg-danger">Cancelled</span>
                                @endif

                                @if ($r->status == 3)
                                <span class="badge bg-warning">changed board</span>
                                @endif
                            </td>
                            <td>{{$r->date_arrival}}</td>
                            <td>{{$r->date_departure}}</td>
                            <td>{{$r->package->name}}</td>
                            <td>{{$r->num_of_pax}}</td>
                            <td>
                                @if ($r->amend_status == 2)
                                    @if (Auth::user()->role == 1)
                                        <a href="#" class="ml-2" data-bs-toggle="modal" data-bs-target="#sure-{{$r->id}}">Approve</a>
                                    @endif
                                @endif
                                @if ($r->amend_status == null || $r->amend_status == 1)
                                    <a href="#" class="ml-2" data-bs-toggle="modal" data-bs-target="#edit-{{$r->id}}">Adjust</a>
                                @endif
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

@foreach ($reservations as $r)
<div class="modal fade" id="sure-{{$r->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('b.approve')}}" method="post">
        @csrf
        <input type="hidden" name="reservation_id" value="{{$r->id}}">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Adjust Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <p class="mb-1"><span class="text-success">Type</span>: 
                            @if($r->change_status_to == 3) Changing Board to {{app\Models\MealPlan::where('id', '=', $r->amended_board)->first()->name}} @endif
                            @if($r->change_status_to == 2) Cancel Booking @endif
                        </p>
                        <p class="mb-3">
                            <span class="text-success">Reason:</span> {{$r->reason_for_change}}
                        </p>
                    </div>
                    <div class="mb-3">
                        <label for="approve">Approve amendment?</label>
                        <select name="amend_type" id="amend_type" class="form-select">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"  data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Submit</button>
                </div>
            </div>  
        </div>
    </form>
</div>
@endforeach

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Reservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-footer">
                <form action="{{route('b.create')}}" method="post">
                    @csrf
                    <input type="hidden" name="type" value="1">
                    <button type="submit" class="btn btn-secondary">Single</button>
                </form>
                <form action="{{route('b.create')}}" method="post">
                    @csrf
                    <input type="hidden" name="type" value="2">
                    <button type="submit" class="btn btn-primary">Group</button>
                </form>
            </div>
        </div>  
    </div>
</div>

@foreach ($reservations as $r)
<div class="modal fade" id="edit-{{$r->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Adjust Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#board-{{$r->id}}" >Change Package</button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cancel-{{$r->id}}">Cancel Booking</button>
            </div>
        </div>  
    </div>
</div>
@endforeach


@foreach ($reservations as $r)
<div class="modal fade" id="cancel-{{$r->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('b.adjust')}}" method="post">
        @csrf
        <input type="hidden" name="change_status_to" value="2">
        <input type="hidden" name="reservation_id" value="{{$r->id}}">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Adjust Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <label for="reason_for_change" class="form-label">Reason for change</label>
                    <textarea name="reason_for_change" class="form-control mb-3" required id="reason_for_change" cols="30" rows="10"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Cancel Booking</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Exit</button>
                </div>
            </div>  
        </div>
    </form>
</div>
@endforeach


@foreach ($reservations as $r)
<div class="modal fade" id="board-{{$r->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('b.adjust')}}" method="post">
        @csrf
        <input type="hidden" name="change_status_to" value="3">
        <input type="hidden" name="reservation_id" value="{{$r->id}}">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Adjust Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="board_id">Choose board</label>
                        <select name="board_id" id="board_id" class="form-select">
                            <option value="0">Choose board</option>
                            @foreach ($mealPlans as $plan)
                                <option value="{{$plan->id}}">{{$plan->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="reason_for_change">Reason for change</label>
                        <textarea class="form-control" name="reason_for_change" required id="reason_for_change"  rows="5" placeholder="Reason for change"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Adjust Board</button>
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