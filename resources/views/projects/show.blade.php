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
                        <a href="{{ route('projects.index') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-list"></i> View Projects
                        </a>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white text-center">
                            <h4 class="mb-0">Project: {{ $project->name }}</h4>
                        </div>
                        <div class="card-body">
                            <h5 class="mb-3">Tasks</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Task Name</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Amount</th>
                                        <th>Duration</th>
                                        <th>Precedence</th>
                                        <th>Dependencies</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($project->tasks as $task)
                                        <tr>
                                            <td>{{ $task->task_name }}</td>
                                            <td>{{ $task->start_date->format('F Y') }}</td>
                                            <td>{{ $task->end_date->format('F Y') }}</td>
                                            <td>{{ number_format($task->amount, 2) }}</td>
                                            <td>{{ $task->duration }}</td>
                                            <td>{{ $task->dependencies->count() }}</td>
                                            <td>
                                                <a href="{{ route('tasks.dependencies.index', $task) }}"
                                                    class="btn btn-sm btn-info">Manage Dependencies</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-4 text-center">
                                <a href="{{ route('budgets.draft', $project) }}" class="btn btn-warning btn-sm">View Draft
                                    Budget</a>
                                <a href="{{ route('budgets.final', $project) }}" class="btn btn-success btn-sm">View Final
                                    Budget (EVM)</a>
                                <a href="{{ route('projects.index') }}" class="btn btn-secondary btn-sm">Back to
                                    Projects</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
