@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('b.index')}}">Company Booking</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    <div>
        <a href="{{route('reservations.index')}}"><button class="btn btn-warning">Back</button></a>
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

<form action="{{route('checkin.company.express.save')}}" method="post" id="my_form">
@csrf
    
<input type="hidden" name="type" value="1"/>
<input type="hidden" name="bill_options" value="2"/>
<input type="hidden" name="acc_paid_by" value="2"/>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
            <h6 class="card-title">Reservation Details</h6>
            <p class="text-muted mb-3"></p> 

            {{-- company details --}}
            <div class="row">
                <div class="mb-3 col-md-5">
                    <div class="row">
                        <div class="col-md-8">
                            <label for="account_id" class="form-label">Account</label>
                            <select name="account_id" id="account_id" class="form-select">
                                <option value="0">Choose account...</option>
                                @foreach ($accounts as $acc)
                                    <option value="{{$acc->id}}">{{$acc->name}}</option>
                                @endforeach
                            </select>
                            <span class="text-danger" id="account_id_error"></span>
                        </div>
                        <div class="col-md-4">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#add_account" style="width: 100%; height: 100%;">+</button>
                        </div>
                    </div>
                </div>

                <div class="mb-3 col-md-3">
                    <label for="num_of_vehicles" class="form-label">Number of vehicles</label>
                    <input required type="text" name="num_of_vehicles" class="form-control" id="num_of_vehicles" autocomplete="off" placeholder="Number of vehicles">
                    <span class="text-danger" id="num_of_vehicles_error"></span>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="extras_paid_by" class="form-label">Extras paid by</label>
                    <select name="extras_paid_by" id="extras_paid_by" class="form-select">
                        <option value="0">Choose extras paid by...</option>
                        <option value="2">Company</option>
                        <option value="1">Guest</option>
                    </select>
                    <span class="text-danger" id="extras_paid_by_error"></span>
                </div>
            </div>
            
            <div class="row" style="border-bottom: 1px solid rgba(0, 0, 0, 0.158)">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="extra_info" class="form-label">Extra Information</label>
                        <textarea class="form-control" name="extra_info" id="extra_info" rows="5" placeholder="Extra information"></textarea>
                    </div>
                </div>
            </div>

            <br>

            <div class="row mb-3" style="border-bottom: 1px solid rgba(0, 0, 0, 0.158)">
                <h6 class="mb-3 mt-3">CHECK IN DETAILS</h6>

                <div class="mb-3 col-md-6">
                    <label for="check_in" class="form-label">Check in</label>
                    <input required type="datetime-local" class="form-control" name="check_in" id="check_in" autocomplete="off">
                    <span class="text-danger" id="check_in_error"></span>
                </div>

                <div class="mb-3 col-md-6">
                    <label for="check_out" class="form-label">Check out</label>
                    <input required type="datetime-local" class="form-control" name="check_out" id="check_out" autocomplete="off">
                    <span class="text-danger" id="check_out_error"></span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="num_of_days" class="form-label">No of days</label>
                    <input type="text" class="form-control" name="num_of_days" id="num_of_days" autocomplete="off" placeholder="Ex: 1">
                    <span class="text-danger" id="num_of_days_error"></span>
                </div>


                <div class="col-md-6 mb-3">
                    <label for="pax" class="form-label">Pax</label>
                    <input type="text" class="form-control" autocomplete="off" name="pax" id="pax" placeholder="Pax">
                    <span class="text-danger" id="pax_error"></span>
                </div>

                
                <div class="mb-3 col-md-6">
                    <label for="delivery_date" class="form-label">Package</label>
                    <select name="package_id" id="package_id" class="form-select mb-3">
                        <option selected value="0">Choose package...</option>
                        @foreach ($packages as $package)
                            <option value="{{$package->id}}" data-price="{{$package->price}}">{{$package->name}}</option>
                        @endforeach
                    </select>
                    <span class="text-danger" id="package_id_error"></span>
                </div>

                <input type="hidden" name="number_of_days" id="number_of_days">
            </div>

            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-success" id="submit-btn">Save Details</button>
            </div>
        </div>
      </div>
    </div>
</div>

</form>



<div class="modal fade" id="add_account" tabindex="-1" aria-labelledby="add_account" aria-hidden="true">
    <form action="{{route('account.approval.store')}}" method="post">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Account Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="type" class="form-label">Organization Name</label>
                        <input type="text" required name="name" class="form-control" id="type" autocomplete="off" placeholder="Ex: Name">
                    </div>

                    <input type="hidden" name="type" value="1">


                    <div class="mb-3">
                        <label for="vat_registration_number" class="form-label">Vat registration number</label>
                        <input type="text" required name="vat_registration_number" class="form-control" id="email" autocomplete="off" placeholder="Ex: XXXXXX">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" required name="email" class="form-control" id="email" autocomplete="off" placeholder="Ex: johndoe@mail.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telephone" class="form-label">Telephone</label>
                                <input type="text" required name="telephone" class="form-control" id="telephone" autocomplete="off" placeholder="Ex: 07XX XXX XXX">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" required name="location" class="form-control" id="location" autocomplete="off" placeholder="Ex: Nairobi, Kenya">
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-bs-dismiss="modal" class="btn btn-success">Cancel</button>
                    <button type="submit" id="submit-btn" class="btn btn-primary">Save</button>
                </div>
            </div>  
        </div>
    </form>
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

            function dateDifference(date1, date2) {
                let Difference_In_Time = date2.getTime() - date1.getTime();
                let Difference_In_Days = Math.round(Difference_In_Time / (1000 * 3600 * 24));
                return Difference_In_Days;
            }

            $('#package_id').on('change', (e) => {
                let package = $('#package_id').val();
                let price = $('#package_id').find(':selected').data('price');
                let date1 = new Date($('#check_in').val());
                let date2 = new Date($('#check_out').val());
                let numOfDays = dateDifference(date1, date2);
                let accommodation_total = price * numOfDays;
                $('#number_of_days').val(numOfDays);
                console.log(numOfDays);
            })

            $('#submit-btn').on('click', (e) => {
                e.preventDefault();

                if ($('#account_id').find(':selected').val() == 0) {
                    $('#account_id').focus();
                    $('#account_id_error').text('field required');
                    return;
                } else {
                    $('#account_id_error').text('');
                }

                if (!$('#num_of_vehicles').val()) {
                    $('#num_of_vehicles').focus();
                    $('#num_of_vehicles_error').text('field required');
                    return;
                
                } else {
                    $('#num_of_vehicles_error').text('');
                }

                if ($('#extras_paid_by').find(':selected').val() == 0) {
                    $('#extras_paid_by').focus();
                    $('#extras_paid_by_error').text('field required');
                    return;
                
                } else {
                    $('#extras_paid_by_error').text('');
                }

                if (!$('#check_in').val()) {
                    $('#check_in').focus();
                    $('#check_in_error').text('field required');
                    return;
                
                } else {
                    $('#check_in_error').text('');
                }

                if (!$('#check_out').val()) {
                    $('#check_out').focus();
                    $('#check_out_error').text('field required');
                    return;
                
                } else {
                    $('#check_out_error').text('');
                }

                if (!$('#num_of_days').val()) {
                    $('#num_of_days').focus();
                    $('#num_of_days_error').text('field required');
                    return;
                
                } else {
                    $('#num_of_days_error').text('');
                }

                if (!$('#pax').val()) {
                    $('#pax').focus();
                    $('#pax_error').text('field required');
                    return;
                
                } else {
                    $('#pax_error').text('');
                }

                if ($('#package_id').find(':selected').val() == 0) {
                    $('#package_id').focus();
                    $('#package_id_error').text('field required');
                    return;
                
                } else {
                    $('#package_id_error').text('');
                }

                
                $('#my_form').submit();
            })
        } );

    </script>
@endpush