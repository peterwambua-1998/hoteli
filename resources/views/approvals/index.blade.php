@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('account.approval.index')}}">Customer Account Approval</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    
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
            <h6 class="card-title">Customer Account Approval List</h6>
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
                        @foreach ($accountApprovals as $account)
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
                            <td style="display: flex; gap: 20px;">
                                <a href="#" style="color: green" class="ml-2" data-bs-toggle="modal" data-bs-target="#edit-{{$account->id}}">Approve</a>
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

@foreach ($accountApprovals as $account)
<div class="modal fade" id="edit-{{$account->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('account.approval.update')}}" method="post">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Approve Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                   <h6>Approve Account: {{$account->name}}</h6>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="approval_id" value="{{$account->id}}">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button type="submit" class="btn btn-success">Yes</button>
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