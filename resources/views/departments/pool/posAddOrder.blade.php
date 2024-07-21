<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kisimani - Bar POS</title>
    <link rel="shortcut icon" href="{{ asset('images/new-ic.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/f34b8c32fc.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css"  rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <style>
        
        /* width */
        ::-webkit-scrollbar {
          width: 10px;
        }
        
        /* Track */
        ::-webkit-scrollbar-track {
          background: #f1f1f1; 
        }
         
        /* Handle */
        ::-webkit-scrollbar-thumb {
          background: #888; 
        }
        
        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
          background: #555; 
        }

        body {
            font-family: "Inter", sans-serif;
        }

        .m-card {
            border: 1px solid #94A3B8;
        }

        .item-add {
            border: 1px solid #94A3B8;
        }

        .item-subtract {
            border: 1px solid #94A3B8;
        }

        .m-glass {
            /* From https://css.glass */
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            margin: 10% auto;
            color: white;
        }
        </style>
</head>
<body >
    <div>
        <div class="grid grid-cols-6 absolute bottom-0 top-0 right-0 w-full">
            <div class="col-span-4 pl-4 pr-4 pt-2">
                <div class="h-[14vh]">
                    {{-- search bar --}}
                    <div >
                        <input id="search-input" type="text" class="outline-none bg-[#A97B3B] placeholder-black/50 border border-slate-500 p-2 rounded-lg w-full" placeholder="Search..." />
                    </div>
                    {{-- search bar --}}

                </div>

                {{-- products --}}
                <div class="h-[78vh]  ">
                    <div class="overflow-y-scroll  h-[100%] ">
                        <div class="pr-6 w-full h-full grid md:grid-cols-2 lg:grid-cols-3 md:gap-4 lg:gap-6" id="items">
                            @foreach ($items as $item)
                            <div class="bg-[#f3f4f6] p-2 h-fit rounded-lg m-card font-medium">
                                <input type="hidden" value="{{$item->id}}" class="item-id" />
                                <div class="grid grid-cols-4 mb-6">
                                    <div class="col-span-3">
                                        <p class="item-name text-sm">{{$item->name}}</p>
                                        <p>Ksh <span class="item-price">{{$item->price}}</span></p>
                                        <input  type="hidden" class="taxable" value="{{$item->taxable}}" />
                                    </div>
                                    <div class="col-span-1 bg-white text-center rounded m-card">
                                        <p class="text-4xl item-amount">0</p>
                                    </div>
                                </div>
                                <div>
                                    <button class="bg-[#A97B3B] w-full pt-2 pb-2 rounded text-white mb-6 item-add text-sm">Add</button>
                                    <button class="bg-[#FFFFFF] w-full pt-2 pb-2 rounded text-black item-subtract text-sm">Subtract</button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                {{-- products --}}
            </div>
            <div class="col-span-2 w-[100%] bg-[#292524] pl-4 pr-4 relative">
                {{-- order number --}}
                {{-- order number --}}
                {{-- buttons --}}
                <div class="grid grid-cols-2 md:gap-4 lg:gap-6 mb-4 mt-2">
                    {{-- <button class="bg-[#A97B3B] text-white pt-2 pb-2 rounded" data-modal-target="customer-modal" data-modal-toggle="customer-modal">Add Customer</button> --}}
                    {{-- <button data-modal-target="default-modal" data-modal-toggle="default-modal" class="bg-[#3B623D] text-white pt-2 pb-2 rounded"><span class="pr-4">Table</span> <i class="fa-solid fa-pencil"></i></button> --}}
                </div>
                {{-- buttons --}}

                <div class="h-[40vh] bg-white p-2 rounded overflow-y-scroll" id="cart-content">
                    
                </div>

                <div class="absolute left-0 right-0 bottom-0 pl-4 pr-4 font-medium ">
                    <div class="m-glass p-2 mb-2 rounded">
                        <div class="border-b pb-2">
                            <div class="flex justify-between">
                                <p>Sub Total</p> 
                                <p>Ksh <span id="sub-total">0</span></p> 
                            </div>
                            <div class="flex justify-between">
                                <p>Tax 16%</p> 
                                <p id="vat"></p> 
                            </div>
                            <div class="flex justify-between">
                                <p>Levy 2%</p> 
                                <p id="levy"></p> 
                            </div>
                        </div>

                        <div class="flex justify-between mb-2 pt-2">
                            <p>Total</p>
                            <p>Ksh <span id="total">0</span></p>
                        </div>

                        <div>
                            <button type="button" class="bg-[#3B623D] pt-2 pb-2 w-full mb-2 border hover:bg-[#264028] rounded" id="print-order">Save Order</button>
                            @if (Auth::user()->role == 1)
                                <a href="/orders"><button class="bg-stone-500 border pt-2 pb-2 w-full rounded hover:bg-stone-600 hover:text-white">Back</button></a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main modal -->
    <div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Table
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="default-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4">
                    <div>
                        <label for="table_number" class="block mb-2 text-sm font-medium text-gray-900 ">Enter Table Number</label>
                        <input type="text" id="table_number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-slate-500 focus:border-slate-500 block w-full p-2.5 " placeholder="Ex: 10" required />
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b ">
                    <button data-modal-hide="default-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100">Close</button>
                </div>
            </div>
        </div>
    </div>

    

    

    {{-- all orders --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" integrity="sha512-3P8rXCuGJdNZOnUx/03c1jOTnMn3rP63nBip5gOP2qmUh5YAdVAvFZ1E+QLZZbC1rtMrQb+mah3AfYW11RUrWA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
    <script>
       

        let cartContent = [];
        

        function cart() {
            $('.item-add').each((index, element) => {
                $(element).on('click', (e) => {
                    let parent = $(element).parent().parent();
                    let id = Number(parent.find('.item-id').val());

                    let itemAmount = parent.find('.item-amount');

                    if (cartContent[id] == undefined) {
                        let name = parent.find('.item-name').text();
                        let price = Number(parent.find('.item-price').text());
                        let taxable = Number(parent.find('.taxable').val());
                        let currentCardCount = Number(parent.find('.item-amount').text()) + 1;
                        let data = {
                            'name': name,
                            'price': price,
                            'currentCardCount': currentCardCount,
                            'parent': parent,
                            'taxable': taxable
                        }
                        cartContent[id] = data;
                        itemAmount.text(currentCardCount);
                    } else {
                        cartContent[id].currentCardCount += 1;
                        itemAmount.text(cartContent[id].currentCardCount);
                    }

                    cartContentDisplay(cartContent);
                });
            });

            $('.item-subtract').each((index, element) => {
                $(element).on('click', (e) => {
                    let parent = $(element).parent().parent();
                    let id = Number(parent.find('.item-id').val());
                    let itemAmount = parent.find('.item-amount');
                    let content = cartContent[id];
                    content.currentCardCount -= 1;
                    if (content.currentCardCount == 0) {
                        cartContent.splice(id, 1);
                        itemAmount.text(0);
                    } else {
                        itemAmount.text(content.currentCardCount);
                    }

                    cartContentDisplay(cartContent);
                });
            });
        }

        function cartContentDisplay(cartContent) {
            let template = '';

            for (let i = 1; i < cartContent.length; i++) {
                if (cartContent[i]) {
                    template += `
                        <div class="bg-slate-300 p-2 flex justify-between items-center mb-4 rounded">
                            <div class="flex items-center gap-2">
                                <p class="rounded-full bg-white pl-2 pr-2 text-center">${cartContent[i].currentCardCount}</p>
                                <p>${cartContent[i].name}</p>
                            </div>
                            <p>Ksh ${cartContent[i].price}</p>
                        </div>
                    `;  
                }
            }

            $('#cart-content').html(template);
            calulateTotal(cartContent);
        }

        function calulateTotal(cartContent) {
            console.log(cartContent);
            let vat = 0;
            let sub_total = 0;
            let sub_total_non_taxable = 0;

            let total = 0;
            let total_taxable = 0;

            for (let i = 1; i < cartContent.length; i++) {
                if (cartContent[i]) {
                    let content = cartContent[i];
                    let isTaxable = content.taxable;

                    if (isTaxable == 0) {
                        let amount = content.price * content.currentCardCount;
                        sub_total_non_taxable += amount;
                        total_taxable += amount;
                    } else {
                        let amount = content.price * content.currentCardCount;
                        sub_total += amount;
                        total += amount;
                    }
                    
                }
            }

            sub_total = Number(sub_total / 1.16).toFixed(2);
            console.log(sub_total, sub_total_non_taxable);
            let sub_total_display = Number(sub_total) + Number(sub_total_non_taxable);
            vat = Number(total - Number(sub_total)).toFixed(2);
            let levy = Number(0.02 * sub_total).toFixed(2);
         
            let t = Number(parseFloat(sub_total) + parseFloat(vat) + parseFloat(sub_total_non_taxable)).toFixed(2);
            // cal tax
            $('#vat').text(Number(vat).toFixed(2));
            $('#levy').text(levy);
            $('#sub-total').text(Number(sub_total_display).toFixed(2));
            $('#total').text(t);
        }

        $('#search-input').on('input', (e) => {
            let value = e.target.value;

            let data = new FormData;
            data.append('_token', '{{csrf_token()}}');
            data.append('search_tearm', value);

            $.ajax({
                url: '{{route("search-item-orders")}}',
                type: 'POST',
                processData: false,
                contentType: false,
                cache: false,
                data: data,
                error: (err) => {
                    console.log(err);
                },
                success: (response) => {
                    console.log(response);
                    let template = '';
                    Array.from(response).forEach(element => {
                        let content = cartContent[element.id];
                        let qty = 0;
                        if (content) {
                            qty = content.currentCardCount;
                        }
                        template += `
                            <div class="bg-[#f3f4f6] p-2 h-fit rounded-lg m-card font-medium">
                                <input type="hidden" value="${element.id}" class="item-id" />
                                <div class="grid grid-cols-4 mb-6">
                                    <div class="col-span-3">
                                        <p class="item-name text-sm">${element.name}</p>
                                        <p>Ksh <span class="item-price">${element.price}</span></p>
                                        <input  type="hidden" class="taxable" value="${element.taxable}" />
                                    </div>
                                    <div class="col-span-1 bg-white text-center rounded  m-card">
                                        <p class="text-4xl item-amount">${qty}</p>
                                    </div>
                                </div>
                                <div>
                                <button class="bg-[#A97B3B] w-full pt-2 pb-2 rounded text-white mb-6 item-add text-sm">Add</button>
                                    <button class="bg-[#FFFFFF] w-full pt-2 pb-2 rounded text-black item-subtract text-sm">Subtract</button>
                                </div>
                            </div>
                        `
                    });

                    $('#items').html(template);
                    cart();
                }
            })
        });


        // print order
        $('#print-order').on('click', (e) => {
            if (cartContent.length > 0) {
                let data = new FormData;
                data.append('_token', '{{csrf_token()}}');
                data.append('sub_total', $('#sub-total').text());
                data.append('vat', $('#vat').text());
                data.append('levy', $('#levy').text());
                data.append('total', $('#total').text());
                data.append('invoice_id', "{{$order->id}}");

                for (let i = 1; i < cartContent.length; i++) {
                    if (cartContent[i]) {
                        let element = cartContent[i];
                        data.append('name[]', element.name);
                        data.append('price[]', element.price);
                        data.append('quantity[]', element.currentCardCount);
                        data.append('item_id[]', i);
                    }
                }

                $.ajax({
                    url: '{{route("bar.add_existing.save")}}',
                    type: 'POST',
                    processData: false,
                    contentType: false,
                    cache: false,
                    data: data,
                    error: (err) => {
                        console.log(err);
                    },
                    success: (response) => {
                        if (response.status == 1) {
                            window.open(`http://127.0.0.1:8000/bar-store/orders/${response.order_id}/print`, '_blank')
                            window.location.href = '/waiter/orders';
                        }
                    }
                }) 
               
            } 
        });

        cart();
    </script>
</body>
</html>