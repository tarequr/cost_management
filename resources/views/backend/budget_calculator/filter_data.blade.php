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
                        <h4 class="page-title">Filter Data</h4>
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
                <div class="col-md-12">
                    <div class="card shadow-md border-0 mb-4">
                        <div class="card-body">
                            <h4 class="card-title text-primary mb-4 text-capitalize text-bold">Budget Calculator</h4>

                            <div class="mb-4">
                                <span class="text-muted text-uppercase">Summary</span>
                                <p class="h2 font-weight-bold text-success mb-0">TK
                                </p>
                            </div>

                            <div class="row mb-4">
                                <div class="col-6">
                                    <span class="text-muted text-uppercase">Tasks</span>
                                    <p class="h4 font-weight-bold mb-0">0</p>
                                </div>

                                <div class="col-6">
                                    <span class="text-muted text-uppercase">Weeks</span>
                                    <p class="h4 font-weight-bold mb-0">
                                        00
                                    </p>
                                </div>
                            </div>

                            <div class="border-top pt-3">
                                <span class="text-muted text-uppercase">Delivery Date</span>
                                <p class="h5 font-weight-bold mb-0">
                                    <i class="bi bi-calendar text-primary me-2"></i>
                                    00
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
                                {{-- @if (!$isNullBudgetCalculators) --}}
                                <div class="col-12">
                                    <span class="text-muted text-uppercase">Budget :</span>
                                    {{-- <p class="h4 font-weight-bold mb-0">{{ ceil($costVariance) }}</p> --}}
                                    <p class="h6 font-weight-bold mb-0">
                                        {{-- @if (ceil($costVariance) > 0) --}}
                                        <span class="text-success">Your project is under budget</span>
                                        {{-- @elseif ($totalCost == $plannedValue) --}}
                                        <span class="text-primary">Your project is on budget</span>
                                        {{-- @else --}}
                                        <span class="text-danger">Your project is over budget</span>
                                        {{-- @endif --}}
                                    </p>
                                </div>
                                <div class="col-12">
                                    <span class="text-muted text-uppercase">Schedule :</span>
                                    {{-- <p class="h4 font-weight-bold mb-0">{{ ceil($scheduleVariance) }}</p> --}}
                                    <p class="h6 font-weight-bold mb-0">
                                        {{-- @if (ceil($scheduleVariance) > 0) --}}
                                        <span class="text-success">Your project is ahead of the schedule</span>
                                        {{-- @elseif (ceil($scheduleVariance) == 0) --}}
                                        <span class="text-primary">You project is on the schedule</span>
                                        {{-- @else --}}
                                        <span class="text-danger">Your project is behind the schedule</span>
                                        {{-- @endif --}}
                                    </p>
                                </div>
                                {{-- @endif --}}
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
