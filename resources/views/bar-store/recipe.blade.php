@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex; justify-content: space-between">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('bar.requisition.view')}}">Material Requisition</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    <a href="{{route('bar.requisition.view')}}"><button class="btn btn-warning">Back</button></a>
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

<form action="{{route('bar.store.recipe.store')}}" method="post">
@csrf

<input type="hidden" name="store_id" value="3" />

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
            {{-- invoice details --}}
            <div class="mb-2">
                <h4 class="mb-2">Recipe Details</h4>
                <h6 class="mb-4">Product Details</h6>
            </div>

            <div class="row">
                <div class="mb-3 col-md-4">
                    <label for="type" class="form-label">Item Category</label>
                    <select class="form-select mb-3" name="category_id">
                        <option selected="0">Choose categoty...</option>
                        @foreach ($categories as $cat)
                            <option value="{{$cat->id}}">{{$cat->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3 col-md-4">
                    <label for="code" class="form-label">Item Code</label>
                    <input type="text" required name="code" class="form-control" id="code" autocomplete="off" placeholder="Ex: 123">
                </div>

                <div class="mb-3 col-md-4">
                    <label for="name" class="form-label">Item Name</label>
                    <input type="text" required name="name" class="form-control" id="name" autocomplete="off" placeholder="Ex: Name">
                </div>
                

                <div class="mb-3 col-md-4">
                    <label for="buying_price" class="form-label">Buying Price</label>
                    <input type="text" required name="buying_price" class="form-control" id="buying_price" autocomplete="off" placeholder="Ex: 200">
                </div>

                <div class="mb-3 col-md-4">
                    <label for="price" class="form-label">Selling Price</label>
                    <input type="text" required name="price" class="form-control" id="price" autocomplete="off" placeholder="Ex: 200">
                </div>

                <div class="mb-3 col-md-4">
                    <label for="item_quantity" class="form-label">Quantity</label>
                    <input type="text" required name="item_quantity" class="form-control" id="item_quantity" autocomplete="off" placeholder="Ex: 2">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Item Description</label>
                    <textarea type="text" required name="description" class="form-control" id="description" autocomplete="off" placeholder="Ex: Description"></textarea>
                </div>
            </div>
            {{-- invoice items --}}
            <div>
                {{-- item --}}
                <table class="table table-bordered" id="dataTableExample">
                    <thead style="background: rgb(6, 181, 0); ">
                        <tr >
                            <th style=" color:black">Code</th>
                            <th style="width: 50%; color:black">Item Description</th>
                            <th style=" color:black">Qty</th>
                            <th style="color:black">Action</th>
                        </tr>
                    </thead>
                    <tbody id="table-row">
                        <tr>
                            <td>
                                <input type="text" required name="item_code[]" class="form-control item_code" readonly autocomplete="off">
                                <input type="hidden" name="product_id[]" class="item_id" />
                            </td>
                            <td>
                                <div style="width: 100%;">
                                    <input type="text" required name="product_description[]" class="form-control item_description" autocomplete="off">
                                    <div class="outer-suggestion" style="position: relative; width: 100%; z-index: 100;">
                                        <div class="suggestion" style="position: absolute; background: #f9fafb; border-radius: 5px; color: black; border: 1px solid #94a3b8; width: 100%; padding: 8px;">
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <input type="text" name="product_quantity[]" class="form-control quantity" autocomplete="off">
                            </td>
                            <td>
                                
                            </td>
                        </tr>
                    </tbody>
                </table>
                
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
                            <input type="text" required name="item_code[]" class="form-control item_code" readonly autocomplete="off">
                            <input type="hidden" name="product_id[]" class="item_id" />
                        </td>
                        <td>
                            <div style="width: 100%;">
                                <input type="text" required name="product_description[]" class="form-control item_description" autocomplete="off">
                                <div class="outer-suggestion" style="position: relative; width: 100%; z-index: 100;">
                                    <div class="suggestion" style="position: absolute; background: #f9fafb; border-radius: 5px; color: black; border: 1px solid #94a3b8; width: 100%; padding: 8px;">
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input type="text" name="product_quantity[]" class="form-control quantity" autocomplete="off">
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
                       
                    })
                })
            }

            minus();


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
                            url: "{{route('bar.search')}}",
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