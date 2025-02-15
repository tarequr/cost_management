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
    </style>
@endpush

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-sm-12 text-right">
                        <a href="{{ route('budget-estimate.index') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-list"></i> View Budget Estimates
                        </a>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white text-center">
                            <h4 class="mb-0">Create Budget Estimate</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('budget-estimate.store') }}" enctype="multipart/form-data" id="budgetForm">
                                @csrf

                                <div class="form-group">
                                    <label for="project_name">Project Name <sup class="text-danger">*</sup></label>
                                    <input type="text" id="project_name" name="project_name"
                                        class="form-control @error('project_name') is-invalid @enderror"
                                        value="{{ old('project_name') }}" placeholder="Enter project name" autocomplete="off" required>
                                    @error('project_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="client_name">Client Name</label>
                                    <input type="text" id="client_name" name="client_name" class="form-control"
                                        value="{{ old('client_name') }}" placeholder="Enter client name" autocomplete="off">
                                </div>

                                <div class="form-group">
                                    <label for="budget_amount">Planned Budget <sup class="text-danger">*</sup></label>
                                    <input type="number" id="budget_amount" name="budget_amount" class="form-control"
                                        value="{{ old('budget_amount') }}" placeholder="Enter planned budget" autocomplete="off" required>
                                </div>

                                <div class="form-group">
                                    <label for="start_date">Start Date <sup class="text-danger">*</sup></label>
                                    <input type="date" id="start_date" name="start_date"
                                        class="form-control @error('start_date') is-invalid @enderror"
                                        value="{{ old('start_date') }}" autocomplete="off">
                                    @error('start_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="end_date">End Date <sup class="text-danger">*</sup></label>
                                    <input type="date" id="end_date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" autocomplete="off" disabled>
                                    @error('end_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group text-center">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Dropify
            $('.dropify').dropify();

            // Start and End Date Validation
            $('#start_date').on('change', function() {
                const startDate = $(this).val();
                if (startDate) {
                    $('#end_date').prop('disabled', false).attr('min', startDate);
                } else {
                    $('#end_date').prop('disabled', true).val('');
                }
            });

            $('#end_date').on('change', function() {
                const startDate = $('#start_date').val();
                const endDate = $(this).val();

                // if (new Date(endDate) < new Date(startDate)) {
                //     alert('End Date cannot be earlier than Start Date.');
                //     $(this).val('');
                // }
            });

            // Form Validation on Submit
            $('#budgetForm').on('submit', function(event) {
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();

                if (!startDate) {
                    alert('Please select a Start Date.');
                    event.preventDefault();
                } else if (!endDate) {
                    alert('Please select an End Date.');
                    event.preventDefault();
                } else if (new Date(endDate) < new Date(startDate)) {
                    alert('End Date cannot be earlier than Start Date.');
                    event.preventDefault();
                }
            });
        });
    </script>
@endpush
