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
                        <div class="card-header bg-info text-white text-center">
                            <h4 class="mb-0">Draft Budget for Project: {{ $project->name }}</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($monthly as $month => $amount)
                                        <tr>
                                            <td>{{ $month }}</td>
                                            <td>{{ number_format($amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-4">
                                <canvas id="budgetChart" height="100"></canvas>
                            </div>
                            <div class="mt-3">
                                <strong>Total Project Amount:</strong> {{ number_format($totalAmount, 2) }}<br>
                                <strong>Total Duration (months):</strong> {{ $totalDuration }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('budgetChart').getContext('2d');
            const months = @json(array_keys($monthly));
            const amounts = @json(array_values($monthly));
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Monthly Budget',
                        data: amounts,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
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
@endsection
