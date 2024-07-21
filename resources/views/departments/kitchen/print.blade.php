<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kismani Print</title>
    <link rel="shortcut icon" href="{{ asset('images/new-ic.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/f34b8c32fc.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
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
    </style>
</head>
<body>
    <div class="flex justify-center w-[100vw] h-[100vh] bg-red-500 bg-slate-50">
      <div class="bg-white w-[36vw] p-4">
        {{-- header --}}
        <div class="flex justify-between text-sm">
          <div>
            <p>{{$order->created_at}}</p>
            <p>Store: Kisimani</p>
          </div>

          <div>
            <p>Sales Receipt <span class="font-bold">#{{$order->inv_number}}</span></p>
            {{-- <p>Workstation: 12</p> --}}
          </div>
        </div>
        {{-- header --}}

        <div class="text-center mt-10">
          <p class="text-2xl font-bold">KISIMANI ECO RESORT & SPA</p>
        </div>

        {{-- address --}}
        <div class="text-center border-b pb-4">
          <p>Isiolo Kenya</p>
          <p>0700 000 000</p>
        </div>

        <div>
          <table class="border border-slate-200 rounded w-full text-sm text-center">
            <thead>
              <tr class="font-bold">
                <td>Item Name</td>
                <td>Qty</td>
                <td>Unit Price</td>
                <td>Total Price</td>
              </tr>
            </thead>
            <tbody>
              @foreach ($order_details as $item)
              <tr>
                <td>{{$item->item->name}}</td>
                <td>{{$item->quantity}}</td>
                <td>KSH {{$item->item->price}}</td>
                <td>KSH {{$item->amount}}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <div class="justify-end flex gap-2 w-full mt-4">
            <p>Sub Total:</p>
            <p>KSH {{number_format($order->sub_total, 2)}}</p>
          </div>
          <div class="justify-end flex gap-2 w-full">
            <p>VAT:</p>
            <p>{{number_format($order->tax_amount, 2)}}</p>
          </div>
          <div class="justify-end flex gap-2 w-full">
            <p>Tourism:</p>
            <p>{{number_format($order->levy, 2)}}</p>
          </div>
          <div class="justify-end flex gap-2 w-full">
            <p>Total:</p>
            <p>KSH {{number_format($order->total, 2)}}</p>
          </div>
        </div>
      </div>
    </div>

    <script async>
      //window.print();
    </script>
</body>
</html>