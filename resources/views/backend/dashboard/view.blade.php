@extends('backend.master')

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.45.1/apexcharts.min.css">
@endpush

@section('content')
    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Budget Overview Card -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Budget Overview</h5>
                        </div>
                        <div class="card-body">
                            <div id="budgetOverviewChart"></div>
                        </div>
                    </div>
                </div>

                <!-- Task Progress Card -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Task Progress</h5>
                        </div>
                        <div class="card-body">
                            <div id="taskProgressChart"></div>
                        </div>
                    </div>
                </div>

                <!-- Cost Variance Trend -->
                {{-- <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Cost Variance Trend</h5>
                        </div>
                        <div class="card-body">
                            <div id="costVarianceChart"></div>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
        <!-- container-fluid -->
    </div>
    <!-- content -->
@endsection

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.45.1/apexcharts.min.js"></script>
    <script>
        // Budget Overview Chart
        let budgetOverviewOptions = {
            series: [{
                name: 'Planned Budget',
                data: @json($chartData['budgetOverview']['planned'])
            }, {
                name: 'Actual Cost',
                data: @json($chartData['budgetOverview']['actual'])
            }],
            chart: {
                type: 'bar',
                height: 350,
                stacked: false,
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: @json($chartData['budgetOverview']['projects']),
            },
            yaxis: {
                title: {
                    text: 'Amount (TK)'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "TK " + val.toLocaleString()
                    }
                }
            }
        };

        let budgetOverviewChart = new ApexCharts(
            document.querySelector("#budgetOverviewChart"),
            budgetOverviewOptions
        );
        budgetOverviewChart.render();

        // Task Progress Donut Chart
        let taskProgressOptions = {
            series: @json($chartData['taskProgress']['values']),
            chart: {
                type: 'donut',
                height: 350
            },
            labels: @json($chartData['taskProgress']['labels']),
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        let taskProgressChart = new ApexCharts(
            document.querySelector("#taskProgressChart"),
            taskProgressOptions
        );
        taskProgressChart.render();

        // Cost Variance Trend Line Chart
        let costVarianceOptions = {
            series: [{
                name: 'Cost Variance',
                data: @json($chartData['costVariance']['values'])
            }],
            chart: {
                height: 350,
                type: 'line',
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                categories: @json($chartData['costVariance']['dates'])
            },
            yaxis: {
                title: {
                    text: 'Amount (TK)'
                }
            },
            markers: {
                size: 4
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "TK " + val.toLocaleString()
                    }
                }
            }
        };

        let costVarianceChart = new ApexCharts(
            document.querySelector("#costVarianceChart"),
            costVarianceOptions
        );
        costVarianceChart.render();
    </script>
@endpush
