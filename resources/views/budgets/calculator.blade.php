@extends('backend.master')

@section('content')
<div class="content">
    <div class="container-fluid pt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title text-primary mb-0 text-capitalize font-weight-bold">Budget Calculator: {{ $project->name }}</h4>
                                <a href="{{ route('budgets.final', $project->id) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>

                            <div class="mb-4 d-flex justify-content-between">
                                <div>
                                    <span class="text-muted text-uppercase small d-block">Project Duration</span>
                                    <p class="font-weight-bold mb-0 text-uppercase">{{ $fromMonth }} - {{ $toMonth }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-muted text-uppercase small d-block">Reporting Month</span>
                                    <p class="font-weight-bold mb-0 text-primary">{{ \Carbon\Carbon::parse($selectedMonth)->format('F Y') }}</p>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-6">
                                    <div class="card bg-primary p-3 mb-3 border-0">
                                        <span class="text-white-50 text-uppercase small">Estimate Amount (PV)</span>
                                        <p class="h4 font-weight-bold mb-0 text-white">{{ number_format($pv, 0) }}</p>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="card bg-info p-3 mb-3 border-0">
                                        <span class="text-white-50 text-uppercase small">Actual Cost (AC)</span>
                                        <p class="h4 font-weight-bold mb-0 text-white">{{ number_format($ac, 0) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-6">
                                    <div class="card bg-warning p-3 mb-3 border-0">
                                        <span class="text-white-50 text-uppercase small">Earned Value (EV)</span>
                                        <p class="h4 font-weight-bold mb-0 text-white">{{ number_format($ev, 2) }}</p>
                                        <small class="text-white-50">({{ $progress }}% of AC)</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-secondary p-3 mb-3 border-0">
                                        <span class="text-white-50 text-uppercase small">Schedule Variance (SV)</span>
                                        <p class="h4 font-weight-bold mb-0 text-white">{{ number_format($sv, 2) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card bg-white p-3 border">
                                        <h6 class="font-weight-bold text-muted text-uppercase small mb-3">S-Curve Report (Cumulative PV, AC, EV)</h6>
                                        <div style="height: 350px;">
                                            <canvas id="sCurveChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card bg-white p-3 border">
                                        <h6 class="font-weight-bold text-muted text-uppercase small mb-3">Monthly Variances (CV & SV)</h6>
                                        <div style="height: 250px;">
                                            <canvas id="varianceChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-5 mt-4">
                                <div class="col-md-6">
                                    <h6 class="font-weight-bold text-muted text-uppercase small mb-3">Current Performance</h6>
                                    <table class="table table-bordered table-sm bg-light">
                                        <thead class="bg-secondary text-white">
                                            <tr>
                                                <th>Metric</th>
                                                <th>Value</th>
                                                <th>Formula</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Cost Variance (CV)</td>
                                                <td class="font-weight-bold {{ $cv < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($cv, 2) }}</td>
                                                <td class="small">EV - AC</td>
                                            </tr>
                                            <tr>
                                                <td>Cost Performance Index (CPI)</td>
                                                <td class="font-weight-bold {{ $cpi < 1 ? 'text-danger' : 'text-success' }}">{{ number_format($cpi, 2) }}</td>
                                                <td class="small">EV / AC</td>
                                            </tr>
                                            <tr>
                                                <td>Schedule Variance (SV)</td>
                                                <td class="font-weight-bold {{ $sv < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($sv, 2) }}</td>
                                                <td class="small">EV - PV</td>
                                            </tr>
                                            <tr>
                                                <td>Schedule Performance Index (SPI)</td>
                                                <td class="font-weight-bold {{ $spi < 1 ? 'text-danger' : 'text-success' }}">{{ number_format($spi, 2) }}</td>
                                                <td class="small">EV / PV</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="font-weight-bold text-muted text-uppercase small mb-3">Project Forecast</h6>
                                    <table class="table table-bordered table-sm bg-light">
                                        <thead class="bg-dark text-white">
                                            <tr>
                                                <th>Metric</th>
                                                <th>Value</th>
                                                <th>Formula</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Budget At Completion (BAC)</td>
                                                <td class="font-weight-bold text-primary">{{ number_format($bac, 2) }}</td>
                                                <td class="small">Sum of all tasks</td>
                                            </tr>
                                            <tr>
                                                <td>Estimate At Completion (EAC)</td>
                                                <td class="font-weight-bold text-danger">{{ number_format($eac, 2) }}</td>
                                                <td class="small">BAC / CPI</td>
                                            </tr>
                                            <tr>
                                                <td>Planned Duration</td>
                                                <td class="font-weight-bold text-primary">{{ number_format(count($chartLabels)) }} Months</td>
                                                <td class="small">Original Timeline</td>
                                            </tr>
                                            <tr>
                                                <td>Estimated Duration</td>
                                                <td class="font-weight-bold text-danger">{{ number_format($est_duration, 2) }} Months</td>
                                                <td class="small">Timeline / SPI</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row mb-4 mt-2 border-top pt-4">
                                <div class="col-12 mb-3">
                                    <span class="text-muted text-uppercase small d-block mb-1">Budget Status:</span>
                                    <p class="h6 font-weight-bold mb-0">
                                        @if ($ac < $pv)
                                            <span class="text-success"><i class="fa fa-check-circle mr-1"></i> Your project is under budget</span>
                                        @elseif ($ac == $pv)
                                            <span class="text-primary"><i class="fa fa-info-circle mr-1"></i> Your project is on budget</span>
                                        @else
                                            <span class="text-danger"><i class="fa fa-exclamation-triangle mr-1"></i> Your project is over budget</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-12">
                                    <span class="text-muted text-uppercase small d-block mb-1">Schedule Status:</span>
                                    <p class="h6 font-weight-bold mb-0">
                                        @if ($sv > 0)
                                            <span class="text-success"><i class="fa fa-arrow-up mr-1"></i> Your project is ahead of the schedule</span>
                                        @elseif ($sv == 0)
                                            <span class="text-primary"><i class="fa fa-check mr-1"></i> Your project is on the schedule</span>
                                        @elseif ($sv < 0)
                                            <span class="text-danger"><i class="fa fa-arrow-down mr-1"></i> Your project is behind the schedule</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('sCurveChart').getContext('2d');
        
        const chartData = {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [
                {
                    label: 'Planned Value (PV)',
                    data: {!! json_encode($chartPV) !!},
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Actual Cost (AC)',
                    data: {!! json_encode($chartAC) !!},
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    borderWidth: 3,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Earned Value (EV)',
                    data: {!! json_encode($chartEV) !!},
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }
            ]
        };

        const config = {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('en-US').format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                return new Intl.NumberFormat('en-US').format(value);
                            }
                        }
                    }
                }
            }
        };

        new Chart(ctx, config);

        // Variance Chart
        const ctxVar = document.getElementById('varianceChart').getContext('2d');
        new Chart(ctxVar, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Cost Variance (CV)',
                        data: {!! json_encode($chartCV) !!},
                        backgroundColor: 'rgba(255, 159, 64, 0.7)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Schedule Variance (SV)',
                        data: {!! json_encode($chartSV) !!},
                        backgroundColor: 'rgba(153, 102, 255, 0.7)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('en-US').format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('en-US').format(value);
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
