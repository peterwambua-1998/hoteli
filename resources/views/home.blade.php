@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 col-xl-12 stretch-card">
      <div class="row flex-grow-1">
        <div class="col-md-4 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-baseline">
                <h6 class="card-title mb-3">Todays Total Sales</h6>
                
              </div>
              <div class="row">
                <div class="col-6 col-md-12 col-xl-12" >
                  <h3 class="mb-2">Ksh {{number_format($totalSales, 2)}}</h3>

                </div>
                <div class="col-6 col-md-12 col-xl-7">
                  <div id="customersChart" class="mt-md-3 mt-xl-0"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-baseline">
                <h6 class="card-title mb-3">Total Total Payments</h6>
                
              </div>
              <div class="row">
                <div class="col-6 col-md-12 col-xl-12" >
                  <h3 class="mb-2">Ksh {{number_format($totalPayment, 2)}}</h3>
                </div>
                <div class="col-6 col-md-12 col-xl-7">
                  <div id="customersChart" class="mt-md-3 mt-xl-0"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
      </div>
    </div>
</div> <!-- row -->
  
<div class="row">
    <div class="col-xl-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Bar chart</h6>
                <canvas id="chartjsBar"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection


@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/chartjs/chart.umd.js') }}"></script>
@endpush

@push('custom-scripts')
  <script>
    $(async function() {
        'use strict';


        var colors = {
            primary        : "#6571ff",
            secondary      : "#7987a1",
            success        : "#05a34a",
            info           : "#66d1d1",
            warning        : "#fbbc06",
            danger         : "#ff3366",
            light          : "#e9ecef",
            dark           : "#060c17",
            muted          : "#7987a1",
            gridBorder     : "rgba(77, 138, 240, .15)",
            bodyColor      : "#000",
            cardBg         : "#fff"
        }

        var fontFamily = "'Roboto', Helvetica, sans-serif"


        try {
            const url = '{{route("cashflows")}}';
            // Make the GET request
            const response = await fetch(url);
            
            // Check if the request was successful
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response);
            }
            
            // Parse the JSON from the response
            const data = await response.json();
            
            // Handle the data from the response
            console.log(data);

            let mLabel = [];
            let mData = [];

            for (let i = 0; i < data.length; i++) {
                const element = data[i];
                
                mLabel.push(element.bank_name);
                mData.push(element.cashFlow);
            }

            createChart(mData, mLabel);
        } catch (error) {
            // Handle any errors that occurred during the fetch
            console.error('There has been a problem with your fetch operation:', error);
        }


        function createChart(data, labels) {
            // Bar chart
            if($('#chartjsBar').length) {
                new Chart($("#chartjsBar"), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: "Todays Sales",
                            backgroundColor: [colors.primary, colors.danger, colors.warning, colors.success, colors.info],
                            data: data,
                        }
                    ]
                },
                options: {
                    plugins: {
                    legend: { display: false },
                    },
                    scales: {
                    x: {
                        display: true,
                        grid: {
                        display: true,
                        color: colors.gridBorder,
                        borderColor: colors.gridBorder,
                        },
                        ticks: {
                        color: colors.bodyColor,
                        font: {
                            size: 12
                        }
                        }
                    },
                    y: {
                        grid: {
                        display: true,
                        color: colors.gridBorder,
                        borderColor: colors.gridBorder,
                        },
                        ticks: {
                        color: colors.bodyColor,
                        font: {
                            size: 12
                        }
                        }
                    }
                    }
                }
                });
            }
        }

       
    })
  </script>
@endpush



