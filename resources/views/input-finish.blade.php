@extends('layouts.app')

@section('content')
<div class="row">

        {{-- item --}}
        <table class="table table-bordered" id="dataTableExample">
            <thead style="background: rgb(6, 181, 0); color:black;">
                <tr>
                    <th style="width: 10%; color:black">Code</th>
                    <th style="width: 40%; color:black">Item Description</th>
                    <th style="width: 10%; color:black">Qty</th>
                    <th style="width: 15%; color:black">Rate</th>
                    <th style="width: 10%; color:black">Days</th>
                    <th style="width: 20%; color:black">Amount</th>
                    <th style="color:black">Action</th>
                </tr>
            </thead>
            <tbody id="table-row">
                <tr>
                    <td>
                        <input type="text" required name="item_code[]" class="form-control item_code" autocomplete="off">
                        <input type="hidden" name="item_id[]" />
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
                        <input type="text" name="quantity[]" class="form-control quantity" autocomplete="off">
                    </td>
                    <td>
                        <input type="text" name="rate[]" class="form-control rate" autocomplete="off">
                    </td>
                    <td>
                        <input type="text" name="days[]" class="form-control days" autocomplete="off">
                    </td>
                    <td>
                        <input type="text" readonly name="amount[]" class="form-control amount" autocomplete="off">
                    </td>
                    <td>
                        
                    </td>
                </tr>
                
            </tbody>
        </table>
   
    {{-- <div style="width: 100%;">
        <input type="text" name="item_description[]" class="form-control item_description" autocomplete="off">
        <div class="outer-suggestion" style="position: relative; width: 100%">
            <div class="suggestion" style="position: absolute; background: #f9fafb; border-radius: 5px; color: black; border: 1px solid #94a3b8; width: 100%; padding: 8px;">
                
                
            </div>
        </div>
        
    </div>
    <input type="text" name="item_code[]" class="form-control item_code" autocomplete="off"> --}}
</div>
@endsection

@push('custom-scripts')
<script>
    $(function () {
        // hide suggestion
        $('.outer-suggestion').each((index, element) => {
            $(element).hide();
        });

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
                            if (response.length == 0) {
                                outer_suggestion.hide();
                            }

                            let template = '';
                            for (let i = 0; i < response.length; i++) {
                                let element = response[i];
                                template += `
                                    <p class="mb-2 suggestion-content" data-id="${element.id}" data-code="${element.code}" data-price="${element.price}" data-description="${element.description}">${element.description}</p>
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
                    let input_description = parentDiv.find('.item_description').val(item_description);

                    let tr = $(element).parent().parent().parent().parent().parent();
                    let input_code  = tr.find('.item_code').val(item_code);
                    let input_price  = tr.find('.rate').val(item_price);
                    let item_id = tr.find('.item_id').val(item_id);
                });
            })
        }

        inputSuggestions();
        
    })
</script>
@endpush