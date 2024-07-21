
@foreach ($booking->invoices as $invoice)
    @if ($invoice->receipt->count() > 0)
        @foreach ($invoice->receipt as $receipt)
        <div>
            <button class="btn btn-info" onclick="$('#print-a-{{$receipt->id}}').print();">Print</button>
        </div>
        <div class="row mt-5">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                    
                    <div class="invoice-wrapper-two" id="print-a-{{$receipt->id}}">
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
                        <div class="invoice-two">
                            <div class="invoice-container">
                                <div class="invoice-head">
                
                                    <div class="invoice-head-top" style="display: flex; justify-content: space-between;">
                                      <div class="invoice-head-top-left text-start">
                                          <img src="{{asset('images/alogo.png')}}" alt="logo-image" srcset="">
                                      </div>
                                      <div class="invoice-head-top-right text-end" style="align-self: flex-end;">
                                          <h3>Receipt</h3>
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
                                                <ul>
                                                    <li class="text-bold">Billed To:</li>
                                                    @if ($invoice->account_id)
                                                        <ul>
                                                            <li class="text-bold">Invoiced To:</li>
                                                            <li class="text-bold">{{$invoice->customer->name}}</li>
                                                            <li class="text-bold">{{$invoice->customer->location}}</li>
                                                            <li class="text-bold">{{$invoice->customer->telephone}}</li>
                                                            {{-- <li class="text-bold">United Kingdom</li> --}}
                                                        </ul>
                                                    @endif
                                                    {{-- <li class="text-bold">United Kingdom</li> --}}
                                                </ul>
                                            </div>
                
                                            <div class="invoice-head-right">
                                                <table>
                                                    <tr>
                                                        <td>Receipt No</td>
                                                        <td>{{$receipt->receipt_number}}</td>
                                                    </tr>
                                                    <tr>
                                                      <td>Payment Methods</td>
                                                      <td>
                                                        @if ($receipt->payment_method == 1)
                                                            Cash
                                                        @endif
          
                                                        @if ($receipt->payment_method == 2)
                                                            Mpesa
                                                        @endif
          
                                                        @if ($receipt->payment_method == 3)
                                                            Bank Transfer
                                                        @endif
          
                                                        @if ($receipt->payment_method == 4)
                                                            Cheque
                                                        @endif
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td>Payment Account</td>
                                                      <td>
                                                        @if ($receipt->payment_method == 1)
                                                            Cash
                                                        @endif
          
                                                        @if ($receipt->payment_method == 2)
                                                            Mpesa
                                                        @endif
          
                                                        @if ($receipt->payment_method == 3)
                                                            {{$receipt->bankAccount->bank_name}}
                                                        @endif
          
                                                        @if ($receipt->payment_method == 4)
                                                            Cheque
                                                        @endif
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Reference No</td>
                                                        <td>{{$receipt->payment_code}}</td>
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
                                                        <td class="text-bold bordered">Subtotal</td>
                                                        <td class="text-bold bordered">Vat</td>
                                                        <td class="text-bold bordered">Total</td>
                                                        <td class="text-bold bordered">Paid</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="invoice-content">
                                                        <td class="bordered">{{number_format($receipt->sub_total, 2)}}</td>
                                                        <td class="bordered">{{number_format($receipt->tax_amount, 2)}}</td>
                                                        <td class="bordered">{{number_format($receipt->amount, 2)}}</td>
                                                        <td class="bordered">{{number_format($receipt->paid_amount, 2)}}</td>
                                                        
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="invoice-foot text-center mt-5">
                
                                    <div class="footer-contact-info">
                                        <p><span class="text-bold">Email; kisimaniresort@gmail.com, resortisiolo@gmail.com.</span></p>
                                        <p><span class="text-bold">Blending Nature With Modern Hospitality</span></p>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- print area --}}
        @endforeach
    @endif
@endforeach
