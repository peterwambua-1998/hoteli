@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('refund-request.index')}}">Refund Request</a></li>
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
            <h6 class="card-title">Refund Requests Table</h6>
            <p class="text-muted mb-3"></p> 
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTableExample">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Receipt Number</th>
                            <th>Receipt Total</th>
                            <th>Refund Amount</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $number = 1; ?>
                        @foreach ($refundRequests as $item)
                        <tr>
                            <td>{{$number}}<?php $number++; ?></td>
                            <td>{{$item->receipt->receipt_number}}</td>
                            <td>{{number_format($item->receipt->amount, 2)}}</td>
                            <td>{{number_format($item->amount, 2)}}</td>
                            <td>
                                @if ($item->approved == 0)
                                    <span class="badge bg-danger">pending</span>
                                @endif

                                @if ($item->approved == 1)
                                <span class="badge bg-success">approved</span>
                                @endif

                                @if ($item->approved == 2)
                                <span class="badge bg-warning">rejected</span>
                                @endif
                            </td>
                            <td>
                                @if ($item->approved == 0)
                                    n/a
                                @endif

                                @if ($item->approved == 1)
                                    n/a
                                @endif

                                @if ($item->approved == 2)
                                    {{$item->reason}}
                                @endif
                            </td>
                            <td>
                                <a href="#" style="color: green" class="ml-2" data-bs-toggle="modal" data-bs-target="#approve-{{$item->id}}">Approve</a>
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



@foreach ($refundRequests as $type)
<div class="modal fade" id="approve-{{$type->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{route('refund-request.approve')}}" method="post">
        @csrf
        <input type="hidden" name="refund_request_id" value="{{$type->id}}">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Approve Refund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="type" class="form-label">Approve</label>
                        <select class="form-select mb-3 approval_status" name="approval_status">
                            <option selected value="1">Approve</option>
                            <option value="2">Reject</option>
                        </select>
                    </div>

                    <div class="mb-3 reason-div">
                        <label for="reason" class="form-label">Rejection Reason</label>
                        <textarea class="form-control" id="reason" name="reason" rows="5" spellcheck="false"></textarea>
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
            $('.reason-div').each((index, element) => {
                $(element).hide();
            });

            $('#reason-div').hide();

            $('#dataTableExample').DataTable({
                language: { searchPlaceholder: "Search records", search: "",},
            });

            $('.approval_status').each((index, element) => {
                $(element).on('change', () => {
                    let parent = $(element).parent().parent();
                    let reason_div = parent.find('.reason-div');
                    if (reason_div.val() == 0) {
                        reason_div.show();
                    } 
                });
            })
            
        } );

    </script>
@endpush