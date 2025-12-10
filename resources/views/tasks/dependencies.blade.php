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
                        <a href="{{ route('projects.show', $task->project_id) }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back to Project
                        </a>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-warning text-white text-center">
                            <h4 class="mb-0">Set Precedence for Task: {{ $task->task_name }}</h4>
                        </div>
                        <div class="card-body">
                            <form id="dependencyForm" method="POST"
                                action="{{ route('tasks.dependencies.store', $task) }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="depends_on_task_id" class="form-label">Depends On</label>
                                    <select name="depends_on_task_id" id="depends_on_task_id" class="form-control" required>
                                        <option value="">Select Task</option>
                                        @foreach ($tasks as $dep)
                                            <option value="{{ $dep->id }}"
                                                @if ($dependencies->where('depends_on_task_id', $dep->id)->count()) selected @endif>
                                                {{ $dep->task_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="type" class="form-label">Dependency Type (Precedence)</label>
                                    <select name="type" id="type" class="form-control" required>
                                        <option value="FF">F-F (Finish-to-Finish) - Both tasks finish together</option>
                                        <option value="SS">S-S (Start-to-Start) - Both tasks start together</option>
                                    </select>
                                    <small class="text-muted">
                                        <strong>Note:</strong> Setting a precedence will automatically update the task dates.
                                    </small>
                                </div>
                                <button type="submit" class="btn btn-primary">Save Dependency</button>
                            </form>
                            @if (session('success'))
                                <div class="alert alert-success mt-3">{{ session('success') }}</div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger mt-3">
                                    @foreach ($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
