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
                <td>
                    <a href="#" class="ml-2" data-bs-toggle="modal" data-bs-target="#edit-{{$a->id}}">Edit</a>
                    {{-- <a href="#">Delete</a> --}}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>