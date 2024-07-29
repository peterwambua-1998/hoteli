@foreach ($orders as $invoice)

    <div style="visibility: hidden" id="print-area-{{$invoice->id}}">
        <style>
            body {
                margin: 0;
                padding: 0;
                font-family: 'PT Sans', sans-serif;
            }
    
            @page {
                size: 2.8in 11in;
                margin-top: 0cm;
                margin-left: 0cm;
                margin-right: 0cm;
            }
    
            table {
                width: 100%;
            }
    
            tr {
                width: 100%;
    
            }
    
            h1 {
                text-align: center;
                vertical-align: middle;
            }
    
            #logo {
                width: 60%;
                text-align: center;
                -webkit-align-content: center;
                align-content: center;
                padding: 5px;
                margin: 2px;
                display: block;
                margin: 0 auto;
            }
    
            header {
                width: 100%;
                text-align: center;
                -webkit-align-content: center;
                align-content: center;
                vertical-align: middle;
            }
    
            .items thead {
                text-align: center;
            }
    
            .center-align {
                text-align: center;
            }
    
            .bill-details td {
                font-size: 12px;
            }
    
            .receipt {
                font-size: medium;
            }
    
            .items .heading {
                font-size: 12.5px;
                text-transform: uppercase;
                border-top:1px solid black;
                margin-bottom: 4px;
                border-bottom: 1px solid black;
                vertical-align: middle;
            }
    
            .items thead tr th:first-child,
            .items tbody tr td:first-child {
                width: 47%;
                min-width: 47%;
                max-width: 47%;
                word-break: break-all;
                text-align: left;
            }
    
            .items td {
                font-size: 12px;
                text-align: right;
                vertical-align: bottom;
            }
    
            .price::before {
                 content: "ksh";
                font-family: Arial;
                text-align: right;
            }
    
            .sum-up {
                text-align: right !important;
            }
            .total {
                font-size: 13px;
                border-top:1px dashed black !important;
                border-bottom:1px dashed black !important;
            }
            .total.text, .total.price {
                text-align: right;
            }
            .total.price::before {
                content: "ksh"; 
            }
            .line {
                border-top:1px solid black !important;
            }
            .heading.rate {
                width: 20%;
            }
            .heading.amount {
                width: 25%;
            }
            .heading.qty {
                width: 5%
            }
            p {
                padding: 1px;
                margin: 0;
            }
            section, footer {
                font-size: 12px;
            }
        </style>
        <header>
            <div id="logo" class="media">KISIMANI ECO RESORT & SPA</div>

        </header>
        <table class="bill-details">
            <tbody>
                <tr>
                    <td>Date : <span id="order_created_at_date">{{$invoice->created_at->format('Y-m-d')}}</span></td>
                    <td>Time : <span id="order_created_at_time">{{$invoice->created_at->format('h:m:s')}}</span></td>
                </tr>
                <tr>
                    <td>Bill # : <span id="inv_number">{{$invoice->inv_number}}</span></td>
                </tr>
            </tbody>
        </table>
        
        <table class="items">
            <thead>
                <tr>
                    <th class="heading name">Item</th>
                    <th class="heading qty">Qty</th>
                    <th class="heading rate">Rate</th>
                    <th class="heading amount">Amount</th>
                </tr>
            </thead>
        
            <tbody id="invoice_content">
            
                @foreach ($invoice->items as $item)
                <tr>
                    <td >{{$item->item_description}}</td>
                    <td>{{$item->quantity}}</td>
                    <td class="price">{{$item->rate}}</td>
                    <td class="price">{{$item->amount}}</td>
                </tr>
                @endforeach
                
                
                <tr>
                    <td colspan="3" class="sum-up line">Subtotal</td>
                    <td class="line price" id="sub_total_receipt">{{number_format($invoice->sub_total, 2)}}</td>
                </tr>
                <tr>
                    <td colspan="3" class="sum-up">VAT:</td>
                    <td class="price" id="vat_receipt">{{number_format($invoice->tax_amount, 2)}}</td>
                </tr>
                <tr>
                    <td colspan="3" class="sum-up">Levy:</td>
                    <td class="price" id="levy_receipt">{{number_format($invoice->levy, 2)}}</td>
                </tr>
                <tr>
                    <th colspan="3" class="total text">Total</th>
                    <th class="total price" id="total_receipt">{{number_format($invoice->total, 2)}}</th>
                </tr>
            </tbody>
        </table>
        <section>
        
            <p>
                Served By: : <span id="served_by"></span>
            </p>
            <p>
                Customer signature: 
            </p>
            <p>
                Room: 
            </p>

            <p style="text-align:center padding-top: 1px;">
                Thank you for your visit!
            </p>
        </section>
        <footer style="text-align:center; font-size: 8px;">
            <p style="border-bottom: 2px solid black;">THIS IS NOT A FISCAL RECEIPT OBTAIN A FISCAL RECEIPT AT THE BACK OFFICE</p>
            <p>Handcrafted By: Zulten Technologies Ltd</p>
            <p>0722808893/0715100539</p>
        </footer>

    </div>
    

@endforeach
