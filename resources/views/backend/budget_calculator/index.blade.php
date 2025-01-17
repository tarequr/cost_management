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
                                <p class="mb-0"><b>{{ $budgetEstimate->client_name }}</b> | <span class="text-uppercase">{{ date('M d, Y', strtotime($budgetEstimate->start_date)) }} - {{ date('M d, Y', strtotime($budgetEstimate->end_date)) }}</span></p>
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
                                <p class="h2 font-weight-bold text-success mb-0">$160.00</p>
                            </div>

                            <div class="row mb-4">
                                <div class="col-6">
                                    <span class="text-muted text-uppercase">Tasks</span>
                                    <p class="h4 font-weight-bold mb-0">$160.00</p>
                                </div>

                                <div class="col-6">
                                    <span class="text-muted text-uppercase">Weeks</span>
                                    <p class="h4 font-weight-bold mb-0">$160.00</p>
                                </div>
                            </div>

                            <div class="border-top pt-3">
                                <span class="text-muted text-uppercase">Delivery Date</span>
                                <p class="h5 font-weight-bold mb-0">
                                    <i class="bi bi-calendar text-primary me-2"></i>20/02/2025
                                </p>
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
                <form method="POST" action="" id="myform">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="task_name" class="col-form-label">Task Name:</label>
                            <input type="text" class="form-control" name="task_name" id="task_name" placeholder="Enter task name" required>
                        </div>

                        <div class="form-group">
                            <label for="from_date" class="col-form-label">From Date:</label>
                            <input type="date" class="form-control" name="from_date" id="from_date" min="{{ $budgetEstimate->start_date }}" max="{{ $budgetEstimate->end_date }}" required>
                        </div>

                        <div class="form-group">
                            <label for="to_date" class="col-form-label">To Date:</label>
                            <input type="date" class="form-control" name="to_date" id="to_date" required>
                        </div>

                        <div class="form-group">
                            <label for="home_page" class="col-form-label">Home Page:</label>
                            <select name="home_page" id="home_page" class="form-control" required>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
@endpush
