@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('accounts.index')}}">Accounts</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    <div>
        <a class="btn btn-primary" style="width: 100%;" href="#" data-bs-toggle="modal" data-bs-target="#exampleModal"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px;" name="add-circle-outline"></ion-icon> Add Account</a>
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
            <h6 class="card-title">Accounts Table</h6>
            <p class="text-muted mb-3"></p> 
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTableExample">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Organization</th>
                            <th>Type</th>
                            <th>Email</th>
                            <th>Telephone</th>
                            <th>Location</th>
                            {{-- <th>Members</th> --}}
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $number = 1; ?>
                        @foreach ($accounts as $account)
                        <tr>
                            <td>{{$number}}<?php $number++; ?></td>
                            <td>{{$account->name}}</td>
                            <td>
                                @if ($account->type == 1)
                                Corporate
                                @endif

                                @if ($account->type == 2)
                                Individual
                                @endif
                            </td>
                            <td>{{$account->email}}</td>
                            <td>{{$account->telephone}}</td>
                            <td>{{$account->location}}</td>
                            {{-- <td>{{$account->members->count()}}</td> --}}
                            <td style="display: flex; gap: 20px;">
                                <a style="color: green" href="{{route('accounts.show', $account->id)}}">show</a>
                                <a href="#" style="color: blue" class="ml-2" data-bs-toggle="modal" data-bs-target="#edit-{{$account->id}}">Edit</a>
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
    <form action="{{route('accounts.store')}}" method="post">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="type" class="form-label">Name/Organization</label>
                        <input type="text" required name="name" class="form-control" id="type" autocomplete="off" placeholder="Ex: Name/Organization">
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select mb-3" name="type">
                            <option selected value="0">Choose Account Type</option>
                            <option value="1">Corporate</option>
                            <option value="2">Individual</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="vat_registration_number" class="form-label">Vat registration number</label>
                        <input type="text" required name="vat_registration_number" class="form-control" id="email" autocomplete="off" placeholder="Ex: j0000ff1">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" required name="email" class="form-control" id="email" autocomplete="off" placeholder="Ex: johndoe@mail.com">
                    </div>

                    <div class="mb-3">
                        <label for="telephone" class="form-label">Telephone</label>
                        <input type="text" required name="telephone" class="form-control" id="telephone" autocomplete="off" placeholder="Ex: 07XX XXX XXX">
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" required name="location" class="form-control" id="location" autocomplete="off" placeholder="Ex: Nairobi, Kenya">
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

@foreach ($accounts as $account)
<div class="modal fade" id="edit-{{$account->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('accounts.update', $account->id)}}" method="post">
        @csrf
        @method('PATCH')
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="type" class="form-label">Name/Organization</label>
                        <input type="text" value="{{$account->name}}" required name="name" class="form-control" id="type" autocomplete="off" placeholder="Ex: Name/Organization">
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select mb-3" name="type">
                            <option selected="0">Choose Account Type</option>
                            <option @if($account->type == 1) selected @endif value="1">Corporate</option>
                            <option @if($account->type == 2) selected @endif value="2">Individual</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="vat_registration_number" class="form-label">Vat registration number</label>
                        <input type="text" value="{{$account->vat_registration_number}}" required name="vat_registration_number" class="form-control" id="email" autocomplete="off" placeholder="Ex: j0000ff1">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" value="{{$account->email}}" required name="email" class="form-control" id="email" autocomplete="off" placeholder="Ex: johndoe@mail.com">
                    </div>

                    <div class="mb-3">
                        <label for="telephone" class="form-label">Telephone</label>
                        <input type="text" value="{{$account->telephone}}" required name="telephone" class="form-control" id="telephone" autocomplete="off" placeholder="Ex: 07XX XXX XXX">
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" value="{{$account->location}}" required name="location" class="form-control" id="location" autocomplete="off" placeholder="Ex: Nairobi, Kenya">
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

@foreach ($accounts as $account)
<div class="modal fade" id="add-users-{{$account->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('account-users.store')}}" method="post">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Account Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">

                    <input type="hidden" name="account_id" value="{{$account->id}}">
                    <div class="mb-3">
                        <label for="type" class="form-label">Name</label>
                        <input type="text" required name="name" class="form-control" id="type" autocomplete="off" placeholder="Ex: Name/Organization">
                    </div>


                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" required name="email" class="form-control" id="email" autocomplete="off" placeholder="Ex: johndoe@mail.com">
                    </div>

                    <div class="mb-3">
                        <label for="telephone" class="form-label">Telephone</label>
                        <input type="text" required name="telephone" class="form-control" id="telephone" autocomplete="off" placeholder="Ex: 07XX XXX XXX">
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
        $(document).ready( function () {
            $('#dataTableExample').DataTable({
                language: { searchPlaceholder: "Search records", search: "",},
            });
        } );

    </script>
@endpush