@extends('backend.master')

@push('css')
    <style>
        .budget-table {
            font-size: 11px;
            overflow-x: auto;
        }
        .budget-table th,
        .budget-table td {
            text-align: center;
            vertical-align: middle;
            padding: 8px 4px;
            white-space: nowrap;
        }
        .budget-table th {
            background-color: #ffc107;
            color: #000;
            font-weight: bold;
        }
        .task-name {
            text-align: left !important;
            min-width: 150px;
        }
        .month-col {
            min-width: 70px;
        }
        .actual-cost-input {
            width: 70px;
            font-size: 10px;
            padding: 2px;
        }
        .total-row {
            background-color: #e0e0e0;
            font-weight: bold;
        }
    </style>
@endpush

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <h4>Draft Budget - {{ $project->name }}</h4>
                    </div>
                    <div class="col-sm-6 text-right">
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back to Project
                        </a>
                        <a href="{{ route('budgets.final', $project) }}" class="btn btn-success btn-sm">
                            <i class="fa fa-chart-line"></i> View Final Budget
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0 text-white">Draft Budget - Monthly Breakdown</h5>
                        </div>
                        <div class="card-body p-2">
                            <div class="table-responsive">
                                <table class="table table-bordered budget-table">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="task-name">WBS Activity</th>
                                            <th rowspan="2">Precedence</th>
                                            <th rowspan="2">Duration<br>(months)</th>
                                            <th rowspan="2">From</th>
                                            <th rowspan="2">To</th>
                                            <th rowspan="2">WBS Item wise<br>Budget</th>
                                            @foreach($months as $month)
                                                <th class="month-col">{{ $month['label'] }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $monthlyTotals = [];
                                        @endphp
                                        @foreach($tasks as $taskData)
                                            @php
                                                $task = $taskData['task'];
                                                $precedence = $task->dependency ? $task->dependency->type : '-';
                                            @endphp
                                            <tr>
                                                <td class="task-name">{{ $task->task_name }}</td>
                                                <td>{{ $precedence }}</td>
                                                <td>{{ $task->duration }}</td>
                                                <td>{{ $task->start_date->format('M') }}</td>
                                                <td>{{ $task->end_date->format('M') }}</td>
                                                <td>{{ number_format($taskData['total'], 2) }}</td>
                                                @foreach($months as $month)
                                                    @php
                                                        $amount = $taskData['monthly_budget'][$month['key']] ?? 0;
                                                        if ($amount > 0) {
                                                            if (!isset($monthlyTotals[$month['key']])) {
                                                                $monthlyTotals[$month['key']] = 0;
                                                            }
                                                            $monthlyTotals[$month['key']] += $amount;
                                                        }
                                                    @endphp
                                                    <td class="month-col">
                                                        @if($amount > 0)
                                                            {{ number_format($amount, 0) }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                        <tr class="total-row">
                                            <td colspan="5">Total</td>
                                            <td>{{ number_format($totalBudget, 2) }}</td>
                                            @foreach($months as $month)
                                                <td>{{ number_format($monthlyTotals[$month['key']] ?? 0, 0) }}</td>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4">
                                <div class="mt-4">
                                    <h6>Monthly Budget Input (Actual Costs & Earned Value %)</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" style="font-size: 13px;">
                                            <thead>
                                                <tr>
                                                    <th style="width: 70%;">WBS Activity</th>
                                                    <th style="width: 30%; text-align: center;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($tasks as $taskData)
                                                    @php 
                                                        $task = $taskData['task']; 
                                                        $existingRecords = $task->monthlyActualCosts->keyBy('month');
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $task->task_name }}</strong>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="{{ route('tasks.budget.input', $task) }}" class="btn btn-info btn-sm">
                                                                <i class="fa fa-edit"></i> Update Input
                                                            </a>
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
                </div>
            </div>
        </div>
    </div>
@endsection


