@extends('backend.master')

@push('css')
    <style>
        .final-budget-table {
            font-size: 11px;
            width: 100%;
            border-collapse: collapse;
        }
        .final-budget-table th, 
        .final-budget-table td {
            border: 1px solid #dee2e6;
            padding: 4px;
            vertical-align: middle;
        }
        .final-budget-table thead th {
            text-align: center;
            background-color: #f8f9fa;
        }
        .bg-yellow { background-color: #ffff00 !important; }
        .bg-orange { background-color: #f4b084 !important; } /* For highlighted cells if needed */
        .bg-blue-light { background-color: #daeef3 !important; }
        .bg-green-light { background-color: #e2efda !important; }
        .bg-grey-light { background-color: #d9d9d9 !important; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-weight-bold { font-weight: bold; }
        
        /* Sticky first columns? Maybe too complex for now */
    </style>
@endpush

@section('content')
    <div class="content">
        <div class="container-fluid pt-3">
            <div class="row mb-3">
                <div class="col-sm-6">
                    <h4>Final Budget: {{ $project->project_name }}</h4>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('projects.show', $project->id) }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to Project
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="final-budget-table">
                            <thead>
                                <tr class="bg-yellow">
                                    <th colspan="{{ 5 + count($months) + 1 }}" class="text-center font-weight-bold" style="font-size: 14px;">
                                        Final Budget
                                    </th>
                                </tr>
                                <tr>
                                    <th style="min-width: 200px;">WBS Activity</th>
                                    <th>Duration<br>(months)</th>
                                    <th>From</th>
                                    <th>To</th>
                                    @foreach($months as $m)
                                        <th style="min-width: 60px;">{{ $m['label'] }}</th>
                                    @endforeach
                                    <th>Total Budget</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Tasks Rows --}}
                                @foreach($tasksData as $row)
                                    <tr>
                                        <td>{{ $row['task']->task_name }}</td>
                                        <td class="text-center">{{ $row['task']->duration }}</td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($row['task']->start_date)->format('M') }}</td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($row['task']->end_date)->format('M') }}</td>
                                        
                                        @foreach($months as $m)
                                            <td class="text-right">
                                                @if($row['monthly'][$m['key']]['actual'] > 0)
                                                    {{ number_format($row['monthly'][$m['key']]['actual'], 0) }}
                                                @endif
                                            </td>
                                        @endforeach
                                        
                                        <td class="text-right font-weight-bold">
                                            {{ number_format($row['total_planned'], 0) }}
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Grand Total --}}
                                <tr class="font-weight-bold bg-light">
                                    <td colspan="4">Grand Total</td>
                                    @foreach($months as $m)
                                        <td class="text-right">
                                            {{ number_format($footerData[$m['key']]['ac_incremental'], 0) }}
                                        </td>
                                    @endforeach
                                    <td class="text-right">{{ number_format($bac, 0) }}</td>
                                </tr>

                                {{-- Spacer Row --}}
                                <tr><td colspan="{{ 5 + count($months) + 1 }}" style="height: 10px;"></td></tr>

                                {{-- PV Section --}}
                                <tr>
                                    <td colspan="2" rowspan="3" class="text-center bg-grey-light font-weight-bold">Planned Value</td>
                                    <td colspan="2">PV (Monthly)</td>
                                    @foreach($months as $m)
                                        <td class="text-right">{{ number_format($footerData[$m['key']]['pv_incremental'], 0) }}</td>
                                    @endforeach
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2">PV (Cum)</td>
                                    @foreach($months as $m)
                                        <td class="text-right">{{ number_format($footerData[$m['key']]['pv_cumulative'], 0) }}</td>
                                    @endforeach
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2">PV %</td>
                                    @foreach($months as $m)
                                        <td class="text-right">{{ number_format($footerData[$m['key']]['pv_pct'], 2) }}%</td>
                                    @endforeach
                                    <td></td>
                                </tr>

                                {{-- Progress Data Section --}}
                                <tr>
                                    <td colspan="2" rowspan="4" class="text-center bg-green-light font-weight-bold">Project Progress Data</td>
                                    <td colspan="2" class="bg-green-light">Actual Cost</td>
                                    @foreach($months as $m)
                                        <td class="text-right bg-green-light">
                                            {{ number_format($footerData[$m['key']]['ac_incremental'], 0) }}
                                        </td>
                                    @endforeach
                                    <td class="bg-green-light"></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="bg-green-light">AC (Cum)</td>
                                    @foreach($months as $m)
                                        <td class="text-right bg-green-light">
                                            {{ number_format($footerData[$m['key']]['ac_cumulative'], 0) }}
                                        </td>
                                    @endforeach
                                    <td class="bg-green-light"></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="bg-green-light">EV %</td>
                                    @foreach($months as $m)
                                        <td class="text-right bg-green-light">
                                            {{ number_format($footerData[$m['key']]['ev_pct_cumulative'], 2) }}%
                                        </td>
                                    @endforeach
                                    <td class="bg-green-light"></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="bg-green-light">EV</td>
                                    @foreach($months as $m)
                                        <td class="text-right bg-green-light">
                                            {{ number_format($footerData[$m['key']]['ev_cumulative'], 0) }}
                                        </td>
                                    @endforeach
                                    <td class="bg-green-light"></td>
                                </tr>

                                {{-- Current Performance Section --}}
                                <tr>
                                    <td colspan="2" rowspan="4" class="text-center bg-blue-light font-weight-bold">Current Performance</td>
                                    <td colspan="2" class="bg-blue-light">CV (EV-AC)</td>
                                    @foreach($months as $m)
                                        <td class="text-right bg-blue-light {{ $footerData[$m['key']]['cv'] < 0 ? 'text-danger' : '' }}">
                                            {{ number_format($footerData[$m['key']]['cv'], 0) }}
                                        </td>
                                    @endforeach
                                    <td class="bg-blue-light"></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="bg-blue-light">CPI (EV/AC)</td>
                                    @foreach($months as $m)
                                        <td class="text-right bg-blue-light {{ $footerData[$m['key']]['cpi'] < 1 ? 'text-danger' : '' }}">
                                            {{ number_format($footerData[$m['key']]['cpi'], 2) }}
                                        </td>
                                    @endforeach
                                    <td class="bg-blue-light"></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="bg-blue-light">SV (EV-PV)</td>
                                    @foreach($months as $m)
                                        <td class="text-right bg-blue-light {{ $footerData[$m['key']]['sv'] < 0 ? 'text-danger' : '' }}">
                                            {{ number_format($footerData[$m['key']]['sv'], 0) }}
                                        </td>
                                    @endforeach
                                    <td class="bg-blue-light"></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="bg-blue-light">SPI (EV/PV)</td>
                                    @foreach($months as $m)
                                        <td class="text-right bg-blue-light {{ $footerData[$m['key']]['spi'] < 1 ? 'text-danger' : '' }}">
                                            {{ number_format($footerData[$m['key']]['spi'], 2) }}
                                        </td>
                                    @endforeach
                                    <td class="bg-blue-light"></td>
                                </tr>

                                {{-- Forecast Section --}}
                                <tr>
                                    <td colspan="2" rowspan="2" class="text-center bg-grey-light font-weight-bold">Forecast</td>
                                    <td colspan="2" class="bg-grey-light">EAC (BAC/CPI)</td>
                                    @foreach($months as $m)
                                        <td class="text-right bg-grey-light">
                                            {{ number_format($footerData[$m['key']]['eac'], 0) }}
                                        </td>
                                    @endforeach
                                    <td class="bg-grey-light"></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="bg-grey-light">Est Duration</td>
                                    @foreach($months as $m)
                                        <td class="text-right bg-grey-light">
                                            {{ number_format($footerData[$m['key']]['est_duration'], 1) }}
                                        </td>
                                    @endforeach
                                    <td class="bg-grey-light"></td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
