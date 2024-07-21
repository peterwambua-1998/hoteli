@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('b.index')}}">Group Reservation</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    <div>
        <a href="{{route('b.index')}}"><button class="btn btn-warning">Back</button></a>
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

<form action="{{route('b.store')}}" method="post">
@csrf

<input type="hidden" name="type" value="2"/>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
            {{-- invoice details --}}
            <div class="mb-2">
                <h4 class="mb-4">Reservation Details</h4>
                <div class="row">
                    <div class="mb-3 col-md-3">
                        <label for="" class="form-label">Group Name</label>
                        <input required type="text" class="form-control" name="org_name" id="org_name" autocomplete="off">
                    </div>

                    <div class="mb-3 col-md-3">
                        <label for="" class="form-label">Group Email</label>
                        <input required type="text" class="form-control" name="org_email" id="org_email" autocomplete="off">
                    </div>

                    <div class="mb-3 col-md-3">
                        <label for="" class="form-label">Telephone</label>
                        <input required type="text" class="form-control" name="telephone" id="telephone" autocomplete="off">
                    </div>

                    <div class="mb-3 col-md-3">
                        <label for="" class="form-label">Location</label>
                        <input required type="text" class="form-control" name="location" id="location" autocomplete="off">
                    </div>

                    <div class="mb-3 col-md-3">
                        <label for="delivery_date" class="form-label">Package</label>
                        <select name="meal_plan" id="meal_plan" class="form-select mb-3">
                            <option selected value="0">Choose package...</option>
                            @foreach ($mealPlans as $plan)
                                <option value="{{$plan->id}}">{{$plan->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 col-md-3">
                        <label for="date_arrival" class="form-label">Arrival Date</label>
                        <input required type="date" class="form-control" name="date_arrival" id="date_arrival" autocomplete="off">
                    </div>

                    <div class="mb-3 col-md-3">
                        <label for="date_departure" class="form-label">Departure Date</label>
                        <input required type="date" class="form-control" name="date_departure" id="date_departure" autocomplete="off">
                    </div>


                    <div class="mb-3 col-md-3">
                        <label for="num_of_vehicles" class="form-label">Number of vehicles</label>
                        <input required type="text" name="num_of_vehicles" class="form-control" id="num_of_vehicles" autocomplete="off" placeholder="Number of vehicles">
                    </div>

                    <div class="mb-3">
                        <label for="extra_info" class="form-label">Extra Information</label>
                        <textarea class="form-control" name="extra_info" id="extra_info" rows="5" placeholder="Extra information"></textarea>
                    </div>
                </div>
            </div>
            {{-- invoice items --}}
            <div>
                {{-- item --}}
                <table class="table table-bordered" id="dataTableExample">
                    <thead style="background: rgb(6, 181, 0); ">
                        <tr >
                            <th style="width: 40%; color:black">Member Name</th>
                            <th style="width: 40%; color:black">Room Type</th>
                            <th style="color:black">Action</th>
                        </tr>
                    </thead>
                    <tbody id="table-row">
                        <tr>
                            <td>
                                <input type="text" required name="org_member_name[]" class="form-control org_member_name" autocomplete="off">
                            </td>
                            <td>
                                <select name="room_type[]" id="room_type" class="form-select">
                                    <option value="0">Choose room type...</option>
                                    @foreach ($roomTypes as $type)
                                       <option value="{{$type->id}}">{{$type->type}}</option> 
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="mt-2">
                    <button type="button" class="btn btn-success" id="add-row" style="width: 20%">
                        <i data-feather="plus-circle" ></i>
                    </button>

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

@push('custom-scripts')
    <script defer>
        $(document).ready( function () {
            // add elements 
            $('#add-row').on('click', () => {
                let template = `
                    <tr>
                        <td>
                            <input type="text" required name="org_member_name[]" class="form-control org_member_name" autocomplete="off">
                        </td>
                        <td>
                            <select name="room_type[]" id="room_type" class="form-select">
                                <option value="0">Choose room type...</option>
                                @foreach ($roomTypes as $type)
                                    <option value="{{$type->id}}">{{$type->type}}</option> 
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="red" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="minus-row"><circle cx="12" cy="12" r="10"></circle><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                        </td>
                    </tr>
                `;

                $('#table-row').append(template);
                minus();
            })


            function minus() {
                $('.minus-row').each((index, el) => {
                    $(el).on('click', () => {
                        let parent = $(el).parent().parent().remove();
                    })
                })
            }

            minus();
        });
    </script>
@endpush