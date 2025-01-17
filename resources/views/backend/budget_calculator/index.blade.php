@extends('backend.master')

@push('css')
@endpush

@section('content')
    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <h4 class="page-title">Budget Calculation</h4>
                    </div>
                </div> <!-- end row -->
            </div>
            <!-- end page-title -->

            <div class="row">
                <div class="col-8">
                    <div class="card m-b-30">
                        <div class="d-flex justify-content-between p-3">
                            <div>
                                <h4>Task</h4>
                                <p class="mb-0"><b>Clint Name</b> | <span>JAN 01, 2020 - JAN 31, 2020</span></p>
                            </div>
                            <div>
                                <a href="javascript:void(0);" class="btn btn-primary">
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
@endsection

@push('js')
@endpush
