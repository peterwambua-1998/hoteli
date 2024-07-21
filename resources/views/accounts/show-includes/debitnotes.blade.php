
<div class="table-responsive">
    <table class="table table-bordered table-striped" id="dataTableExample2">
        <thead>
            <tr>
                <th>#</th>
                <th>Invoice</th>
                <th>Note Number</th>
                <th>Subtotal</th>
                <th>Vat</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $num = 1; ?>
            @foreach ($allDebitNotes as $item)
            <tr>
                <td>{{$num}}<?php $num++ ?></td>
                <td>{{$item->invoice->inv_number}}</td>
                <td>{{$item->note_number}}</td>
                <td>{{number_format($item->sub_total, 2)}}</td>
                <td>{{number_format($item->tax_amount, 2)}}</td>
                <td>{{number_format($item->total, 2)}}</td>
                <td><a style="color: green" href="{{route('debit-note.show', $item->id)}}">show</a></td>
            </tr>
            @endforeach
            
        </tbody>
    </table>
    
</div>