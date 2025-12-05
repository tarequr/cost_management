@extends('backend.master')

@push('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css" rel="stylesheet">
    <style>
        .form-group label {
            font-weight: bold;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .text-danger {
            font-size: 0.875rem;
        }

        .btn {
            border-radius: 5px;
        }

        .task-row {
            display: flex;
            gap: 10px;
            margin-bottom: 5px;
        }

        .remove-task {
            background: #e74c3c;
            color: #fff;
            border: none;
            border-radius: 3px;
            padding: 0 10px;
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
                            <h4 class="mb-0">Create Project</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data"
                                id="projectForm">
                                @csrf
                                <div class="form-group">
                                    <label for="name">Project Name <sup class="text-danger">*</sup></label>
                                    <input type="text" id="name" name="name"
                                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                        placeholder="Enter project name" autocomplete="off" required>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <h5 class="mt-4 mb-2">Tasks</h5>
                                <div id="tasksRepeater">
                                    <div class="task-row">
                                        <input type="text" name="tasks[0][task_name]" class="form-control"
                                            placeholder="Task Name" required>
                                        <input type="date" name="tasks[0][start_date]" class="form-control" required>
                                        <input type="date" name="tasks[0][end_date]" class="form-control" required>
                                        <input type="number" name="tasks[0][amount]" class="form-control"
                                            placeholder="Amount" required>
                                        <button type="button" class="remove-task">Remove</button>
                                    </div>
                                </div>
                                <button type="button" id="addTask" class="btn btn-secondary mt-2">Add Task</button>
                                <div class="form-group text-center mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Save
                                    </button>
                                    <button type="reset" class="btn btn-secondary">
                                        <i class="fa fa-undo"></i> Reset
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script>
        $(document).ready(function() {
            // $('.dropify').dropify();
            let taskIndex = $('#tasksRepeater .task-row').length;
            $('#addTask').on('click', function() {
                $('#tasksRepeater').append(`
                    <div class=\"task-row\">
                        <input type=\"text\" name=\"tasks[${taskIndex}][task_name]\" class=\"form-control\" placeholder=\"Task Name\" required>
                        <input type=\"date\" name=\"tasks[${taskIndex}][start_date]\" class=\"form-control\" required>
                        <input type=\"date\" name=\"tasks[${taskIndex}][end_date]\" class=\"form-control\" required>
                        <input type=\"number\" name=\"tasks[${taskIndex}][amount]\" class=\"form-control\" placeholder=\"Amount\" required>
                        <button type=\"button\" class=\"remove-task\">Remove</button>
                    </div>
                `);
                taskIndex++;
            });
            $('#tasksRepeater').on('click', '.remove-task', function() {
                $(this).closest('.task-row').remove();
            });
        });
    </script>
@endpush
