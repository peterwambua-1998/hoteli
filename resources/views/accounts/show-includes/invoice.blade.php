<div class="mb-3" style="display: flex; flex-direction:row-reverse;">
    <a href="{{route('invoice.create', $account->id)}}"><button class="btn btn-outline-success">Add</button></a>
</div>
<div class="table-responsive">
    <table class="table table-bordered table-striped" id="dataTableExample3">
        <thead>
            <tr>
                <th>#</th>
                <th>Inv No</th>
                <th>Delivery date</th>
                <th>Tax date</th>
                <th>Subtotal</th>
                <th>Vat</th>
                <th>Total</th>
                <th>D/Note</th>
                <th>C/Note</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $number = 1; ?>
            @foreach ($invoice as $pr)
            <?php
                $creditNote = App\Models\CreditNote::where('invoice_id', '=', $pr->id)->get();
                $debitNote = App\Models\DebitNote::where('invoice_id', '=', $pr->id)->get();
            ?>
            <tr>
                <td>{{$number}}<?php $number++; ?></td>
                <td>{{$pr->inv_number}}</td>
                <td>{{$pr->delivery_date}}</td>
                <td>{{$pr->tax_date}}</td>
                <td>{{number_format($pr->sub_total, 2)}}</td>
                <td>{{number_format($pr->tax_amount, 2)}}</td>
                <td>{{number_format($pr->total, 2)}}</td>
                <td><input type="checkbox" class="form-check-input" id="checkInlineCheckedDisabled" disabled=""  @if(count($pr->creditN) > 0) checked="" @endif></td>
                <td><input type="checkbox" class="form-check-input" id="checkInlineCheckedDisabled" disabled="" @if(count($pr->debitN) > 0) checked="" @endif></td>
                <td style="display: flex; gap: 20px;">
                    <a style="color:green" href="{{route('invoice.show', $pr->id)}}">Show</a>
                    {{-- <a style="color:blue" href="#" data-bs-toggle="modal" data-bs-target="#receipt-{{$pr->id}}">Receipt</a> --}}
                    
                    {{-- <a href="#" data-bs-toggle="modal" data-bs-target="#withholding-{{$pr->id}}">withholding</a> --}}
                    {{-- <a href="#">Download</a> --}}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- @foreach ($invoice as $inv)
<div class="modal fade" id="exampleModal-{{$inv->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Invoice {{$inv->inv_number}} items</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTableExample">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item code</th>
                            <th>Item description</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Days</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $num = 1; ?>
                        @foreach ($inv->items as $item)
                            <td>{{$num}}<?php $num ++ ?></td>
                            <td>{{$item->item_code}}</td>
                            <td>{{$item->item_description}}</td>
                            <td>{{$item->quantity}}</td>
                            <td>{{$item->rate}}</td>
                            <td>{{$item->days}}</td>
                            <td>{{$item->amount}}</td>
                            <td>
                                <a href="{{route('credit-note.create', $item->id)}}">Credit Note</a>
                            </td>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div> 
@endforeach --}}