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
                        </ol>
                    </div>
                </div> <!-- end row -->
            </div>
            <!-- end page-title -->

            <div class="row">
                <div class="col-8">
                    <div class="card m-b-30">
                        <div class="d-flex justify-content-between p-3">
                            <div style="background-color: aliceblue; padding: 5px 15px;">
                                <h4>{{ $budgetEstimate->project_name }}</h4>
                                <p class="mb-0" style="font-size: 16px">
                                    <b>Planned Budget:</b>
                                    <span class="badge badge-primary">{{ $plannedValue }}</span>
                                </p>
                                <p class="mb-0">
                                    <b>{{ $budgetEstimate->client_name }}</b> |
                                    <span class="text-uppercase">
                                        {{ date('M d, Y', strtotime($budgetEstimate->start_date)) }} - {{ date('M d, Y', strtotime($budgetEstimate->end_date)) }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <a href="javascript:void(0);" class="btn btn-primary" data-toggle="modal" data-target="#addModal">
                                    <i class="fa fa-plus-circle"></i>
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">Task Name</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Rate</th>
                                        <th class="text-center">Cost</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach ($budgetCalculators as $budgetCalculator)
                                        <tr>
                                            <td>{{ $budgetCalculator->task_name }}</td>
                                            <td>
                                                <span class="text-uppercase">{{ date('M d', strtotime($budgetCalculator->from_date)) .' - ' . date('M d', strtotime($budgetCalculator->to_date)) }}</span>
                                            </td>
                                            <td>
                                                @if ($budgetCalculator->rate == 'fixed')
                                                    <span class="text-uppercase">Fixed</span>
                                                @else
                                                    <span class="text-uppercase">Hourly - ({{ $budgetCalculator->hourly_rate.' Rate' }} * {{ $budgetCalculator->number_of_hours. ' Hours' }})</span>
                                                @endif
                                            </td>
                                            <td>TK {{ $budgetCalculator->total }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> <!-- end col -->

                <div class="col-md-4">
                    <div class="card shadow-md border-0 mb-4">
                        <div class="card-body">
                            <h4 class="card-title text-primary mb-4 text-capitalize text-bold">Budget Calculator</h4>

                            <div class="mb-4">
                                <span class="text-muted text-uppercase">Summary</span>
                                <p class="h2 font-weight-bold text-success mb-0">TK {{ $totalCost ? $totalCost : '00.00' }}</p>
                            </div>

                            <div class="row mb-4">
                                <div class="col-6">
                                    <span class="text-muted text-uppercase">Tasks</span>
                                    <p class="h4 font-weight-bold mb-0">{{ $totalTasks }}</p>
                                </div>

                                <div class="col-6">
                                    <span class="text-muted text-uppercase">Weeks</span>
                                    <p class="h4 font-weight-bold mb-0">{{ ceil(\Carbon\Carbon::parse($budgetEstimate->start_date)->diffInDays($budgetEstimate->end_date) / 7) }}</p>
                                </div>
                            </div>

                            <div class="border-top pt-3">
                                <span class="text-muted text-uppercase">Delivery Date</span>
                                <p class="h5 font-weight-bold mb-0">
                                    <i class="bi bi-calendar text-primary me-2"></i>
                                    {{ $budgetEstimate->budgetCalculators && $budgetEstimate->budgetCalculators->count() > 0 ? date('M d, Y', strtotime($budgetEstimate->budgetCalculators->max('to_date'))) : '' }}
                                </p>
                            </div>

                            <div class="row mb-4 mt-2">
                                {{-- <div class="col-12">
                                    <span class="text-muted text-uppercase">EV :</span>
                                    <p class="h4 font-weight-bold mb-0">{{ ceil($earnedValue) }}</p>
                                </div>
                                <div class="col-12">
                                    <span class="text-muted text-uppercase">PV :</span>
                                    <p class="h4 font-weight-bold mb-0">{{ ceil($plannedValue) }}</p>
                                </div>
                                <div class="col-12">
                                    <span class="text-muted text-uppercase">BAC :</span>
                                    <p class="h4 font-weight-bold mb-0">{{ ceil($bac) }}</p>
                                </div> --}}
                                @if (!$isNullBudgetCalculators)
                                    <div class="col-12">
                                        <span class="text-muted text-uppercase">Budget :</span>
                                        {{-- <p class="h4 font-weight-bold mb-0">{{ ceil($costVariance) }}</p> --}}
                                        <p class="h6 font-weight-bold mb-0">
                                            @if (ceil($costVariance) > 0)
                                                <span class="text-success">Your Project is Under Budget</span>
                                            @elseif ($totalCost == $plannedValue)
                                                <span class="text-primary">Your Project is on Budget</span>
                                            @else
                                                <span class="text-danger">Your Project is Over Budget</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-12">
                                        <span class="text-muted text-uppercase">Schedule :</span>
                                        {{-- <p class="h4 font-weight-bold mb-0">{{ ceil($scheduleVariance) }}</p> --}}
                                        <p class="h6 font-weight-bold mb-0">
                                            @if (ceil($scheduleVariance) > 0)
                                                <span class="text-success">Your project is ahead of the schedule</span>
                                            @elseif (ceil($scheduleVariance) == 0)
                                                <span class="text-primary">You project is on the schedule</span>
                                            @else
                                                <span class="text-danger">Your project is behind the schedule</span>
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end row -->
        </div>
        <!-- container-fluid -->
    </div>
    <!-- content -->

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
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
                            <input type="text" class="form-control" name="task_name" id="task_name" placeholder="Enter task name" required>
                        </div>

                        <div class="form-group">
                            <label for="from_date" class="col-form-label">From Date:</label>
                            <input type="date" class="form-control" name="from_date" id="from_date" min="{{ \Carbon\Carbon::parse($budgetEstimate->start_date)->format('Y-m-d') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="to_date" class="col-form-label">To Date:</label>
                            <input type="date" class="form-control" name="to_date" id="to_date" min="{{ \Carbon\Carbon::parse($budgetEstimate->start_date)->format('Y-m-d') }}" required>
                        </div>

                        <div class="form-group">
                            <label class="col-form-label">Rate Type:</label>
                            <div class="form-check">
                                <input type="radio" class="form-check-input text-uppercase" id="fixedRateType" name="rate" value="fixed">Fixed
                                <label class="form-check-label" for="fixedRateType"></label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input text-uppercase" id="hourlyRateType" name="rate" value="hourly">Hourly
                                <label class="form-check-label" for="hourlyRateType"></label>
                            </div>
                        </div>

                        <!-- Fixed Rate Field -->
                        <div class="form-group" id="fixedRateGroup">
                            <label for="fixed_rate" class="col-form-label">Fixed Rate:</label>
                            <input type="number" class="form-control" name="fixed_rate" id="fixed_rate" placeholder="Enter fixed rate">
                        </div>

                        <!-- Hourly Rate Fields -->
                        <div class="form-group" id="hourlyRateGroup">
                            <label for="hourly_rate" class="col-form-label">Hourly Rate:</label>
                            <input type="number" class="form-control" name="hourly_rate" id="hourly_rate" placeholder="Enter hourly rate">
                        </div>
                        <div class="form-group" id="numberOfHoursGroup">
                            <label for="number_of_hours" class="col-form-label">Number of Hours:</label>
                            <input type="number" class="form-control" name="number_of_hours" id="number_of_hours" placeholder="Enter number of hours">
                        </div>
                    </div>
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
        $(document).ready(function () {
            // Initially hide both groups
            $("#fixedRateGroup").hide();
            $("#hourlyRateGroup").hide();
            $("#numberOfHoursGroup").hide();

            // Listen for changes on the rate type radio buttons
            $("input[name='rate']").on("change", function () {
                if ($(this).val() === "fixed") {
                    // Show Fixed Rate and hide Hourly Rate fields
                    $("#fixedRateGroup").show();
                    $("#fixed_rate").prop("required", true); // Make required

                    $("#hourlyRateGroup").hide();
                    $("#numberOfHoursGroup").hide();
                    $("#hourly_rate, #number_of_hours").prop("required", false); // Remove required
                } else if ($(this).val() === "hourly") {
                    // Show Hourly Rate fields and hide Fixed Rate field
                    $("#fixedRateGroup").hide();
                    $("#fixed_rate").prop("required", false); // Remove required

                    $("#hourlyRateGroup").show();
                    $("#numberOfHoursGroup").show();
                    $("#hourly_rate, #number_of_hours").prop("required", true); // Make required
                }
            });
        });

    </script>
@endpush
