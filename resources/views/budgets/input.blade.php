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
                                    <table class="table table-bordered table-striped" data-task-budget="{{ $task->amount }}">
                                        <thead>
                                            <tr>
                                                <th style="width: 20%;">Month</th>
                                                <th style="width: 20%;">Planned Budget</th>
                                                <th style="width: 30%;">Updated Cost</th>
                                                <!-- <th style="width: 30%;">Earned Value % (Cumulative)</th> -->
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
                                                               step="0.01"
                                                               value="{{ old('inputs.'.$month['key'].'.actual_cost', $existing->actual_cost ?? '') }}">
                                                        <small class="text-muted d-none max-msg">Max: <span class="max-val"></span></small>
                                                    </td>
                                                    <!-- <td>
                                                        <div class="input-group">
                                                            <input type="number" 
                                                                   name="inputs[{{ $month['key'] }}][earned_value_percentage]" 
                                                                   class="form-control ev-input" 
                                                                   placeholder="0-100"
                                                                   min="0" max="100"
                                                                   step="0.01"
                                                                   readonly
                                                                   value="{{ old('inputs.'.$month['key'].'.earned_value_percentage', $existing->earned_value_percentage ?? '') }}">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    </td> -->
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div id="total-error-msg" class="alert alert-danger mt-3" style="display:none;">
                                    Total cost exceeds project budget!
                                </div>
                                <div class="mt-2">
                                    <strong>Total Entered: <span id="total-entered">0.00</span> / <span id="project-budget">{{ number_format($task->amount, 2) }}</span></strong>
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
        const taskBudget = parseFloat($('table').data('task-budget')) || 0;

        // Function to calculate sum of all *other* inputs
        function getOtherInputsTotal(currentInput) {
            let totalOthers = 0;
            $('.cost-input').not(currentInput).each(function() {
                totalOthers += parseFloat($(this).val()) || 0;
            });
            return totalOthers;
        }

        // Validate a single input and show max logic
        function validateInput(input) {
            const currentVal = parseFloat(input.val()) || 0;
            const otherTotal = getOtherInputsTotal(input);
            const remainingForThis = taskBudget - otherTotal;
            
            // Display Max
            const row = input.closest('td');
            const maxMsg = row.find('.max-msg');
            const maxVal = row.find('.max-val');
            
            // Format nice number
            let displayMax = remainingForThis > 0 ? remainingForThis : 0;
            maxVal.text(displayMax.toFixed(2));
            maxMsg.removeClass('d-none'); // Show it

            // Check validity
            if (currentVal > remainingForThis + 0.001) { 
                input.addClass('is-invalid');
                return false; 
            } else {
                input.removeClass('is-invalid');
                return true;
            }
        }

        function checkTotalBudget() {
            let total = 0;
            let isValid = true;

            $('.cost-input').each(function() {
                const val = parseFloat($(this).val()) || 0;
                total += val;
            });
            
            $('#total-entered').text(total.toFixed(2));
            
            // Re-validate all inputs individually
            $('.cost-input').each(function() {
                if (!validateInput($(this))) {
                    isValid = false;
                }
            });

            // Track previous state to avoid spamming notification
            const wasInvalid = $('#total-error-msg').is(':visible');

            if (total > taskBudget + 0.001) {
                $('#total-error-msg').show();
                isValid = false;

                if (!wasInvalid && typeof iziToast !== 'undefined') {
                    iziToast.error({
                        title: 'Error',
                        message: 'Total cost exceeds project budget!',
                        position: 'topRight'
                    });
                }
            } else {
                $('#total-error-msg').hide();
            }

            $('#btn-submit').prop('disabled', !isValid);
        }
        
        // Initial check
        checkTotalBudget();

        // On Focus: Show the max allowed for this specific field
        $('.cost-input').on('focus', function() {
            validateInput($(this));
        });

        // On Blur: Hide if valid
        $('.cost-input').on('blur', function() {
            if (!$(this).hasClass('is-invalid')) {
                 $(this).closest('td').find('.max-msg').addClass('d-none');
            }
        });

        $('.cost-input').on('input', function() {
            // ... existing input logic ...
            const input = $(this);
            const val = parseFloat(input.val()) || 0;
            
            // Auto-calculate Earned Value %
            let evPercent = 0;
            if (taskBudget > 0 && val >= 0) {
                evPercent = (val / taskBudget) * 100;
            }
            
            // Update the corresponding EV input in the same row
            const row = input.closest('tr');
            row.find('.ev-input').val(evPercent.toFixed(2));

            checkTotalBudget();
        });
    });
</script>
@endpush