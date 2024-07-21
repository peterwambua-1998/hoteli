@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('b.index')}}">Single Reservation</a></li>
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

<form action="{{route('b.store')}}" method="post" id="my_form">
@csrf
    
<input type="hidden" name="type" value="1"/>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
            <h6 class="card-title">Reservation Details</h6>
            <p class="text-muted mb-3"></p> 
            <div class="row">
                <div class="mb-3 col-md-3">
                    <label for="surname" class="form-label">Surname</label>
                    <input type="text" class="form-control" name="surname" id="surname" autocomplete="off">
                </div>

                <div class="mb-3 col-md-3">
                    <label for="other_names" class="form-label">Other names</label>
                    <input type="text" class="form-control" name="other_names" id="other_names" autocomplete="off">
                </div>

                <div class="mb-3 col-md-3">
                    <label for="profession" class="form-label">Profession</label>
                    <input type="text" class="form-control" name="profession" id="profession" autocomplete="off">
                </div>

                <div class="mb-3 col-md-3">
                    <label for="id_number" class="form-label">ID/Passport NO</label>
                    <input type="text" class="form-control" name="id_number" id="id_number" autocomplete="off">
                </div>

                <div class="mb-3 col-md-3">
                    <label for="single_email" class="form-label">Email</label>
                    <input type="text" class="form-control" name="single_email" id="single_email" autocomplete="off">
                </div>

                <div class="mb-3 col-md-3">
                    <label for="telephone" class="form-label">Telephone</label>
                    <input type="text" class="form-control" name="telephone" id="telephone" autocomplete="off">
                </div>

                <div class="mb-3 col-md-3">
                    <label for="location" class="form-label">County of residence</label>
                    <input type="text" class="form-control" name="location" id="location" autocomplete="off">
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
                    <label for="delivery_date" class="form-label">Package</label>
                    <select name="meal_plan" id="meal_plan" class="form-select mb-3">
                        <option selected value="0">Choose package...</option>
                        @foreach ($mealPlans as $plan)
                            <option value="{{$plan->id}}">{{$plan->name}}</option>
                        @endforeach
                    </select>
                    <span class="text-danger" id="meal_plan_error"></span>
                </div>

                <div class="mb-3 col-md-3">
                    <label for="delivery_date" class="form-label">Checkin under company</label>
                    <select name="account_id" id="account_id" class="form-select mb-3">
                        <option selected value="0">Choose company...</option>
                        @foreach ($accounts as $account)
                            <option value="{{$account->id}}">{{$account->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3 col-md-3">
                    <label for="num_of_vehicles" class="form-label">Number of vehicles</label>
                    <input required type="text" name="num_of_vehicles" class="form-control" id="num_of_vehicles" autocomplete="off" placeholder="Number of vehicles">
                </div>
               
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="extra_info" class="form-label">Extra Information</label>
                        <textarea class="form-control" name="extra_info" id="extra_info" rows="5" placeholder="Extra information"></textarea>
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <button class="btn btn-success" id="submit-btn">Save Details</button>
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
            $('#dataTableExample').DataTable({
                language: { searchPlaceholder: "Search records", search: "",},
            });

            $('#submit-btn').on('click', (e) => {
                e.preventDefault();

                if ($('#meal_plan').val() == 0) {
                    $('#meal_plan_error').text('field required');
                    return;
                } else {
                    $('#meal_plan_error').text('');
                }

                $('#my_form').submit();
            })
        } );

    </script>
@endpush