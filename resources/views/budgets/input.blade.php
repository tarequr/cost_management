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
                                    <li><strong>Budget:</strong> {{ number_format($task->cost, 2) }}</li>
                                    <li><strong>Duration:</strong> {{ $task->start_date->format('M Y') }} - {{ $task->end_date->format('M Y') }} ({{ $task->duration }} months)</li>
                                </ul>
                            </div>

                            @if($isLocked)
                                <div class="alert alert-warning">
                                    <i class="fa fa-lock"></i> This task's budget is finalized and cannot be updated.
                                </div>
                            @endif

                            <form action="{{ route('tasks.budget.store', $task) }}" method="POST" id="budget-input-form">
                                @csrf
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" data-task-budget="{{ $task->cost }}">
                                        <thead>
                                            <tr>
                                                <th style="width: 20%;">Month</th>
                                                <th style="width: 20%;">Planned Budget</th>
                                                <th style="width: 30%;">Updated Cost</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($months as $month)
                                                @php
                                                    $existing = $existingRecords[$month['key']] ?? null;
                                                    $plannedAmount = $plannedBudget[$month['key']] ?? 0;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong>{{ $month['label'] }}</strong>
                                                    </td>
                                                    <td>
                                                        <span class="planned-val" data-month="{{ $month['key'] }}">{{ number_format($plannedAmount, 2) }}</span>
                                                    </td>
                                                    <td>
                                                        <input type="number" 
                                                               id="inputs_{{ $month['key'] }}_actual_cost"
                                                               name="inputs[{{ $month['key'] }}][actual_cost]" 
                                                               class="form-control cost-input @error('inputs.'.$month['key'].'.actual_cost') is-invalid @enderror" 
                                                               placeholder="0.00"
                                                               step="0.01"
                                                               required
                                                               {{ $isLocked ? 'readonly' : '' }}
                                                               data-planned="{{ $plannedAmount }}"
                                                               value="{{ old('inputs.'.$month['key'].'.actual_cost', $existing->actual_cost ?? '') }}">
                                                        <div class="invalid-feedback">Total entered cost must exactly match the task budget.</div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div id="total-error-msg" class="alert alert-danger mt-3" style="display:none;">
                                    Total entered cost must exactly match the task budget!
                                </div>
                                <div class="mt-2">
                                    <strong>Total Entered: <span id="total-entered">0.00</span> / <span id="project-budget">{{ number_format($task->cost, 2) }}</span></strong>
                                </div>

                                <div class="text-right mt-3">
                                    @if(!$isLocked)
                                        <button type="submit" class="btn btn-primary" id="btn-submit">
                                            <i class="fa fa-save"></i> Save Input
                                        </button>
                                    @endif
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
        @if($isLocked)
            $('.cost-input').prop('readonly', true);
            calculateTotal();
            return;
        @endif

        const taskBudget = parseFloat($('table').data('task-budget')) || 0;

        function calculateTotal() {
            let total = 0;
            $('.cost-input').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#total-entered').text(total.toFixed(2));
            return total;
        }

        function validateForm() {
            let allFilled = true;
            let total = calculateTotal();

            $('.cost-input').each(function() {
                if ($(this).val() === '') {
                    allFilled = false;
                }
            });

            if (Math.abs(total - taskBudget) > 0.01) {
                $('.cost-input').addClass('is-invalid');
                $('#total-error-msg').show();
                $('#btn-submit').prop('disabled', true);
            } else {
                $('.cost-input').removeClass('is-invalid');
                $('#total-error-msg').hide();
                
                if (allFilled) {
                    $('#btn-submit').prop('disabled', false);
                } else {
                    $('#btn-submit').prop('disabled', true);
                }
            }
        }

        $('.cost-input').on('input change blur', function() {
            validateForm();
        });

        // Initial validation
        validateForm();
    });
</script>
@endpush