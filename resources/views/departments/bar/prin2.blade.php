<!DOCTYPE html>
<html lang="en">
<head>

</head>

<body>
    <div style="visibility: hidden" id="print-receipt-div">
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
        <div id="logo" class="media">Bar Order</div>

    </header>
    <table class="bill-details">
        <tbody>
            <tr>
                <td>Date : <span id="order_created_at_date"></span></td>
                <td>Time : <span id="order_created_at_time"></span></td>
            </tr>
            <tr>
                <td>Bill # : <span id="inv_number"></span></td>
                <td>Waiter # : <span id="inv_number" style="font-weight: bolder">
                    @if (Auth::user())
                    {{Auth::user()->name}}
                    @endif    
                </span></td>
            </tr>
        </tbody>
    </table>
    
    <table class="items">
        <thead>
            <tr>
                <th class="heading name">Item</th>
                <th class="heading qty">Qty</th>
            </tr>
        </thead>
       
        <tbody id="invoice_content">
           
            
            
            
        </tbody>
    </table>
    
</div>
    
</body>

</html>