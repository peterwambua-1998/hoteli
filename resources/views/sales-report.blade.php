@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-xl-12 grid-margin stretch-card">
        <div class="card mt-5">
            <div class="card-body">
                <form action="{{route('cashflows.query')}}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="fromDate">From</label>
                            <input type="date" class="form-control" id="from" name="from">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="toDate">To</label>
                            <input type="date" class="form-control" id="to" name="to">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">SALES</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableExample">
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Bar</td>
                                <td>{{$barTotal}}</td>
                            </tr>
                            <tr>
                                <td>Kitchen</td>
                                <td>{{$restTotal}}</td>
                            </tr>
                            <tr>
                                <th>Total Sales</th>
                                <th>{{$barKitchenTotal}}</th>
                            </tr>
                        </tbody>
                    </table>

                    <table class="table table-bordered table-striped" id="dataTableExample">
                        <thead>
                            <tr>
                                <th>Payment</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($accounts as $account)
                                <tr>
                                    <td>{{$account->bank_name}}</td>
                                    <td>{{$account->cashFlow}}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <th>Total Cash Flows</th>
                                <th>{{$cashFlowTotal}}</th>
                            </tr>
                            <tr>
                                <th>Balance C/F</td>
                                <th>{{$balance}}</th>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/chartjs/chart.umd.js') }}"></script>
@endpush

@push('custom-scripts')
  <script>
    
  </script>
@endpush



