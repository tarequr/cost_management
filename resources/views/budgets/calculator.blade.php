@extends('backend.master')

@section('content')
<div class="content">
    <div class="container-fluid pt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title text-primary mb-0 text-capitalize font-weight-bold">Budget Calculator: {{ $project->name }}</h4>
                                <a href="{{ route('budgets.final', $project->id) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>

                            <div class="mb-4 d-flex justify-content-between">
                                <div>
                                    <span class="text-muted text-uppercase small d-block">Project Duration</span>
                                    <p class="font-weight-bold mb-0 text-uppercase">{{ $fromMonth }} - {{ $toMonth }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-muted text-uppercase small d-block">Reporting Month</span>
                                    <p class="font-weight-bold mb-0 text-primary">{{ \Carbon\Carbon::parse($selectedMonth)->format('F Y') }}</p>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-6">
                                    <div class="card bg-primary p-3 mb-3 border-0">
                                        <span class="text-white-50 text-uppercase small">Estimate Amount (PV)</span>
                                        <p class="h4 font-weight-bold mb-0 text-white">{{ number_format($pv, 0) }}</p>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="card bg-info p-3 mb-3 border-0">
                                        <span class="text-white-50 text-uppercase small">Actual Cost (AC)</span>
                                        <p class="h4 font-weight-bold mb-0 text-white">{{ number_format($ac, 0) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-6">
                                    <div class="card bg-warning p-3 mb-3 border-0">
                                        <span class="text-white-50 text-uppercase small">Earned Value (EV)</span>
                                        <p class="h4 font-weight-bold mb-0 text-white">{{ number_format($ev, 2) }}</p>
                                        <small class="text-white-50">({{ $progress }}% of AC)</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-secondary p-3 mb-3 border-0">
                                        <span class="text-white-50 text-uppercase small">Schedule Variance (SV)</span>
                                        <p class="h4 font-weight-bold mb-0 text-white">{{ number_format($sv, 2) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4 mt-2">
                                <div class="col-12 mb-3">
                                    <span class="text-muted text-uppercase small d-block mb-1">Budget Status:</span>
                                    <p class="h6 font-weight-bold mb-0">
                                        @if ($ac < $pv)
                                            <span class="text-success"><i class="fa fa-check-circle mr-1"></i> Your project is under budget</span>
                                        @elseif ($ac == $pv)
                                            <span class="text-primary"><i class="fa fa-info-circle mr-1"></i> Your project is on budget</span>
                                        @else
                                            <span class="text-danger"><i class="fa fa-exclamation-triangle mr-1"></i> Your project is over budget</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-12">
                                    <span class="text-muted text-uppercase small d-block mb-1">Schedule Status:</span>
                                    <p class="h6 font-weight-bold mb-0">
                                        @if ($sv > 0)
                                            <span class="text-success"><i class="fa fa-arrow-up mr-1"></i> Your project is ahead of the schedule</span>
                                        @elseif ($sv == 0)
                                            <span class="text-primary"><i class="fa fa-check mr-1"></i> Your project is on the schedule</span>
                                        @elseif ($sv < 0)
                                            <span class="text-danger"><i class="fa fa-arrow-down mr-1"></i> Your project is behind the schedule</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
