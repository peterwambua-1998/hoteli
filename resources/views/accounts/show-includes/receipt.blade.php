<div class="table-responsive">
    <table class="table table-bordered table-striped" id="dataTableExample7">
        <thead>
            <tr>
                <th>#</th>
                <th>Receipt No</th>
                <th>Payment method</th>
                <th>Ref number</th>
                <th>Amount</th>
                <th>Paid Amount</th>
                <th>Balance</th>
                <th>Refund amount</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $number = 1; ?>
            @foreach ($receipts as $r)
            <tr>
                <td>{{$number}}<?php $number++; ?></td>
                <td>{{$r->receipt_number}}</td>
                <td>
                    @if ($r->payment_method == 1)
                        Cash
                    @endif

                    @if ($r->payment_method == 2)
                        Mpesa
                    @endif

                    @if ($r->payment_method == 3)
                        Bank Transfer
                    @endif

                    @if ($r->payment_method == 4)
                        Cheque
                    @endif

                    @if ($r->payment_method == 5)
                        Package
                    @endif

                    @if ($r->payment_method == 6)
                        Complimentary
                    @endif
                </td>
                <td>
                    {{$r->payment_code}}
                </td>
                <td>{{number_format($r->amount, 2)}}</td>
                <td>{{number_format($r->paid_amount, 2)}}</td>
                <td>{{number_format($r->balance, 2)}}</td>
                <td>{{number_format($r->refund_amount, 2)}}</td>
                <td style="display: flex; gap: 20px;">
                   <a href="{{route('receipt.show', $r->id)}}">show</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>