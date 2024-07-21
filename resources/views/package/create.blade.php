@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('packages.index')}}">Packages</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    <div>
        <a class="btn btn-warning" style="width: 100%;" href="{{route('packages.index')}}">Back</a>
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

<form action="{{route('packages.store')}}" method="post" id="my-form">
    @csrf
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
            <h6 class="card-title">Create Packages</h6>
            <p class="text-muted mb-3"></p> 
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="" class="form-label">Package name</label>
                    <input type="text" class="form-control" placeholder="package name" autocomplete="off" name="package_name" id="package_name">
                    <span class="text-danger" id="package_name_error"></span>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Package Description</label>
                    <textarea type="text" required name="description" class="form-control" id="description" autocomplete="off" placeholder="Ex: Description"></textarea>
                    <span class="text-danger" id="description_error"></span>
                </div>


                <p class="text-muted mb-3">Package items</p>

                @foreach ($facilities as $facility)
                    <div class="mb-5 col-md-6">
                        <div style="padding: 8px; border: 1px solid rgba(0, 0, 0, 0.146); border-radius: 5px;">
                            <div class="form-check mb-4">
                                <input type="checkbox" name="facility[]" data-name="{{$facility->name}}" value="{{$facility->id}}" class="form-check-input facility" id="checkDefault">
                                <label class="form-check-label" for="checkDefault">
                                  {{$facility->name}}
                                </label>
                                <span class="text-danger facility_error"></span>
                            </div>
                            <div class="mb-4">
                                <div>
                                    <input type="text" autocomplete="off" name="{{$facility->id}}" class="form-control amount" id="checkDefault" placeholder="Amount">
                                    <span class="text-danger amount_error"></span>
                                </div>
                            </div>
                            <div>
                                <label for="" class="form-label">Main or extra item</label>
                                <select name="extra_item_{{$facility->id}}" id="" class="form-select extras">
                                    <option value="2" class="text-muted">Choose if main item or extra</option>
                                    <option value="1">Main</option>
                                    <option value="0">Extra</option>
                                </select>
                                <span class="text-danger extras_error"></span>
                            </div>
                        </div>
                       
                    </div>
                @endforeach

                <div class="col-md-12 mb-2 text-center">
                    <button type="submit" id="submit-btn" class="btn btn-success">Save package</button>
                </div>
            </div>
        </div>
      </div>
    </div>
</div>

<div class="modal fade" id="packageDetailsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Package Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <p>Package Name: <span id="package_name_modal"></span></p>
                <p>Package Description: <span id="package_description_modal"></span></p>


                <p>Package Items</p>
                <div id="package_details_modal">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="save-btn" class="btn btn-primary">Save changes</button>
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
            $('#submit-btn').on('click', (e) => {
                e.preventDefault();

                if (!$('#package_name').val()) {
                    $('#package_name_error').text('field required');
                    $('#package_name').focus();
                    return;
                } else {
                    $('#package_name_error').text('');
                }

                if (!$('#description').val()) {
                    $('#description_error').text('field required');
                    $('#description').focus();
                    return;
                } else {
                    $('#description_error').text('');
                }

                let isEmpty = false;

                $('.facility').each((i, element) => {
                    let isChecked = $(element).is(":checked");
                    if (isChecked) {
                        let amountInput = $(element).parent().parent().find('.amount');
                        let amountInputError = $(element).parent().parent().find('.amount_error');
                        let extrasInput = $(element).parent().parent().find('.extras');
                        let extrasInputError = $(element).parent().parent().find('.extras_error');
                        if (!$(amountInput).val()) {
                            $(amountInput).focus()
                            $(amountInputError).text('field required');
                            isEmpty = true;
                            return;
                        } else {

                            $(amountInputError).text('');
                        }

                        if ($(extrasInput).find(':selected').val() == 2) {
                            $(extrasInput).focus()
                            $(extrasInputError).text('field required');
                            isEmpty = true;
                            return;
                        } else {
                            $(extrasInputError).text('');
                        }
                    }
                });

                $('#package_details_modal').html('<div></div>');

                $('.facility').each((i, element) => {
                    let isChecked = $(element).is(":checked");
                    console.log(isChecked);
                    if (isChecked) {
                        let facilityName = $(element).data('name');
                        let facilityAmount = $(element).parent().parent().find('.amount').val();
                        let extraOrMain = $(element).parent().parent().find('.extras').find(':selected').text();
                        let template = `
                            <div style="display: flex; gap: 20px;">
                                <p>${facilityName}</p>
                                <p>Ksh ${facilityAmount}</p>
                                <p>${extraOrMain}</p>
                            </div>
                        `;

                        $('#package_details_modal').append(template);
                    }
                });

                if (isEmpty == false) {
                    $('#packageDetailsModal').modal('toggle');
                    $('#save-btn').on('click', (ev) => {
                        ev.preventDefault();
                        $('#my-form').submit();
                    })
                }
            })
        });
    </script>
@endpush