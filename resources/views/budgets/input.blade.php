@extends('backend.master')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <h4>Budget Input: {{ $task->task_name }}</h4>
                    </div>
                    <div class="col-sm-6 text-right">
                        <a href="{{ route('projects.show', $task->project_id) }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back to Project
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-4">
                                <h6>Task Details</h6>
                                <ul>
                                    <li><strong>Budget:</strong> {{ number_format($task->amount, 2) }}</li>
                                    <li><strong>Duration:</strong> {{ $task->start_date->format('M Y') }} - {{ $task->end_date->format('M Y') }} ({{ $task->duration }} months)</li>
                                </ul>
                            </div>

                            <form action="{{ route('tasks.budget.store', $task) }}" method="POST">
                                @csrf
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 20%;">Month</th>
                                                <th style="width: 20%;">Planned Budget</th>
                                                <th style="width: 30%;">Actual Cost</th>
                                                <th style="width: 30%;">Earned Value % (Cumulative)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($months as $month)
                                                @php
                                                    $existing = $existingRecords[$month['key']] ?? null;
                                                    $maxAllowed = $plannedBudget[$month['key']] ?? 0;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong>{{ $month['label'] }}</strong>
                                                    </td>
                                                    <td>
                                                        {{ number_format($maxAllowed, 2) }}
                                                    </td>
                                                    <td>
                                                        <input type="number" 
                                                               name="inputs[{{ $month['key'] }}][actual_cost]" 
                                                               class="form-control cost-input" 
                                                               placeholder="0.00"
                                                               max="{{ $maxAllowed }}"
                                                               step="0.01"
                                                               value="{{ old('inputs.'.$month['key'].'.actual_cost', $existing->actual_cost ?? '') }}">
                                                        <small class="text-danger error-msg" style="display:none;">Exceeds Budget!</small>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="number" 
                                                                   name="inputs[{{ $month['key'] }}][earned_value_percentage]" 
                                                                   class="form-control" 
                                                                   placeholder="0-100"
                                                                   min="0" max="100"
                                                                   value="{{ old('inputs.'.$month['key'].'.earned_value_percentage', $existing->earned_value_percentage ?? '') }}">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-right mt-3">
                                    <button type="submit" class="btn btn-primary" id="btn-submit">
                                        <i class="fa fa-save"></i> Save Input
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
<script>
    $(document).ready(function() {
        $('.cost-input').on('input', function() {
            const input = $(this);
            const max = parseFloat(input.attr('max')) || 0;
            const val = parseFloat(input.val()) || 0;
            const errorMsg = input.siblings('.error-msg');
            const submitBtn = $('#btn-submit');

            if (val > max) {
                input.addClass('is-invalid');
                errorMsg.show();
                submitBtn.prop('disabled', true);
            } else {
                input.removeClass('is-invalid');
                errorMsg.hide();
                // Check if any other input has error
                if ($('.is-invalid').length === 0) {
                    submitBtn.prop('disabled', false);
                }
            }
        });
    });
</script>
@endpush
