@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <style>
        .package:hover {
            cursor: pointer;
        }
    </style>
  @endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('reservations.index')}}">Bookings</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    <div>
        <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" style="width: 100%;" ><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px;" name="add-circle-outline"></ion-icon> Add Booking</a>
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
            <h6 class="card-title">Bookings Table</h6>
            <p class="text-muted mb-3"></p> 
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTableExample">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Rooms</th>
                            <th>Package</th>
                            <th>Main paid by</th>
                            <th>extras paid by</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $number = 1; ?>
                        @foreach ($captain as $reservation)
                        <tr>
                            <td>{{$number}}<?php $number++; ?></td>
                            <td>{{$reservation->account->name}}</td>
                            <td>{{$reservation->check_in->format('j F Y, g:i A')}}</td>
                            <td>{{$reservation->check_out->format('j F Y, g:i A')}}</td>
                            <td>{{$reservation->rooms}}</td>
                            <td class="package" data-bs-toggle="modal" data-bs-target="#package-{{$reservation->id}}">
                                {{$reservation->package->description}}
                            </td>
                            <td>
                                @if ($reservation->acc_paid_by == 1)
                                    Guest
                                @endif

                                @if ($reservation->acc_paid_by == 2)
                                    <?php $acc = App\Models\Account::where('id','=', $reservation->company_id)->first()  ?>
                                    Company @if ($acc) ({{$acc->name}}) @endif
                                @endif
                            </td>
                            <td>
                                @if ($reservation->extras_paid_by == 1)
                                    Guest
                                @endif

                                @if ($reservation->extras_paid_by == 2)
                                    Company
                                @endif
                            </td>
                            <td>
                                {{-- add account_id after reservation  --}}
                                <a href="{{route('booking.checkout',[ $reservation->id, $reservation->account->id])}}" style="color: green">checkout</a>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Check-ing Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-footer">
                <a href="#" data-bs-toggle="modal" data-bs-target="#expressCheckinOptions" class="btn btn-success">Express Check-in</a>
                <a href="#" class="btn btn-primary">Reserved check-in</a>
            </div>
        </div>  
    </div>
</div>

<div class="modal fade" id="expressCheckinOptions" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Check-ing Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-footer">
                <a href="{{route('checkin.create')}}" class="btn btn-success">Guest</a>
                <a href="{{route('checkin.create.company')}}" class="btn btn-primary">Company</a>
            </div>
        </div>  
    </div>
</div>

@foreach ($captain as $r)
<div class="modal fade" id="package-{{$r->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Package Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <p>Package: </p>
                    <p class="mb-1" style="font-weight: bold">{{$r->package->name}}</p>
                    <p style="font-weight: bold">{{$r->package->description}}</p>
                </div>

                <div class="mb-3">
                    <p>Package Content: </p>
                    @foreach ($r->package->items as $item)
                        <?php $facility = App\Models\PackageFacility::where('id','=', $item->package_facility_id)->first(); ?>
                        @if ($facility)
                        <p style="font-weight: bold">{{$facility->name}}</p>
                            
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-primary" data-bs-dismiss="modal">Close</a>
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