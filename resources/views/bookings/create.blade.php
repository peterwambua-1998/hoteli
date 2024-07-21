@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <style>
        .autocomplete {
            position: relative;
            display: inline-block;
            width: 300px;
        }

       

        .autocomplete-items {
            position: absolute;
            border: 1px solid #ddd;
            border-top: none;
            z-index: 99;
            top: 100%;
            left: 0;
            right: 0;
            background-color: #fff;
            max-height: 200px;
            overflow-y: auto;
            border-radius: 4px;
        }

        .autocomplete-items div {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #ddd;
        }

        .autocomplete-items div:hover {
            background-color: #f1f1f1;
        }

        .autocomplete-active {
            background-color: #e9e9e9 !important;
        }
    </style>
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('b.index')}}">Guest Booking</a></li>
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

<form action="{{route('checkin.express.save')}}" method="post" id="my_form">
@csrf
    
<input type="hidden" name="type" value="1"/>
<input type="hidden" name="bill_options" value="1"/>
<input type="hidden" name="existing_account_id" id="existing_account_id" value="0"/>


<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
            <h6 class="card-title">Reservation Details</h6>
            <p class="text-muted mb-3"></p> 
            <div class="row">
                <div class="mb-3 col-md-3">
                    <label for="surname" class="form-label">Surname</label>
                    <div>
                        <input type="text" class="form-control" name="surname" id="surname" autocomplete="off">
                        <div class="outer-suggestion" style="position: relative; width: 100%; z-index: 100;">
                            <div class="suggestion" style="position: absolute; background: #f9fafb; border-radius: 5px; color: black; border: 1px solid #94a3b8; width: 100%; padding: 8px;">
                            </div>
                        </div>
                    </div>
                    <span class="text-danger" id="surname_error"></span>
                </div>

                <div class="mb-3 col-md-3">
                    <label for="other_names" class="form-label">Other names</label>
                    <input type="text" class="form-control" name="other_names" id="other_names" autocomplete="off">
                    <span class="text-danger" id="other_names_error"></span>
                </div>

                <div class="mb-3 col-md-3">
                    <label for="profession" class="form-label">Profession</label>
                    <input type="text" class="form-control" name="profession" id="profession" autocomplete="off">
                    <span class="text-danger" id="profession_error"></span>
                </div>

                <div class="mb-3 col-md-3">
                    <label for="id_number" class="form-label">ID/Passport NO</label>
                    <input type="text" class="form-control" name="id_number" id="id_number" autocomplete="off">
                    <span class="text-danger" id="id_number_error"></span>
                </div>

                <div class="mb-3 col-md-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" name="email" id="email" autocomplete="off">
                    <span class="text-danger" id="email_error"></span>
                
                </div>

                <div class="mb-3 col-md-3">
                    <label for="telephone" class="form-label">Telephone</label>
                    <input type="text" class="form-control" name="telephone" id="telephone" autocomplete="off">
                    <span class="text-danger" id="telephone_error"></span>
                </div>

                <div class="mb-3 col-md-3">
                    <label for="location" class="form-label">County of residence</label>
                    <input type="text" class="form-control" name="location" id="location" autocomplete="off">
                    <span class="text-danger" id="location_error"></span>
                </div>

                <div class="mb-3 col-md-3">
                    <label for="num_of_vehicles" class="form-label">Number of vehicles</label>
                    <input required type="text" name="num_of_vehicles" class="form-control" id="num_of_vehicles" autocomplete="off" placeholder="Number of vehicles">
                    <span class="text-danger" id="num_of_vehicles_error"></span>
                </div>

                <div class="mb-3 col-md-3">
                    <label for="delivery_date" class="form-label">Main paid by</label>
                    <select name="acc_paid_by" id="acc_paid_by" class="form-select mb-3">
                        <option value="0">Choose main bills paid by...</option>
                        <option value="1">Guest</option>
                        <option value="2">Company</option>
                    </select>
                    <span class="text-danger" id="acc_paid_by_error"></span>
                </div>

                <div class="mb-3 col-md-3">
                    <label for="delivery_date" class="form-label">Extras bills paid by</label>
                    <select name="extras_paid_by" id="extras_paid_by" class="form-select mb-3">
                        <option value="0">Choose extra bills paid by...</option>
                        <option value="1">Guest</option>
                        <option value="2">Company</option>
                    </select>
                    <span class="text-danger" id="extras_paid_by_error"></span>
                </div>

               
                <div class="mb-3 col-md-4">
                    <div class="row">
                        <div class="col-md-8">
                            <label for="delivery_date" class="form-label">Check-in under company</label>
                            <select name="company_id" id="company_id" class="form-select mb-3">
                                <option selected value="0">Choose company...</option>
                                @foreach ($accounts as $account)
                                    <option value="{{$account->id}}">{{$account->name}}</option>
                                @endforeach
                            </select>
                            <span class="text-danger" id="company_id_error"></span>
                        </div>
                        <div class="col-md-4">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#add_account" style="width: 100%; height: 100%;">+</button>
                        </div>
                    </div>
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
                    <label for="room_id" class="form-label">Room</label>
                    <select multiple="multiple" name="room_id[]" id="room_id" class="js-example-basic-multiple form-select">
                        <option value="0">Choose room</option>
                        @foreach ($rooms as $room)
                            <option value="{{$room->id}}" data-price="{{$room->price}}">{{$room->description}} - {{$room->number}} ({{$room->type->type}})</option>
                        @endforeach
                    </select>
                    <span class="text-danger" id="room_id_error"></span>
                </div>

                

                <div class="col-md-6 mb-3">
                    <label for="underage_child" class="form-label">Child under 8</label>
                    <select name="underage_child" id="underage_child" class="form-select">
                        <option value="2">Choose yes or no ...</option>
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                    <span class="text-danger" id="underage_child_error"></span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="different_room" class="form-label">Child different room</label>
                    <label for="" style="font-size: 12px" class="text-muted">( select no if there is no under age child )</label>
                    <select name="different_room" id="different_room" class="form-select">
                        <option value="2">Choose yes or no ...</option>
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                    <span class="text-danger" id="different_room_error"></span>
                </div>
               

                <div class="col-md-6 mb-3">
                    <label for="pax" class="form-label">Pax Adults</label>
                    <input type="text" class="form-control" autocomplete="off" name="pax" id="pax" placeholder="Ex: 5" required>
                    <span class="text-danger" id="pax_error"></span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="num_of_underage" class="form-label">Pax Under 8</label>
                    <input type="text" class="form-control" autocomplete="off" name="num_of_underage" id="num_of_underage" placeholder="Ex: 5">
                    <span class="text-danger" id="num_of_underage_error"></span>
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
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>  
        </div>
    </form>
</div>

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
  <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>

@endpush

@push('custom-scripts')
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script defer>
        $(document).ready( function () {
            hideOuterSuggestionDiv();

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
            });

            function hideOuterSuggestionDiv() {
                $('.outer-suggestion').hide();
            }
           

            function inputSuggestions(params) {
                $('#surname').on('input', () => {
                    let element = $('#surname');
                    let parent = $(element).parent();
                    let suggestion_div = parent.find('.suggestion');
                    let outer_suggestion = parent.find('.outer-suggestion');
                    let data = new FormData;
                    data.append('_token', '{{csrf_token()}}');
                    data.append('input_query', $(element).val());
                    $.ajax({
                        type: 'POST',
                        url: "{{route('accounts.query')}}",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: data,
                        error: (err) => {
                            console.log(err);
                        },
                        success: (response) => {
                            console.log(response);
                            if (response.length == 0) {
                                outer_suggestion.hide();
                            }

                            let template = '';
                            for (let i = 0; i < response.length; i++) {
                                let element = response[i];
                                template += `
                                    <p class="mb-2 suggestion-content" data-id="${element.id}" data-name="${element.name}" data-profession="${element.profession}" data-telephone="${element.telephone}" data-idNumber="${element.id_number}" data-email="${element.email}" data-location="${element.location}">${element.name}</p>
                                `;
                            }
                            suggestion_div.html(template);
                            outer_suggestion.show();
                            clickSuggestion();
                            if (response.length == 0) {
                                outer_suggestion.hide();
                            }
                        }
                    })
                });
            }

            function clickSuggestion() {
                $('.suggestion-content').on('click', () => {
                    let element = $('.suggestion-content');
                    let parent = $(element).parent().parent().hide();
                    let parentDiv = $(element).parent().parent().parent().parent().parent();
                    console.log(parentDiv, $(element).data('profession'), $(element).data('telephone'));

                    let item_id = $(element).data('id');
                    let item_name = $(element).data('name');
                    let item_profession = $(element).data('profession');
                    let id_number = $(element).data('idnumber');
                    let email = $(element).data('email');
                    let telephone = $(element).data('telephone');
                    let location = $(element).data('location');

                    parentDiv.find('#profession').val(item_profession);
                    parentDiv.find('#id_number').val(id_number);
                    parentDiv.find('#email').val(email);
                    parentDiv.find('#telephone').val(telephone);
                    parentDiv.find('#surname').val(item_name);
                    parentDiv.find('#location').val(location);

                    $('#existing_account_id').val(item_id)
                });
            }

            inputSuggestions();

            $('#submit-btn').on('click', (e) => {
                e.preventDefault();

                if (!$('#surname').val()) {
                    $('#surname_error').text('field required');
                    $('#surname').focus();
                    return;
                } else {
                    $('#surname_error').text('');
                }

                

                if (!$('#profession').val()) {
                    $('#profession_error').text('field required');
                    $('#profession').focus();
                    return;
                } else {
                    $('#profession_error').text('');
                }

                if (!$('#id_number').val()) {
                    $('#id_number_error').text('field required');
                    $('#id_number').focus();
                    return;
                } else {
                    $('#id_number_error').text('');
                }

                if (!$('#email').val()) {
                    $('#email_error').text('field required');
                    $('#email').focus();
                    return;
                } else {
                    $('#email_error').text('');
                }

                if (!$('#telephone').val()) {
                    $('#telephone_error').text('field required');
                    $('#telephone').focus();
                    return;
                } else {
                    $('#telephone_error').text('');
                }

                if (!$('#location').val()) {
                    $('#location_error').text('field required');
                    $('#location').focus();
                    return;
                } else {
                    $('#location_error').text('');
                }

                if (!$('#num_of_vehicles').val()) {
                    $('#num_of_vehicles_error').text('field required');
                    $('#num_of_vehicles').focus();
                    return;
                } else {
                    $('#num_of_vehicles_error').text('');
                }

                if ($('#acc_paid_by').find(':selected').val() == 0) {
                    $('#acc_paid_by_error').text('field required');
                    $('#acc_paid_by').focus();
                    return;
                } else {
                    $('#acc_paid_by_error').text('');
                }

                if ($('#extras_paid_by').find(':selected').val() == 0) {
                    $('#extras_paid_by_error').text('field required');
                    $('#extras_paid_by').focus();
                    return;
                } else {
                    $('#extras_paid_by_error').text('');
                }

                if ($('#extras_paid_by').find(':selected').val() == 2 || $('#acc_paid_by').find(':selected').val() == 2) {
                    if ($('#company_id').find(':selected').val() == 0) {
                        $('#company_id_error').text('field required');
                        $('#company_id').focus();
                        return;
                    } else {
                        $('#company_id_error').text('');
                    }
                } else {
                    $('#company_id_error').text('');
                }

                if (!$('#check_in').val()) {
                    $('#check_in_error').text('field required');
                    $('#check_in').focus();
                    return;
                } else {
                    $('#check_in_error').text('');
                }

                if (!$('#check_out').val()) {
                    $('#check_out_error').text('field required');
                    $('#check_out').focus();
                    return;
                } else {
                    $('#check_out_error').text('');
                }

                if ($('#room_id').find(':selected').val() == 0) {
                    $('#room_id_error').text('field required');
                    $('#room_id').focus();
                    return;
                } else {
                    $('#room_id_error').text('');
                }

                if ($('#underage_child').find(':selected').val() == 2) {
                    $('#underage_child_error').text('field required');
                    $('#underage_child').focus();
                    return;
                } else {
                    $('#underage_child_error').text('');
                }

                if ($('#different_room').find(':selected').val() == 2) {
                    $('#different_room_error').text('field required');
                    $('#different_room').focus();
                    return;
                } else {
                    $('#different_room_error').text('');
                }

                if (!$('#pax').val()) {
                    $('#pax_error').text('field required');
                    $('#pax').focus();
                    return;
                } else {
                    $('#pax_error').text('');
                }

                if (!$('#num_of_underage').val()) {
                    $('#num_of_underage_error').text('field required');
                    $('#num_of_underage').focus();
                    return;
                } else {
                    $('#num_of_underage_error').text('');
                }

                if ($('#package_id').find(':selected').val() == 0) {
                    $('#package_id_error').text('field required');
                    $('#package_id').focus();
                    return;
                } else {
                    $('#package_id_error').text('');
                }

                if ($('#num_of_underage').find(':selected').val() == 2) {
                    $('#num_of_underage_error').text('field required');
                    $('#num_of_underage').focus();
                    return;
                } else {
                    $('#num_of_underage_error').text('');
                }

                $('#my_form').submit();
            });
        } );

    </script>
@endpush