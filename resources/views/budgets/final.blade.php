@extends('backend.master')

@push('css')
    <style>
        .table th,
        .table td {
            text-align: center;
        }

        .btn {
            border-radius: 5px;
        }
    </style>
@endpush

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-sm-12 text-right">
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back to Project
                        </a>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-success text-white text-center">
                            <h4 class="mb-0">Final Budget (EVM) for Project: {{ $project->name }}</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Metric</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>PV (Planned Value)</td>
                                        <td>{{ number_format($evm['pv'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>AC (Actual Cost)</td>
                                        <td>{{ number_format($evm['ac'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>EV (Earned Value)</td>
                                        <td>{{ number_format($evm['ev'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>SPI (Schedule Performance Index)</td>
                                        <td>{{ number_format($evm['spi'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>CPI (Cost Performance Index)</td>
                                        <td>{{ number_format($evm['cpi'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>CV (Cost Variance)</td>
                                        <td>{{ number_format($evm['cv'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>SV (Schedule Variance)</td>
                                        <td>{{ number_format($evm['sv'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>BAC (Budget at Completion)</td>
                                        <td>{{ number_format($evm['bac'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>ETC (Estimate to Complete)</td>
                                        <td>{{ number_format($evm['etc'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>EAC (Estimate at Completion)</td>
                                        <td>{{ number_format($evm['eac'], 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="mt-4">
                                <canvas id="evmChart" height="100"></canvas>
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
        const ctx = document.getElementById('evmChart').getContext('2d');
        const labels = ['PV', 'AC', 'EV', 'SPI', 'CPI', 'CV', 'SV', 'BAC', 'ETC', 'EAC'];
        const data = [
            {{ $evm['pv'] ?? 0 }},
            {{ $evm['ac'] ?? 0 }},
            {{ $evm['ev'] ?? 0 }},
            {{ $evm['spi'] ?? 0 }},
            {{ $evm['cpi'] ?? 0 }},
            {{ $evm['cv'] ?? 0 }},
            {{ $evm['sv'] ?? 0 }},
            {{ $evm['bac'] ?? 0 }},
            {{ $evm['etc'] ?? 0 }},
            {{ $evm['eac'] ?? 0 }}
        ];
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'EVM Metrics',
                    data: data,
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endpush
