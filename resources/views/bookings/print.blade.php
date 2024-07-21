<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        .text-center {
            text-align: center !important;
        }
        *,
        *::after,
        *::before {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-size: 13px;
        }

        :root {
            --blue-color: #000000;
            --dark-color: #000000;
            --white-color: #fff;
        }


        .invoice-wrapper {
            min-height: 100vh;
            background-color: rgba(0, 0, 0, 0.1);
            padding-top: 20px;
            padding-bottom: 20px;
        }

        .invoice-head-right table {
            border-collapse: collapse;
        }

        .invoice-bank-info {
            padding: 30px 0 20px 0;
        }

        .footer-contact-info {
            padding: 10px 0px 10px 0px;
            background-color: #fff;
            font-style: italic;
            color: #000000;
            margin-bottom: 10px;
        }

        .invoice-head-right table td {
            border: 2px solid #000000;
            padding: 0 6px;
        }

        .invoice {
            max-width: 850px;
            margin-right: auto;
            margin-left: auto;
            background-color: #fff;
            padding: 70px;
            border: 1px solid rgba(0, 0, 0, 0.12);
            border-radius: 5px;
            min-height: 920px;
        }

        ul {
            list-style-type: none;
        }

        ul li {
            margin: 2px 0;
        }

        .invoice-head-main p{
            text-align: center;
            padding: 2px;
        }

        .invoice-head-top-left img {
            width: 170px;
        }

        .invoice-head-top-right h3 {
            font-weight: 500;
            font-size: 27px;
            color: var(--blue-color);
        }

        .invoice-head-bottom {
            display: flex;
            justify-content: space-between;
        }

        .invoice-head-middle, .invoice-head-bottom {
            padding: 16px 0;
        }

        .invoice-body {
            border-radius: 4px;
            overflow: hidden;
        }

        .invoice-body table {
            border-collapse: collapse;
            width: 100%;
        }

        .invoice-body table .bordered {
            border: 2px solid black;
            padding: 5px 10px;
        }

        .invoice-body table .invoice-content {
            vertical-align: text-top;
            height: fit-content;
        }

        .invoice-body table thead {
            background-color: #92D14F;
        }

        .invoice-body-info-item {
            display: grid;
            grid-template-columns: 80% 20%;
        }

        .invoice-body-info-item .info-item-td {
            padding: 8px;
            
        }

        .invoice-btns {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }

        .invoice-btn {
            padding: 3px 9px;
            color: var(--dark-color);
            font-family: inherit;
            border: 1px solid rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        @media screen and (max-width: 576px) {
            .invoice-head-bottom {
                display: grid;
            }

            .invoice-head-right table{
            margin: 10px 0 0 0;
            }

            .overflow-view {
                overflow-x: scroll;
            }

            .invoice-body {
                min-width: 600px;
            }
        }

        .invoice-wrapper {
        background-color: rgba(0, 0, 0, 0.1);
        padding-top: 20px;
        padding-bottom: 20px;
        }

        .invoice-wrapper-two {
        background-color: #fff;
        padding-top: 20px;
        padding-bottom: 20px;
        }

        .invoice-head-right table {
        border-collapse: collapse;
        }

        .invoice-bank-info {
        padding: 30px 0 20px 0;
        }

        .footer-contact-info {
        padding: 10px 0px 10px 0px;
        background-color: #92D14F;
        font-style: italic;
        color: #000000;
        margin-bottom: 10px;
        }

        .invoice-head-right table td {
        border: 2px solid #000000;
        padding: 0 6px;
        }

        .invoice {
        max-width: 850px;
        margin-right: auto;
        margin-left: auto;
        background-color: white;
        padding: 70px;
        border: 1px solid rgba(0, 0, 0, 0.12);
        border-radius: 5px;
        }

        .invoice-two {
        max-width: 850px;
        margin-right: auto;
        margin-left: auto;
        background-color: white;
        padding: 70px;
        border: 1px solid rgba(0, 0, 0, 0.12);
        border-radius: 5px;
        }

        .invoice-head-main p{
        text-align: center;
        padding: 2px;
        }

        .invoice-head-top-left img {
        width: 170px;
        }

        .invoice-head-top-right h3 {
        font-weight: 500;
        font-size: 27px;
        color: var(--blue-color);
        }

        .invoice-head-bottom {
        display: flex;
        justify-content: space-between;
        }

        .invoice-head-middle, .invoice-head-bottom {
        padding: 16px 0;
        }

        .invoice-body {
        border-radius: 4px;
        overflow: hidden;
        }

        .invoice-body table {
        border-collapse: collapse;
        width: 100%;
        }

        .invoice-body table .bordered {
        border: 2px solid black;
        padding: 5px 10px;
        }

        .invoice-body table .invoice-content {
        vertical-align: text-top;
        }

        .invoice-body table thead {
        background-color: #92D14F;
        }

        .invoice-body-info-item {
        display: grid;
        grid-template-columns: 80% 20%;
        }

        .invoice-body-info-item .info-item-td {
        padding: 8px;
        
        }

        .invoice-btns {
        margin-top: 20px;
        display: flex;
        justify-content: center;
        }

        .invoice-btn {
        padding: 3px 9px;
        color: var(--dark-color);
        font-family: inherit;
        border: 1px solid rgba(0, 0, 0, 0.1);
        cursor: pointer;
        }

        @media screen and (max-width: 576px) {
        .invoice-head-bottom {
            display: grid;
        }

        .invoice-head-right table{
            margin: 10px 0 0 0;
        }

        .overflow-view {
            overflow-x: scroll;
        }

        .invoice-body {
            min-width: 600px;
        }
        }

    </style>
</head>
<body>
    <div class="invoice-wrapper-two" id="print-area">
        <div class="invoice-two">
            <div class="invoice-container">
                <div class="invoice-head">

                    <div class="invoice-head-top" style="display: flex; justify-content: space-between;">
                        <div class="invoice-head-top-left text-start">
                            <img src="{{asset('images/alogo.png')}}" alt="logo-image" srcset="">
                        </div>
                        <div class="invoice-head-top-right text-end" style="align-self: flex-end;">
                            <h3>Invoice</h3>
                        </div>
                    </div>

                    <div class="hr"></div>
                    <div class="invoice-head-middle">

                        <div class="invoice-head-main">
                            <p>KISIMANI ECO RESORT AND SPA LTD</p>
                            <p>P.O Box 56049-00200 Nairobi, Kenya</p>
                            <p>Tel: 0715-120-280, 0733-808-200</p>
                        </div>
            
                        <div class="hr"></div>
                        <div class="invoice-head-bottom">

                            <div class="invoice-head-left">
                                @if ($invoice->account_id)
                                <ul>
                                    <li class="text-bold">Invoiced To:</li>
                                    <li class="text-bold">{{$invoice->customer->name}}</li>
                                    <li class="text-bold">{{$invoice->customer->location}}</li>
                                    <li class="text-bold">{{$invoice->customer->telephone}}</li>
                                    {{-- <li class="text-bold">United Kingdom</li> --}}
                                </ul>
                                @endif
                            </div>

                            <div class="invoice-head-right">
                                <table>
                                    <tr>
                                        <td>Tax Date</td>
                                        <td>{{$invoice->tax_date}}</td>
                                    </tr>
                                    <tr>
                                        <td>Invoice No</td>
                                        <td>{{$invoice->inv_number}}</td>
                                    </tr>
                                    <tr>
                                        <td>Delivery Date</td>
                                        <td>{{$invoice->delivery_date}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-center">Company VAT Reg</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"  class="text-center">{{$invoice->customer->vat_registration_number}}</td>
                                    </tr>
                                </table>
                            </div>
                            
                        </div>
                    </div>

                    <div class="overflow-view">
                        <div class="invoice-body">
                            <table>
                                <thead>
                                    <tr>
                                        <td class="text-bold bordered">Item Code</td>
                                        <td class="text-bold bordered">Item Description</td>
                                        <td class="text-bold bordered">Qty</td>
                                        <td class="text-bold bordered">Rate</td>
                                        <td class="text-bold bordered">Days</td>
                                        <td class="text-bold bordered text-end">Amount</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoice->items as $item)
                                    <tr class="invoice-content">
                                        <td class="bordered">{{$item->item_code}}</td>
                                        <td class="bordered" >
                                            {{$item->item_description}}
                                            
                                        </td>
                                        <td class="bordered">{{$item->quantity}}</td>
                                        <td class="bordered">{{$item->rate}}</td>
                                        <td class="bordered">{{$item->days}}</td>
                                        <td class="text-end bordered">{{number_format($item->amount, 2)}}</td>
                                    </tr>
                                    @endforeach

                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td colspan="2" class="bordered text-bold">Sub Total</td>
                                        <td class="bordered text-end">{{number_format($invoice->sub_total, 2)}}</td>
                                       
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td colspan="2" class="bordered text-bold">Vat (16%)</td>
                                        <td class="bordered text-end">{{number_format($invoice->tax_amount, 2)}}</td>
                                       
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td colspan="2" class="bordered text-bold">Total Amount</td>
                                        <td class="bordered text-end">{{number_format($invoice->total, 2)}}</td>
                                       
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @foreach ($bankAccounts as $bankAccount)
                <div class="invoice-bank-info">
                    <p>Bank: <span class="text-bold">{{$bankAccount->bank_name}}</span></p>
                    <p>Account Name: <span class="text-bold">{{$bankAccount->account_name}}</span></p>
                    <p>Account No: <span class="text-bold">{{$bankAccount->account_number}}</span></p>
                    <p>Branch: <span class="text-bold">{{$bankAccount->branch}}</span></p>
                </div>
                @endforeach
               
                <div class="invoice-foot text-center mt-3">

                    <div class="footer-contact-info">
                        <p><span class="text-bold">Email; kisimaniresort@gmail.com, resortisiolo@gmail.com.</span></p>
                        <p><span class="text-bold">Blending Nature With Modern Hospitality</span></p>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script async>
        window.print();
    </script>
</body>
</html>