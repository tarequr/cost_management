@extends('backend.master')

@push('css')
    <style>
        .evm-table {
            font-size: 11px;
        }
        .evm-table th,
        .evm-table td {
            text-align: center;
            vertical-align: middle;
            padding: 6px 4px;
        }
        .evm-table th {
            background-color: #ffc107;
            color: #000;
            font-weight: bold;
        }
        .metrics-card {
            border-left: 4px solid #28a745;
        }
        .metric-value {
            font-size: 24px;
            font-weight: bold;
        }
        .metric-label {
            font-size: 12px;
            color: #666;
        }
        .status-good {
            background-color: #d4edda;
            color: #155724;
        }
        .status-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-critical {
            background-color: #f8d7da;
            color: #721c24;
        }
        .monthly-table {
            font-size: 10px;
        }
        .monthly-table th,
        .monthly-table td {
            padding: 4px 2px;
        }
    </style>
@endpush

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <h4>Final Budget (EVM) - {{ $project->name }}</h4>
                    </div>
                    <div class="col-sm-6 text-right">
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back to Project
                        </a>
                        <a href="{{ route('budgets.draft', $project) }}" class="btn btn-warning btn-sm">
                            <i class="fa fa-edit"></i> Edit Draft Budget
                        </a>
                    </div>
                </div>
            </div>

            <!-- EVM Metrics Summary -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card metrics-card">
                        <div class="card-body text-center">
                            <div class="metric-label">Budget at Completion (BAC)</div>
                            <div class="metric-value text-primary">{{ number_format($evm['BAC'], 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metrics-card">
                        <div class="card-body text-center">
                            <div class="metric-label">Planned Value (PV)</div>
                            <div class="metric-value text-info">{{ number_format($evm['PV'], 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metrics-card">
                        <div class="card-body text-center">
                            <div class="metric-label">Actual Cost (AC)</div>
                            <div class="metric-value text-danger">{{ number_format($evm['AC'], 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metrics-card">
                        <div class="card-body text-center">
                            <div class="metric-label">Earned Value (EV)</div>
                            <div class="metric-value text-success">{{ number_format($evm['EV'], 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Indices -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-{{ $spiStatus['color'] }} text-white">
                            <strong>Schedule Performance Index (SPI)</strong>
                            <i class="fa fa-{{ $spiStatus['icon'] }} float-right"></i>
                        </div>
                        <div class="card-body text-center">
                            <h2 class="text-{{ $spiStatus['color'] }}">{{ number_format($evm['SPI'], 2) }}</h2>
                            <p class="mb-0">
                                @if($evm['SPI'] >= 1.0)
                                    Ahead of Schedule
                                @elseif($evm['SPI'] >= 0.8)
                                    Slightly Behind Schedule
                                @else
                                    Significantly Behind Schedule
                                @endif
                            </p>
                            <small class="text-muted">Schedule Variance: {{ number_format($evm['SV'], 2) }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-{{ $cpiStatus['color'] }} text-white">
                            <strong>Cost Performance Index (CPI)</strong>
                            <i class="fa fa-{{ $cpiStatus['icon'] }} float-right"></i>
                        </div>
                        <div class="card-body text-center">
                            <h2 class="text-{{ $cpiStatus['color'] }}">{{ number_format($evm['CPI'], 2) }}</h2>
                            <p class="mb-0">
                                @if($evm['CPI'] >= 1.0)
                                    Under Budget
                                @elseif($evm['CPI'] >= 0.8)
                                    Slightly Over Budget
                                @else
                                    Significantly Over Budget
                                @endif
                            </p>
                            <small class="text-muted">Cost Variance: {{ number_format($evm['CV'], 2) }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Forecasts -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="metric-label">Estimate at Completion (EAC)</div>
                            <div class="metric-value text-warning">{{ number_format($evm['EAC'], 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="metric-label">Estimate to Complete (ETC)</div>
                            <div class="metric-value text-secondary">{{ number_format($evm['ETC'], 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="metric-label">Variance at Completion (VAC)</div>
                            @php $VAC = $evm['BAC'] - $evm['EAC']; @endphp
                            <div class="metric-value {{ $VAC >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($VAC, 0) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly EVM Data Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0 text-white">Monthly EVM Breakdown</h5>
                        </div>
                        <div class="card-body p-2">
                            <div class="table-responsive">
                                <table class="table table-bordered monthly-table">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>PV (Cumulative)</th>
                                            <th>AC (Cumulative)</th>
                                            <th>EV (Cumulative)</th>
                                            <th>SV</th>
                                            <th>CV</th>
                                            <th>SPI</th>
                                            <th>CPI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($monthlyData as $data)
                                            <tr>
                                                <td>{{ $data['month'] }}</td>
                                                <td>{{ number_format($data['PV'], 0) }}</td>
                                                <td>{{ number_format($data['AC'], 0) }}</td>
                                                <td>{{ number_format($data['EV'], 0) }}</td>
                                                <td class="{{ $data['SV'] >= 0 ? 'status-good' : 'status-critical' }}">
                                                    {{ number_format($data['SV'], 0) }}
                                                </td>
                                                <td class="{{ $data['CV'] >= 0 ? 'status-good' : 'status-critical' }}">
                                                    {{ number_format($data['CV'], 0) }}
                                                </td>
                                                <td class="{{ $data['SPI'] >= 1.0 ? 'status-good' : ($data['SPI'] >= 0.8 ? 'status-warning' : 'status-critical') }}">
                                                    {{ number_format($data['SPI'], 2) }}
                                                </td>
                                                <td class="{{ $data['CPI'] >= 1.0 ? 'status-good' : ($data['CPI'] >= 0.8 ? 'status-warning' : 'status-critical') }}">
                                                    {{ number_format($data['CPI'], 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- EVM Chart -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">EVM Trend Chart</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="evmChart" height="80"></canvas>
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
        const ctx = document.getElementById('evmChart').getContext('2d');
        const monthlyData = @json($monthlyData);
        
        const labels = monthlyData.map(d => d.month);
        const pvData = monthlyData.map(d => d.PV);
        const acData = monthlyData.map(d => d.AC);
        const evData = monthlyData.map(d => d.EV);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Planned Value (PV)',
                        data: pvData,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        borderWidth: 2,
                        fill: true
                    },
                    {
                        label: 'Actual Cost (AC)',
                        data: acData,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        borderWidth: 2,
                        fill: true
                    },
                    {
                        label: 'Earned Value (EV)',
                        data: evData,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        borderWidth: 2,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush
