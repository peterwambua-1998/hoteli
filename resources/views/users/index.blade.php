@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('users.index')}}">System users</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    <div>
        <a class="btn btn-primary" style="width: 100%;" href="#" data-bs-toggle="modal" data-bs-target="#exampleModal"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px;" name="add-circle-outline"></ion-icon> Add System User</a>
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
            <h6 class="card-title">System Users Table</h6>
            <p class="text-muted mb-3"></p> 
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTableExample">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $number = 1; ?>
                        @foreach ($users as $user)
                        <tr>
                            <td>{{$number}}<?php $number++; ?></td>
                            <td>{{$user->name}}</td>
                            <td>
                                @if ($user->role == 1)
                                    Admin
                                @endif

                                @if ($user->role == 2)
                                    Receptionist
                                @endif

                                @if ($user->role == 3)
                                    Cashier Drinks
                                @endif

                                @if ($user->role == 4)
                                    Cashier Food
                                @endif
                            </td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->phone_num}}</td>
                            <td>
                                <a href="#" class="ml-2" data-bs-toggle="modal" data-bs-target="#edit-{{$user->id}}">Edit</a>
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
    <form action="{{route('users.store')}}" method="post">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add System USer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" required name="name" class="form-control" id="name" autocomplete="off" placeholder="Ex: John Doe">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" required name="email" class="form-control" id="email" autocomplete="off" placeholder="Ex: john@mail.com">
                    </div>

                    <div class="mb-3">
                        <label for="phone_num" class="form-label">Phone</label>
                        <input type="text" required name="phone_num" class="form-control" id="phone_num" autocomplete="off" placeholder="Ex: 07XX XXX XXX">
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">User Role</label>
                        <select class="form-select mb-3" name="role">
                            <option selected="0">Choose User Role</option>
                            <option value="1">Administrator</option>
                            <option value="2">Receptionist</option>
                            <option value="3">Kitchen Cashier</option>
                            <option value="4">Bar Cashier</option>
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

@foreach ($users as $user)
<div class="modal fade" id="edit-{{$user->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('users.update', $user->id)}}" method="post">
        @csrf
        @method('PATCH')
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit System User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" value="{{$user->name}}" required name="name" class="form-control" id="name" autocomplete="off" placeholder="Ex: John Doe">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" value="{{$user->email}}" required name="email" class="form-control" id="email" autocomplete="off" placeholder="Ex: john@mail.com">
                    </div>

                    <div class="mb-3">
                        <label for="phone_num" class="form-label">Phone</label>
                        <input type="text" required value="{{$user->phone_num}}" name="phone_num" class="form-control" id="phone_num" autocomplete="off" placeholder="Ex: 07XX XXX XXX">
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">User Role</label>
                        <select class="form-select mb-3" name="role">
                            <option selected="0">Choose User Role</option>
                            <option @if($user->role == 1) selected @endif  value="1">Administrator</option>
                            <option @if($user->role == 2) selected @endif value="2">Receptionist</option>
                            <option @if($user->role == 3) selected @endif value="3">Kitchen Cashier</option>
                            <option @if($user->role == 4) selected @endif value="4">Bar Cashier</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
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