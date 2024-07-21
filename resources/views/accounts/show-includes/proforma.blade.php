<div class="mb-3" style="display: flex; flex-direction:row-reverse;">
    <a href="{{route('proforma.create', $account->id)}}"><button class="btn btn-outline-success">Add</button></a>
</div>
<div class="table-responsive">
    <table class="table table-bordered table-striped" id="dataTableExample5">
        <thead>
            <tr>
                <th>#</th>
                <th>Inv No</th>
                <th>Delivery date</th>
                <th>Tax date</th>
                <th>SubTotal</th>
                <th>Vat</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $number = 1; ?>
            @foreach ($proforma as $pr)
            <tr>
                <td>{{$number}}<?php $number++; ?></td>
                <td>{{$pr->inv_number}}</td>
                <td>{{$pr->delivery_date}}</td>
                <td>{{$pr->tax_date}}</td>
                <td>{{number_format($pr->sub_total, 2)}}</td>
                <td>{{number_format($pr->tax_amount, 2)}}</td>
                <td>{{number_format($pr->total, 2)}}</td>
                <td style="display: flex; gap: 20px;">
                    <a style="color: green" href="/proforma/show/{{$pr->id}}">show</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>