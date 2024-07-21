@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('accounts.index')}}">Quotation</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    <div>
        <a href="{{route('accounts.show', $account->id)}}"><button class="btn btn-warning">Back</button></a>
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

<form action="{{route('quotation.store')}}" method="post">
@csrf

<input type="hidden" name="account_id" value="{{$account->id}}"/>
<input type="hidden" name="inv_number" id="inv_number"/>
<input type="hidden" name="invoiced_to" value="{{$account->name}}"/>
<input type="hidden" name="vat_registration_number" value="{{$account->vat_registration_number}}"/>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
            {{-- invoice details --}}
            <div class="mb-2">
                <h4 class="mb-4">Quotation Details</h4>
                <div style="display: flex; justify-content:space-between">
                    <div class="mb-2">
                        <p class="mb-1" style="color: #808080">Quoted To: <span>{{$account->name}}</span></p>
                        <p style="color: #808080">Company VAT Reg: <span></span></p>
                    </div>
                    
                    <p class="mb-3" style="color: #808080">Quotation No: <span id="inv_no">{{rand(0, 1000000)}}</span></p>
                </div>
                <div class="row">
                    <div class="mb-3 col-md-3">
                        <label for="delivery_date" class="form-label">Delivery Date</label>
                        <input required type="date" class="form-control" name="delivery_date" id="delivery_date" autocomplete="off">
                    </div>

                    <div class="mb-3 col-md-3">
                        <label for="to_date" class="form-label">To</label>
                        <input required type="date" class="form-control" name="to_date" id="to_date" autocomplete="off">
                    </div>

                    <div class="mb-3 col-md-3">
                        <label for="from_date" class="form-label">From</label>
                        <input required type="date" class="form-control" name="from_date" id="from_date" autocomplete="off">
                    </div>

                    {{-- <div class="mb-3 col-md-3">
                        <label for="tax_date" class="form-label">Tax Date</label>
                        <input required type="date" name="tax_date" class="form-control" id="tax_date" autocomplete="off" >
                    </div> --}}

                    <div class="mb-3 col-md-3">
                        <label for="quotation_validity" class="form-label">Validity (Days)</label>
                        <input required type="text" name="quotation_validity" class="form-control" id="quotation_validity" autocomplete="off" placeholder="Quotation validity">
                    </div>

                    {{-- <div class="mb-3 col-md-3">
                        <label class="form-label">Bank Account</label>
                        <select class="form-select mb-3" name="bank_account_id">
                          <option selected value="0">Choose bank account...</option>
                          @foreach ($bankAccounts as $a)
                            <option value="{{$a->id}}">{{$a->bank_name}}</option>
                          @endforeach
                        </select>
                    </div> --}}
                </div>
            </div>
            {{-- invoice items --}}
            <div>
                {{-- item --}}
                <table class="table table-bordered" id="dataTableExample">
                    <thead style="background: rgb(6, 181, 0); ">
                        <tr >
                            <th style="width: 10%; color:black">Code</th>
                            <th style="width: 40%; color:black">Item Description</th>
                            <th style="width: 15%; color:black">Rate</th>
                            <th style="width: 10%; color:black">Qty</th>
                            <th style="width: 10%; color:black">Days</th>
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
                                <input type="text" name="rate[]" class="form-control rate" autocomplete="off" readonly>
                            </td>
                            <td>
                                <input type="text" name="quantity[]" class="form-control quantity" autocomplete="off">
                            </td>
                            <td>
                                <input type="text" name="days[]" class="form-control days" autocomplete="off">
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
                        <p style="width: 40%; font-size: 16px;">Levy 2%</p>
                        <input type="text" readonly name="levy" class="form-control levy" autocomplete="off">
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
            let inv_no = $('#inv_no').text();
            $('#inv_number').val(inv_no);
            hideOuterSuggestionDiv();

            // add elements 
            $('#add-row').on('click', () => {
                let template = `
                    <tr>
                        <td>
                            <input type="text" name="item_code[]" class="form-control item_code" autocomplete="off">
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
                            <input type="text" name="rate[]" class="form-control rate" autocomplete="off">
                        </td>
                        <td>
                            <input type="text" name="quantity[]" class="form-control quantity" autocomplete="off">
                        </td>
                        <td>
                            <input type="text" name="days[]" class="form-control days" autocomplete="off">
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
                $('.days').each((index, el) => {
                    $(el).on('input', (e) => {
                        let parent = $(el).parent().parent();
                        let qty = Number(parent.find('.quantity').val());
                        let rate = Number(parent.find('.rate').val());
                        let days = Number(parent.find('.days').val());
                        let amount_input = parent.find('.amount');
                        let amount = qty * rate * days;
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
                let levy = Number(0.02 * sub_total).toFixed(2);
                $('.vat').val(vat);
                $('.levy').val(levy);
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
                                        <p class="mb-2 suggestion-content" data-id="${element.id}" data-code="${element.code}" data-price="${element.price}" data-description="${element.description}" data-taxable=${element.taxable}>${element.description}</p>
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