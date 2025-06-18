@extends('backend.master')

@push('css')
@endpush

@section('content')
    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <h4 class="page-title">Budget Calculation</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class=" float-right">
                            <a href="{{ route('budget-estimate.index') }}" class="btn btn-sm btn-danger">
                                <i class="fa fa-arrow-left"></i>
                                Back
                            </a>

                            <a href="javascript:void(0);" class="btn btn-sm btn-warning" onclick="location.reload()">
                                <i class="fa fa-rotate-right"></i>
                                Refresh
                            </a>
                        </ol>
                    </div>
                </div> <!-- end row -->
            </div>
            <!-- end page-title -->

            <div class="row">
                <div class="col-12">
                    <div class="p-4 bg-white shadow-sm border rounded mb-4">
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">
                            <div>
                                <h4 class="mb-1">{{ $budgetEstimate->project_name }}</h4>
                                <p class="mb-0 text-muted"><strong>{{ $budgetEstimate->client_name ?? '' }}</strong></p>
                            </div>

                            <div class="mt-3 mt-md-0">
                                <div>
                                    <a href="javascript:void(0);" class="btn btn-success" data-toggle="modal"
                                        data-target="#addModal">
                                        <i class="fa fa-plus-circle"></i> Add Task
                                    </a>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('budget.filter') }}" method="POST" class="row g-2 align-items-end">
                            @csrf

                            <input type="hidden" name="budget_estimate_id" value="{{ $budgetEstimate->id }}">


                            <div class="col-md-3 pr-0">
                                <label for="from_month" class="form-label">From Month <sup
                                        class="text-danger">*</sup></label>
                                <input type="month" class="form-control" name="from_month" id="from_month" required>
                            </div>

                            <div class="col-md-3 pr-0">
                                <label for="to_month" class="form-label">To Month <sup class="text-danger">*</sup></label>
                                <input type="month" class="form-control" name="to_month" id="to_month" required disabled>
                            </div>


                            <div class="col-md-3 pr-0">
                                <label for="expected_amount" class="form-label">Estimate Amount <sup
                                        class="text-danger">*</sup></label>
                                <input type="number" class="form-control" name="expected_amount" id="expected_amount"
                                    placeholder="Estimate Amount" required>
                            </div>

                            <div class="col-md-3 d-grid">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fa fa-search me-1"></i> Search
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="card m-b-30">
                        <div class="card-body">
                            <table class="table table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">Task Name</th>
                                        <th class="text-center">Form Date</th>
                                        <th class="text-center">To Date</th>
                                        <th class="text-center">Rate</th>
                                        {{-- @if (!$budgetEstimate->is_project_finalized) --}}
                                        <th class="text-center">Action</th>
                                        {{-- @endif --}}
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach ($budgetCalculators as $budgetCalculator)
                                        <tr>
                                            <td>{{ $budgetCalculator->task_name }}</td>
                                            <td>
                                                <span
                                                    class="text-uppercase">{{ date('d/m/Y', strtotime($budgetCalculator->from_date)) }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-uppercase">{{ date('d/m/Y', strtotime($budgetCalculator->to_date)) }}</span>
                                            </td>
                                            <td>
                                                {{ $budgetCalculator->fixed_rate }}
                                            </td>
                                            <td>
                                                <form action="{{ route('budget-calculator.delete') }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')

                                                    <input type="hidden" name="id"
                                                        value="{{ $budgetCalculator->id }}">
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure you want to delete?')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            {{-- @endif --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div>
        <!-- container-fluid -->
    </div>
    <!-- content -->

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" data-backdrop="static" role="dialog"
        aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add a Task</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form method="POST" action="{{ route('budget-calculator.store') }}" id="myform">
                    @csrf

                    <input type="hidden" name="budget_estimate_id" value="{{ $budgetEstimate->id }}">

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="task_name" class="col-form-label">Task Name:</label>
                            <input type="text" class="form-control" name="task_name" id="task_name"
                                placeholder="Enter task name" required>
                        </div>

                        <div class="form-group">
                            <label for="from_date" class="col-form-label">From Date:</label>
                            <input type="date" class="form-control" name="from_date" id="from_date"
                                min="{{ \Carbon\Carbon::parse($budgetEstimate->start_date)->format('Y-m-d') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="to_date" class="col-form-label">To Date:</label>
                            <input type="date" class="form-control" name="to_date" id="to_date"
                                min="{{ \Carbon\Carbon::parse($budgetEstimate->start_date)->format('Y-m-d') }}" required>
                        </div>

                        {{-- <div class="form-group">
                            <label class="col-form-label">Rate Type:</label>
                            <div class="form-check">
                                <input type="radio" class="form-check-input text-uppercase" id="fixedRateType" name="rate" value="fixed">Fixed
                                <label class="form-check-label" for="fixedRateType"></label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input text-uppercase" id="hourlyRateType" name="rate" value="hourly">Hourly
                                <label class="form-check-label" for="hourlyRateType"></label>
                            </div>
                        </div> --}}

                        <!-- Fixed Rate Field -->
                        <div class="form-group" id="fixedRateGroup">
                            <label for="fixed_rate" class="col-form-label">Fixed Rate:</label>
                            <input type="number" class="form-control" name="fixed_rate" id="fixed_rate"
                                placeholder="Enter fixed rate">
                        </div>

                        {{-- <!-- Hourly Rate Fields -->
                        <div class="form-group" id="hourlyRateGroup">
                            <label for="hourly_rate" class="col-form-label">Hourly Rate:</label>
                            <input type="number" class="form-control" name="hourly_rate" id="hourly_rate" placeholder="Enter hourly rate">
                        </div>
                        <div class="form-group" id="numberOfHoursGroup">
                            <label for="number_of_hours" class="col-form-label">Number of Hours:</label>
                            <input type="number" class="form-control" name="number_of_hours" id="number_of_hours" placeholder="Enter number of hours">
                        </div> --}}
                    </div>

                    {{-- <div class="form-group">
                        <div class="custom-control custom-switch" style="margin-left: 15px">
                            <input type="checkbox" class="custom-control-input" name="is_project_finalized"
                                id="is_project_finalized">
                            <label class="custom-control-label" for="is_project_finalized">Is Project Finalized?</label>
                        </div>
                    </div> --}}

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        // $(document).ready(function () {
        //     // Initially hide both groups
        //     $("#fixedRateGroup").hide();
        //     $("#hourlyRateGroup").hide();
        //     $("#numberOfHoursGroup").hide();

        //     // Listen for changes on the rate type radio buttons
        //     $("input[name='rate']").on("change", function () {
        //         if ($(this).val() === "fixed") {
        //             // Show Fixed Rate and hide Hourly Rate fields
        //             $("#fixedRateGroup").show();
        //             $("#fixed_rate").prop("required", true); // Make required

        //             $("#hourlyRateGroup").hide();
        //             $("#numberOfHoursGroup").hide();
        //             $("#hourly_rate, #number_of_hours").prop("required", false); // Remove required
        //         } else if ($(this).val() === "hourly") {
        //             // Show Hourly Rate fields and hide Fixed Rate field
        //             $("#fixedRateGroup").hide();
        //             $("#fixed_rate").prop("required", false); // Remove required

        //             $("#hourlyRateGroup").show();
        //             $("#numberOfHoursGroup").show();
        //             $("#hourly_rate, #number_of_hours").prop("required", true); // Make required
        //         }
        //     });
        // });

        $(document).ready(function() {
            $('#from_month').on('change', function() {
                const fromMonth = $(this).val();

                if (fromMonth) {
                    // Enable to_month and set its min value
                    $('#to_month').prop('disabled', false);
                    $('#to_month').attr('min', fromMonth);

                    // Optional: Clear to_month if it's less than from_month
                    if ($('#to_month').val() && $('#to_month').val() < fromMonth) {
                        $('#to_month').val('');
                    }
                } else {
                    // If from_month is cleared
                    $('#to_month').prop('disabled', true).val('');
                }
            });
        });
    </script>
@endpush
