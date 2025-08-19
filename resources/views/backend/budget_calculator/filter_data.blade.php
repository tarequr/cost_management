@extends('backend.master')

@push('css')
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1a2a6c, #2a4d69);
            color: #333;
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        header {
            background: #2a4d69;
            color: white;
            padding: 25px 30px;
            text-align: center;
        }

        h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #e0e7ff;
            font-size: 1.1rem;
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .chart-container {
            padding: 30px;
            position: relative;
            height: 500px;
            background: #f8f9ff;
        }

        .metrics-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            padding: 30px;
            background: white;
            border-top: 1px solid #eaeaea;
        }

        .metric-card {
            background: #f0f5ff;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border-left: 4px solid #2a4d69;
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .metric-card h3 {
            color: #1a2a6c;
            font-size: 1.1rem;
            margin-bottom: 12px;
        }

        .metric-card .value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2a4d69;
            margin-bottom: 5px;
        }

        .metric-card .label {
            font-size: 0.95rem;
            color: #4b86b4;
            margin-bottom: 8px;
        }

        .status-indicator {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            background: #ff6b6b;
            color: white;
            margin-top: 10px;
            font-size: 0.95rem;
        }

        .status-indicator.good {
            background: #51cf66;
        }

        .status-indicator.warning {
            background: #ffd166;
            color: #333;
        }

        .legend {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 25px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            border-top: 1px solid #eee;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .legend-color {
            width: 25px;
            height: 25px;
            border-radius: 4px;
        }

        .legend-text {
            font-size: 1rem;
            color: #2a4d69;
            font-weight: 500;
        }

        .analysis {
            background: #e3f2fd;
            padding: 25px;
            margin: 20px;
            border-radius: 10px;
        }

        .analysis h3 {
            color: #1a2a6c;
            margin-bottom: 15px;
            border-bottom: 2px solid #bbdefb;
            padding-bottom: 10px;
        }

        .analysis p {
            line-height: 1.7;
            color: #333;
            margin-bottom: 15px;
        }

        .highlight {
            background: #fff8e1;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .chart-container {
                height: 400px;
                padding: 15px;
            }

            .metrics-container {
                grid-template-columns: 1fr;
                padding: 20px;
            }

            header {
                padding: 20px 15px;
            }
        }
    </style> --}}
@endpush

@section('content')
    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <h4 class="page-title">Budget Breakdown: {{ $budgetEstimate->project_name }}</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class=" float-right">
                            <a href="{{ route('budget-estimate.index') }}" class="btn btn-sm btn-danger">
                                <i class="fa fa-arrow-left"></i>
                                Back
                            </a>
                        </ol>
                    </div>
                </div> <!-- end row -->
            </div>
            <!-- end page-title -->

            {{-- <header>
                <h1>Earned Value Management (EVM)</h1>
                <p class="subtitle">Track your project's performance with key financial metrics including Budget at
                    Completion (BAC), Planned Value (PV), Actual Cost (AC), Earned Value (EV), and Estimate at Completion
                    (EAC)</p>
            </header>

            <div class="chart-container">
                <canvas id="evChart"></canvas>
            </div>

            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #2a4d69;"></div>
                    <div class="legend-text">BAC (Budget at Completion)</div>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #4b86b4;"></div>
                    <div class="legend-text">PV (Planned Value)</div>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #ff6b6b;"></div>
                    <div class="legend-text">AC (Actual Cost)</div>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #51cf66;"></div>
                    <div class="legend-text">EV (Earned Value)</div>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #ffd166;"></div>
                    <div class="legend-text">EAC (Estimate at Completion)</div>
                </div>
            </div> --}}




            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-md border-0 mb-4">
                        <div class="card-body">
                            <div>
                                <h4 class="card-title text-primary mb-4 text-capitalize text-bold">Budget Calculator</h4>

                                <div class="mb-4">
                                    <span class="text-muted text-uppercase">Duration</span>
                                    <p class="font-weight-bold mb-0">{{ $fromMonth }} - {{ $toMonth }}</p>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-6">
                                        <div class="card bg-primary p-3 mb-3">
                                            <span class="text-muted text-uppercase">Estimate Amount</span>
                                            <p class="h4 font-weight-bold mb-0 text-light">{{ number_format($pv) }}</p>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="card bg-secondary p-3 mb-3">
                                            <span class="text-muted text-uppercase">Actual Cost</span>
                                            <p class="h4 font-weight-bold mb-0 text-light">{{ number_format($ac) }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="border-top pt-3">
                                <span class="text-muted text-uppercase">Delivery Date</span>
                                <p class="h5 font-weight-bold mb-0">
                                    <i class="bi bi-calendar text-primary me-2"></i>
                                    00
                                </p>
                            </div> --}}

                                <div class="row mb-4 mt-2">
                                    {{-- <div class="col-12">
                                    <span class="text-muted text-uppercase">EV :</span>
                                    <p class="h4 font-weight-bold mb-0">{{ ceil($earnedValue) }}</p>
                                </div>
                                <div class="col-12">
                                    <span class="text-muted text-uppercase">PV :</span>
                                    <p class="h4 font-weight-bold mb-0">{{ ceil($plannedValue) }}</p>
                                </div>
                                <div class="col-12">
                                    <span class="text-muted text-uppercase">BAC :</span>
                                    <p class="h4 font-weight-bold mb-0">{{ ceil($bac) }}</p>
                                </div> --}}
                                    {{-- @if (!$isNullBudgetCalculators) --}}
                                    <div class="col-12">
                                        <span class="text-muted text-uppercase">Budget :</span>
                                        {{-- <p class="h4 font-weight-bold mb-0">{{ ceil($costVariance) }}</p> --}}
                                        <p class="h6 font-weight-bold mb-0">
                                            {{-- @if ($cv > 0)
                                                <span class="text-success">Your project is under budget</span>
                                            @elseif ($ac == $pv)
                                                <span class="text-primary">Your project is on budget</span>
                                            @else
                                                <span class="text-danger">Your project is over budget</span>
                                            @endif --}}

                                            {{-- @if ($cv > 0)
                                                <span class="text-success">Your project is under budget</span>
                                            @elseif ($ac == $pv)
                                                <span class="text-primary">Your project is on budget</span>
                                            @elseif ($cv < 0)
                                                <span class="text-danger">Your project is over budget</span>
                                            @endif --}}

                                            {{-- 3rd time  --}}
                                            @if ($ac < $pv)
                                                <span class="text-success">Your project is under budget</span>
                                            @elseif ($ac == $pv)
                                                <span class="text-primary">Your project is on budget</span>
                                            @else
                                                <span class="text-danger">Your project is over budget</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-12">
                                        <span class="text-muted text-uppercase">Schedule :</span>
                                        {{-- <p class="h4 font-weight-bold mb-0">{{ ceil($scheduleVariance) }}</p> --}}
                                        <p class="h6 font-weight-bold mb-0">
                                            {{-- @if ($sv > 0)
                                                <span class="text-success">Your project is ahead of the schedule</span>
                                            @elseif ($sv == 0)
                                                <span class="text-primary">You project is on the schedule</span>
                                            @else
                                                <span class="text-danger">Your project is behind the schedule</span>
                                            @endif --}}

                                            @if ($sv > 0)
                                                <span class="text-success">Your project is ahead of the schedule</span>
                                            @elseif ($sv == 0)
                                                <span class="text-primary">You project is on the schedule</span>
                                            @elseif ($sv < 0)
                                                <span class="text-danger">Your project is behind the schedule</span>
                                            @endif
                                        </p>
                                    </div>
                                    {{-- @endif --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end row -->

            <div class="mb-5 mt-4">
                <canvas id="ev-chart" height="120"></canvas>
            </div>
        </div>
        <!-- container-fluid -->
    </div>
    <!-- content -->
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (() => {
            const labels = @json($chartLabels);
            const acData = @json($chartAC);
            const pvData = @json($chartPV);
            const evData = @json($chartEV);

            const ctx = document.getElementById('ev-chart').getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                            label: 'Actual Cost (AC)',
                            data: acData,
                            borderColor: 'rgba(0,128,0,0.9)',
                            borderDash: [6, 4],
                            borderWidth: 2,
                            tension: 0.2,
                            fill: false,
                        },
                        {
                            label: 'Planned Value (PV)',
                            data: pvData,
                            borderColor: 'rgba(200,0,0,0.9)',
                            borderWidth: 2,
                            tension: 0.2,
                            fill: false,
                        },
                        {
                            label: 'Earned Value (EV)',
                            data: evData,
                            borderColor: 'rgba(128,0,128,0.9)',
                            borderDash: [2, 6],
                            borderWidth: 2,
                            tension: 0.2,
                            fill: false,
                        },
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => 'TK ' + value.toLocaleString('en-BD')
                            },
                            title: {
                                display: true,
                                text: 'Amount (TK)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Month'
                            }
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: context => {
                                    const val = context.raw ?? 0;
                                    return `${context.dataset.label}: TK ${parseFloat(val).toLocaleString('en-BD')}`;
                                }
                            }
                        }
                    }
                }
            });
        })();
    </script>






    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Get data from Laravel backend
        const bac = {{ $bac }};
        const pv = {{ $pv }};
        const ac = {{ $ac }};
        const ev = {{ $ev }};
        const cv = {{ $cv }};
        const sv = {{ $sv }};

        // Calculate Estimate at Completion (EAC)
        const cpi = ev / ac; // Cost Performance Index
        const eac = cpi > 0 ? bac / cpi : bac * 1.2; // Fallback if CPI is 0

        // Create project timeline (simulated data based on your inputs)
        const months = ['Start', '1', '2', '3', '4', '5', '6', '7', '8'];
        const bacData = Array(months.length).fill(bac);
        const pvData = [0, bac * 0.1, bac * 0.25, bac * 0.45, bac * 0.7, bac * 0.9, bac, bac, bac];
        const acData = [0, ac * 0.15, ac * 0.35, ac * 0.6, ac * 0.85, ac, ac * 1.1, ac * 1.1, ac * 1.1];
        const evData = [0, ev * 0.1, ev * 0.25, ev * 0.4, ev * 0.65, ev, ev * 1.15, ev * 1.15, ev * 1.15];
        const eacData = [bac, bac, bac, bac, bac, bac, bac, eac, eac];

        // Get the canvas element
        const ctx = document.getElementById('evChart').getContext('2d');

        // Create the chart
        const evChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                        label: 'BAC (Budget at Completion)',
                        data: bacData,
                        borderColor: '#2a4d69',
                        backgroundColor: 'rgba(42, 77, 105, 0.1)',
                        borderWidth: 3,
                        borderDash: [5, 5],
                        fill: false,
                        pointRadius: 0,
                        pointHoverRadius: 5
                    },
                    {
                        label: 'PV (Planned Value)',
                        data: pvData,
                        borderColor: '#4b86b4',
                        backgroundColor: 'rgba(75, 134, 180, 0.1)',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.3,
                        pointRadius: 3
                    },
                    {
                        label: 'AC (Actual Cost)',
                        data: acData,
                        borderColor: '#ff6b6b',
                        backgroundColor: 'rgba(255, 107, 107, 0.1)',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.3,
                        pointRadius: 3
                    },
                    {
                        label: 'EV (Earned Value)',
                        data: evData,
                        borderColor: '#51cf66',
                        backgroundColor: 'rgba(81, 207, 102, 0.1)',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.3,
                        pointRadius: 3
                    },
                    {
                        label: 'EAC (Estimate at Completion)',
                        data: eacData,
                        borderColor: '#ffd166',
                        backgroundColor: 'rgba(255, 209, 102, 0.1)',
                        borderWidth: 3,
                        borderDash: [5, 5],
                        fill: false,
                        pointRadius: 0,
                        pointHoverRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cost ($)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        ticks: {
                            callback: function(value) {
                                return 'TK' + value.toLocaleString();
                            }
                        },
                        max: Math.max(bac, pv, ac, ev, eac) * 1.2
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Project Month',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += '$' + context.parsed.y.toLocaleString();
                                return label;
                            }
                        }
                    },
                    annotation: {
                        annotations: {
                            currentMonthLine: {
                                type: 'line',
                                mode: 'vertical',
                                scaleID: 'x',
                                value: 5,
                                borderColor: '#888',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                label: {
                                    display: true,
                                    content: 'Current Month',
                                    position: 'top',
                                    backgroundColor: 'rgba(136, 136, 136, 0.7)',
                                    color: 'white',
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            bacPoint: {
                                type: 'point',
                                xValue: months.length - 1,
                                yValue: bac,
                                pointStyle: 'circle',
                                radius: 6,
                                borderColor: '#2a4d69',
                                backgroundColor: '#2a4d69'
                            },
                            eacPoint: {
                                type: 'point',
                                xValue: months.length - 1,
                                yValue: eac,
                                pointStyle: 'circle',
                                radius: 6,
                                borderColor: '#ffd166',
                                backgroundColor: '#ffd166'
                            },
                            bacLabel: {
                                type: 'label',
                                xValue: months.length - 1,
                                yValue: bac,
                                content: 'BAC',
                                position: 'end',
                                backgroundColor: 'rgba(42, 77, 105, 0.7)',
                                color: 'white',
                                font: {
                                    size: 12
                                },
                                padding: 4
                            },
                            eacLabel: {
                                type: 'label',
                                xValue: months.length - 1,
                                yValue: eac,
                                content: 'EAC',
                                position: 'end',
                                backgroundColor: 'rgba(255, 209, 102, 0.7)',
                                color: '#333',
                                font: {
                                    size: 12
                                },
                                padding: 4
                            }
                        }
                    }
                }
            }
        });
    </script> --}}
@endpush
