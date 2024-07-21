@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('goods.receive.index')}}">Goods Receive Note</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    <a href="{{route('goods.receive.index')}}"><button class="btn btn-warning">Back</button></a>
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

<form action="{{route('goods.receive.generate.store')}}" method="post">
@csrf

<input type="hidden" name="grn_number" id="po_number" />

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
            {{-- invoice details --}}
            <div class="mb-2">
                <h4 class="mb-4">Goods Receive Note Details</h4>
                <div style="display: flex; justify-content:space-between">
                    <p class="mb-3" style="color: #808080">GRN No: <span id="inv_no">{{rand(0, 1000000)}}</span></p>
                </div>
                <div class="row">
                    <div class="mb-3 col-md-4">
                        <label for="exampleInputUsername1" class="form-label">Date Received</label>
                        <input required type="datetime-local" class="form-control" name="date_issue" id="exampleInputUsername1" autocomplete="off">
                    </div>

                    <div class="mb-3 col-md-4">
                        <label class="form-label">Supplier</label>
                        <select class="form-select mb-3" name="supplier_id">
                          <option selected value="0">Choose supplier...</option>
                          @foreach ($suppliers as $s)
                            <option value="{{$s->id}}">{{$s->name}}</option>
                          @endforeach
                        </select>
                    </div>

                    <div class="mb-3 col-md-4">
                        <label for="send_to" class="form-label">Select Store</label>
                        <select name="send_to" class="form-select">
                            <option value="1" seleceted>Main store</option>
                            <option value="2">kitchen store</option>
                            <option value="3">bar store</option>
                            <option value="4">Maintenance store</option>
                        </select>
                    </div>
                </div>
            </div>
            {{-- invoice items --}}
            <div>
                {{-- item --}}
                <table class="table table-bordered" id="dataTableExample">
                    <thead style="background: rgb(6, 181, 0); color:black;">
                        <tr>
                            <th style="width: 10%; color:black">Code</th>
                            <th style="width: 40%; color:black">Item Description</th>
                            <th style="width: 15%; color:black">Rate</th>
                            <th style="width: 10%; color:black">Qty Received</th>
                            <th style="width: 20%; color:black">Amount</th>
                            <th style="color:black">Action</th>
                        </tr>
                    </thead>
                    <tbody id="table-row">
                        <tr>
                            <td>
                                <input type="text" required name="item_code[]" class="form-control item_code" autocomplete="off" readonly>
                                <input type="hidden" name="item_id[]" class="item_id" />
                            </td>
                            <td>
                                <div style="width: 100%;">
                                    <input type="text" required name="item_description[]" class="form-control item_description" autocomplete="off">
                                    <div class="outer-suggestion" style="position: relative; width: 100%; z-index: 100;">
                                        <div class="suggestion" style="position: absolute; background: #f9fafb; border-radius: 5px; color: black; border: 1px solid #94a3b8; width: 100%; padding: 8px;">
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <input type="text" name="price[]" class="form-control rate" autocomplete="off" readonly>
                            </td>
                            <td>
                                <input type="text" name="qty_received[]" class="form-control quantity" autocomplete="off">
                            </td>
                            
                            <td>
                                <input type="text" readonly name="amount[]" class="form-control amount" autocomplete="off">
                                <input type="hidden" class="taxable" />
                            </td>
                            <td>
                                
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-1" style="display: flex; flex-direction:row-reverse">
                    <div style="width: 40%; display: flex;">
                        <p style="width: 40%; font-size: 16px;">Sub Total</p>
                        <input type="text" readonly name="sub_total" class="form-control sub_total" autocomplete="off">
                    </div>
                </div>
                <div class="mt-1" style="display: flex; flex-direction:row-reverse">
                    <div style="width: 40%; display: flex;">
                        <p style="width: 40%; font-size: 16px;">Vat 16%</p>
                        <input type="text" readonly name="tax_amount" class="form-control vat" autocomplete="off">
                    </div>
                </div>
                <div class="mt-1" style="display: flex; flex-direction:row-reverse">
                    <div style="width: 40%; display: flex;">
                        <p style="width: 40%; font-size: 16px;">Total</p>
                        <input type="text" readonly name="total" class="form-control total" autocomplete="off">
                    </div>
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-success" id="add-row" style="width: 20%">
                        <i data-feather="plus-circle" ></i>
                    </button>

                    <button type="submit" class="btn btn-primary" id="save" style="width: 20%">
                        Save
                    </button>
                </div>
            </div>
        </div>
      </div>
    </div>
</div>
</form>

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush

@push('custom-scripts')
    <script defer>
        $(document).ready( function () {
            let po_number = $('#inv_no').text();
            $('#po_number').val(po_number);
            hideOuterSuggestionDiv();
            // add elements 
            $('#add-row').on('click', () => {
                let template = `
                    <tr>
                        <td>
                            <input type="text" name="item_code[]" class="form-control item_code" autocomplete="off" readonly>
                            <input type="hidden" name="item_id[]" class="item_id" />
                        </td>
                        <td>
                            <div style="width: 100%;">
                                <input type="text" required name="item_description[]" class="form-control item_description" autocomplete="off">
                                <div class="outer-suggestion" style="position: relative; width: 100%; z-index: 100;">
                                    <div class="suggestion" style="position: absolute; background: #f9fafb; border-radius: 5px; color: black; border: 1px solid #94a3b8; width: 100%; padding: 8px;">
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input type="text" name="price[]" class="form-control rate" autocomplete="off" readonly>
                        </td>
                        <td>
                            <input type="text" name="qty_received[]" class="form-control quantity" autocomplete="off">
                        </td>
                        <td>
                            <input type="text" readonly name="amount[]" class="form-control amount" autocomplete="off">
                            <input type="hidden" class="taxable" />
                        </td>
                        <td>
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="red" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="minus-row"><circle cx="12" cy="12" r="10"></circle><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                        </td>
                    </tr>
                `;

                $('#table-row').append(template);
                inputSuggestions();
                hideOuterSuggestionDiv();
                minus();
                calculateTotal();
            })


            function minus() {
                $('.minus-row').each((index, el) => {
                    $(el).on('click', () => {
                        let parent = $(el).parent().parent().remove();
                        // recalculate totals
                        calFullTotal();
                    })
                })
            }

            minus();


            function calculateTotal() {
                $('.quantity').each((index, el) => {
                    $(el).on('input', (e) => {
                        let parent = $(el).parent().parent();
                        let qty = Number(parent.find('.quantity').val());
                        let rate = Number(parent.find('.rate').val());
                        let amount_input = parent.find('.amount');
                        let amount = qty * rate;
                        amount_input.val(amount);
                        calFullTotal();
                    });
                });
            }

            calculateTotal();

            function calFullTotal() {
                let sub_total = 0;
                let sub_total_non_taxable = 0
                
                let total = 0;
                let total_taxable = 0;
                let vat = 0;
                $('.amount').each((index, el) => {
                    let parent = $(el).parent();
                    let isTaxable = parent.find('.taxable').val(); 
                    if (isTaxable == 1) {
                        sub_total += Number($(el).val());
                        total += Number($(el).val());

                    } else {
                        sub_total_non_taxable += Number($(el).val());
                        total_taxable += Number($(el).val());

                    }
                });

                sub_total = Number(sub_total / 1.16).toFixed(2);
                console.log(sub_total, sub_total_non_taxable);
                let sub_total_display = Number(sub_total) + Number(sub_total_non_taxable);
                $('.sub_total').val(Number(sub_total_display).toFixed(2));
                vat = Number(total - Number(sub_total)).toFixed(2);
                $('.vat').val(vat);
                let t = Number(parseFloat(sub_total) + parseFloat(vat) + parseFloat(sub_total_non_taxable)).toFixed(2);
                $('.total').val(t);
            }

            function hideOuterSuggestionDiv() {
                $('.outer-suggestion').each((index, element) => {
                    $(element).hide();
                });
            }
           

            function inputSuggestions(params) {
                $('.item_description').each((index, element) => {
                    $(element).on('input', () => {
                        let parent = $(element).parent();
                        let suggestion_div = parent.find('.suggestion');
                        let outer_suggestion = parent.find('.outer-suggestion');
                        let data = new FormData;
                        data.append('_token', '{{csrf_token()}}');
                        data.append('input_query', $(element).val());
                        $.ajax({
                            type: 'POST',
                            url: "{{route('query.items')}}",
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: data,
                            error: (err) => {
                                console.log(err);
                            },
                            success: (response) => {
                                console.log(response);
                                if (response.length == 0) {
                                    outer_suggestion.hide();
                                }

                                let template = '';
                                for (let i = 0; i < response.length; i++) {
                                    let element = response[i];
                                    template += `
                                        <p class="mb-2 suggestion-content" data-id="${element.id}" data-code="${element.code}" data-price="${element.buying_price}" data-description="${element.description}" data-taxable=${element.taxable}>${element.description}</p>
                                    `;
                                }
                                suggestion_div.html(template);
                                outer_suggestion.show();
                                clickSuggestion();
                                if (response.length == 0) {
                                    outer_suggestion.hide();
                                }
                            }
                        })
                    });
                })
            }

            function clickSuggestion() {
                $('.suggestion-content').each((index, element) => {
                    $(element).on('click', () => {
                        let parent = $(element).parent().parent().hide();
                        let parentDiv = $(element).parent().parent().parent();
                        let item_id = $(element).data('id');
                        let item_code = $(element).data('code');
                        let item_description = $(element).data('description');
                        let item_price = $(element).data('price');
                        let item_taxable = $(element).data('taxable');
                        let input_description = parentDiv.find('.item_description').val(item_description);

                        let tr = $(element).parent().parent().parent().parent().parent();
                        let input_code  = tr.find('.item_code').val(item_code);
                        let input_price  = tr.find('.rate').val(item_price);
                        let input_item_id = tr.find('.item_id').val(item_id);
                        let input_taxable = tr.find('.taxable').val(item_taxable);
                    });
                })
            }

            inputSuggestions();
            
        });
    </script>
@endpush