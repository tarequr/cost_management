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
                <div class="col-lg-10">
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
                                    <div class="task-row border p-2 mb-3 rounded shadow-sm">
                                        <div class="row w-100 m-0">
                                            <div class="col-md-4 mb-2">
                                                <input type="text" name="tasks[0][task_name]" class="form-control task-name-input"
                                                    placeholder="Task Name" required>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <input type="month" name="tasks[0][start_date]" class="form-control start-date-input" required>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <input type="number" name="tasks[0][duration]" class="form-control duration-input"
                                                    placeholder="Duration (months)" min="1" required>
                                                <small class="text-muted end-date-label"></small>
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <input type="number" name="tasks[0][cost]" class="form-control"
                                                    placeholder="Cost" required>
                                            </div>
                                            <input type="hidden" name="tasks[0][has_precedence]" value="no">
                                        </div>
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
            let taskIndex = $('#tasksRepeater .task-row').length;

            function updateAllDependsOnDropdowns() {
                let taskRows = $('#tasksRepeater .task-row');
                taskRows.each(function(index) {
                    let select = $(this).find('.depends-on-select');
                    let currentValue = select.val();
                    select.empty().append('<option value="">Select Task</option>');
                    
                    // Add all PREVIOUS tasks as options
                    for (let i = 0; i < index; i++) {
                        let prevTaskName = taskRows.eq(i).find('.task-name-input').val() || `Task ${i+1}`;
                        select.append(`<option value="${i}">${prevTaskName}</option>`);
                    }
                    
                    if (currentValue && currentValue < index) {
                        select.val(currentValue);
                    }
                });
            }

            $('#addTask').on('click', function() {
                let range = getNextMonthMinMax();
                let newRow = `
                    <div class="task-row border p-2 mb-3 rounded shadow-sm">
                        <div class="row w-100 m-0">
                            <div class="col-md-4 mb-2">
                                <input type="text" name="tasks[${taskIndex}][task_name]" class="form-control task-name-input" placeholder="Task Name" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <input type="month" name="tasks[${taskIndex}][start_date]" class="form-control start-date-input" min="${range.min}" max="${range.max}" required>
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="number" name="tasks[${taskIndex}][duration]" class="form-control duration-input" placeholder="Duration (months)" min="1" required>
                                <small class="text-muted end-date-label"></small>
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="number" name="tasks[${taskIndex}][cost]" class="form-control" placeholder="Cost" required>
                            </div>
                            <div class="col-md-1 mb-2 text-right">
                                <button type="button" class="btn btn-danger btn-sm remove-task">Remove</button>
                            </div>
                            <div class="col-md-4 mb-2">
                                <select name="tasks[${taskIndex}][has_precedence]" class="form-control precedence-toggle">
                                    <option value="no">Precedence: No</option>
                                    <option value="yes">Precedence: Yes</option>
                                </select>
                            </div>
                            <div class="col-md-8 mb-2 precedence-fields d-none">
                                <div class="row">
                                    <div class="col-md-6">
                                        <select name="tasks[${taskIndex}][depends_on]" class="form-control depends-on-select">
                                            <option value="">Select Task</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <select name="tasks[${taskIndex}][precedence_type]" class="form-control">
                                            <option value="FF">Finish-to-Finish (FF)</option>
                                            <option value="SS">Start-to-Start (SS)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#tasksRepeater').append(newRow);
                taskIndex++;
                updateAllDependsOnDropdowns();
            });

            $(document).on('click', '.remove-task', function() {
                $(this).closest('.task-row').remove();
                updateAllDependsOnDropdowns();
            });

            $(document).on('change', '.precedence-toggle', function() {
                let fields = $(this).closest('.task-row').find('.precedence-fields');
                if ($(this).val() === 'yes') {
                    fields.removeClass('d-none');
                    fields.find('select').attr('required', true);
                } else {
                    fields.addClass('d-none');
                    fields.find('select').attr('required', false);
                }
            });

            function updateTaskDates() {
                let taskRows = $('#tasksRepeater .task-row');
                taskRows.each(function(index) {
                    let row = $(this);
                    let hasPrecedence = row.find('.precedence-toggle').val() === 'yes';
                    let dependsOnIndex = row.find('.depends-on-select').val();
                    let precedenceType = row.find('select[name*="[precedence_type]"]').val();
                    let startDateInput = row.find('.start-date-input');
                    let durationInput = row.find('.duration-input');
                    let label = row.find('.end-date-label');

                    if (hasPrecedence && dependsOnIndex !== "" && dependsOnIndex < index) {
                        let parentRow = taskRows.eq(dependsOnIndex);
                        let parentStart = parentRow.find('.start-date-input').val();
                        let parentDuration = parseInt(parentRow.find('.duration-input').val()) || 1;

                        if (parentStart) {
                            let startDate;
                            if (precedenceType === 'SS') {
                                // Start-to-Start: Matches parent start
                                startDate = parentStart;
                            } else if (precedenceType === 'FF') {
                                // Finish-to-Finish: Matches parent end
                                let parentDate = new Date(parentStart + '-01');
                                parentDate.setMonth(parentDate.getMonth() + parentDuration - 1);
                                
                                let taskDuration = parseInt(durationInput.val()) || 1;
                                let taskStartDate = new Date(parentDate);
                                taskStartDate.setMonth(taskStartDate.getMonth() - taskDuration + 1);
                                
                                let year = taskStartDate.getFullYear();
                                let month = (taskStartDate.getMonth() + 1).toString().padStart(2, '0');
                                startDate = `${year}-${month}`;
                            }
                            
                            startDateInput.val(startDate).attr('readonly', true).css('background-color', '#e9ecef');
                        }
                    } else {
                        startDateInput.attr('readonly', false).css('background-color', '');
                    }

                    // Update "Ends" label
                    let start = startDateInput.val();
                    let duration = parseInt(durationInput.val());
                    if (start && duration > 0) {
                        let date = new Date(start + '-01');
                        date.setMonth(date.getMonth() + duration - 1);
                        let months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                        label.text('Ends: ' + months[date.getMonth()] + ' ' + date.getFullYear());
                    } else {
                        label.text('');
                    }
                });
            }

            $(document).on('change', '.start-date-input, .duration-input, .precedence-toggle, .depends-on-select, select[name*="[precedence_type]"]', function() {
                updateTaskDates();
            });

            $(document).on('input', '.task-name-input', function() {
                updateAllDependsOnDropdowns();
            });

            function getNextMonthMinMax() {
                let today = new Date();
                let year = today.getFullYear();
                let month = today.getMonth() + 2;
                if (month > 12) {
                    month = 1;
                    year++;
                }
                let min = `${year}-${month.toString().padStart(2, '0')}`;
                let maxYear = year + 5;
                let max = `${maxYear}-12`;
                return { min, max };
            }
            
            let range = getNextMonthMinMax();
            $('#tasksRepeater .task-row input[type="month"]').attr('min', range.min).attr('max', range.max);
            updateAllDependsOnDropdowns();
        });
    </script>
@endpush
