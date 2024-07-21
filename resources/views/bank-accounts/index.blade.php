@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('bank-account.index')}}">Bank Account</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    <div>
        <a class="btn btn-primary" style="width: 100%;" href="#" data-bs-toggle="modal" data-bs-target="#exampleModal"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px;" name="add-circle-outline"></ion-icon> Add Bank Account</a>
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
            <h6 class="card-title">Bank Accounts Table</h6>
            <p class="text-muted mb-3"></p> 
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTableExample">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Bank</th>
                            <th>Account Name</th>
                            <th>Account Number</th>
                            <th>Branch</th>
                            <th>Available Balance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $number = 1; ?>
                        @foreach ($bankAccounts as $a)
                        <tr>
                            <td>{{$number}}<?php $number++; ?></td>
                            <td>{{$a->bank_name}}</td>
                            <td>{{$a->account_name}}</td>
                            <td>{{$a->account_number}}</td>
                            <td>{{$a->branch}}</td>
                            <td>{{number_format($a->total_amount, 2)}}</td>
                            <td style="display: flex; gap: 20px;">
                                <a href="{{route('bank.statement.view', $a->id)}}" class="ml-2" style="color: green">statement</a>
                                <a href="#" class="ml-2" style="color: blue;" data-bs-toggle="modal" data-bs-target="#edit-{{$a->id}}">edit</a>
                                {{-- <a href="#">Delete</a> --}}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            

            <div id="m-pie-chart"></div>
        </div>
      </div>
    </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('bank-account.store')}}" method="post">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Bank Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bank_name" class="form-label">Bank Name</label>
                        <input type="text" required name="bank_name" class="form-control" id="bank_name" autocomplete="off" placeholder="Ex: Bank Name">
                    </div>

                    <div class="mb-3">
                        <label for="account_name" class="form-label">Account Name</label>
                        <input type="text" required name="account_name" class="form-control" id="bank_name" autocomplete="off" placeholder="Ex: Account Name">
                    </div>

                    <div class="mb-3">
                        <label for="bank_name" class="form-label">Account Number</label>
                        <input type="text" required name="account_number" class="form-control" id="bank_name" autocomplete="off" placeholder="Ex: Account Number">
                    </div>

                    <div class="mb-3">
                        <label for="branch" class="form-label">Branch</label>
                        <input type="text" required name="branch" class="form-control" id="branch" autocomplete="off" placeholder="Ex: Branch">
                    </div>

                    <div class="mb-3">
                        <label for="available_balance" class="form-label">Available balance</label>
                        <input type="text" required name="available_balance" class="form-control" id="available_balance" autocomplete="off" placeholder="Ex: 100000">
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

@foreach ($bankAccounts as $a)
<div class="modal fade" id="edit-{{$a->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('bank-account.update', $a->id)}}" method="post">
        @csrf
        @method('PATCH')
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Room Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bank_name" class="form-label">Bank Name</label>
                        <input type="text" required value="{{$a->bank_name}}" name="bank_name" class="form-control" id="bank_name" autocomplete="off" placeholder="Ex: Bank Name">
                    </div>

                    <div class="mb-3">
                        <label for="account_name" class="form-label">Account Name</label>
                        <input type="text" required value="{{$a->account_name}}" name="account_name" class="form-control" id="bank_name" autocomplete="off" placeholder="Ex: Account Name">
                    </div>

                    <div class="mb-3">
                        <label for="bank_name" class="form-label">Account Number</label>
                        <input type="text" required value="{{$a->account_number}}" name="account_number" class="form-control" id="bank_name" autocomplete="off" placeholder="Ex: Account Number">
                    </div>

                    <div class="mb-3">
                        <label for="branch" class="form-label">Branch</label>
                        <input type="text" value="{{$a->branch}}" required name="branch" class="form-control" id="branch" autocomplete="off" placeholder="Ex: Branch">
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
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script defer>
        $(document).ready( function () {
            $('#dataTableExample').DataTable({
                language: { searchPlaceholder: "Search records", search: "",},
            });
        } );



        $.ajax({
            type: "GET",
            url: "/bank-accounts-analytics",
            processData: false,
            contentType: false,
            cache: false,
            error: function(data){
                console.log(data);
            },
            success: function (response) {
                google.charts.load("current", {packages:["corechart"]});
                google.charts.setOnLoadCallback(drawChart);
                let dataTwo = [['Task', 'Account balances']];
                for (let i = 0; i < response.length; i++) {
                    let element = response[i];
                    dataTwo.push([element.bank_name, element.amount])
                }
                function drawChart() {
                    var data = google.visualization.arrayToDataTable(
                        dataTwo
                    );

                    var options = {
                        
                        title: 'Account balances',
                        is3D: true,
                    };

                    var chart = new google.visualization.PieChart(document.getElementById('m-pie-chart'));
                    chart.draw(data, options);
                }

                $('#m-pie-chart').css({'width':'900px', 'height': '500px'});
            }
        });

       

        
        
    </script>
@endpush