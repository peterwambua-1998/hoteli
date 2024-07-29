<div class="table-responsive">
    <table class="table table-bordered table-striped" id="dataTableExample7">
        <thead>
            <tr>
                <th>#</th>
                <th>Receipt No</th>
                <th>description</th>
                <th>Paid Amount</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $number = 1; ?>
            @foreach ($cashAccount as $r)
            <tr>
                <td>{{$number}}<?php $number++; ?></td>
                <td>{{$r->receipt_number}}</td>
                <td>{{$r->invoice->description}}</td>
                <td>
                    {{$r->payment_code}}
                </td>
                <td>{{number_format($r->amount, 2)}}</td>
                <td style="display: flex; gap: 20px;">
                   {{-- <a href="{{route('receipt.show', $r->id)}}">show</a> --}}
                   <a href="#" style="color: green">Show</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>