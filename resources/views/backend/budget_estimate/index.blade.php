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
                        <h4 class="page-title">Budget Estimate Manage</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class=" float-right">
                            <a href="{{ route('budget-estimate.create') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-plus-circle"></i>
                                Create
                            </a>
                        </ol>
                    </div>
                </div> <!-- end row -->
            </div>
            <!-- end page-title -->

            <div class="row">
                <div class="col-12">
                    <div class="card m-b-30">
                        <div class="card-body">
                            <table id="datatable" class="table table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">SL</th>
                                        <th class="text-center">Project Name</th>
                                        <th class="text-center">Clint Name</th>
                                        {{-- <th class="text-center">Planned Budget</th>
                                        <th class="text-center">Project Period</th> --}}
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach ($budgetEstimates as $budgetEstimate)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $budgetEstimate->project_name }}</td>
                                            <td>{{ $budgetEstimate->client_name }}</td>
                                            {{-- <td>TK {{ $budgetEstimate->budget_amount }}</td>
                                            <td>
                                                {{ $budgetEstimate->start_date->format('M d, Y') }} - {{ $budgetEstimate->end_date->format('M d, Y') }}
                                            </td> --}}
                                            <td>
                                                <a href="{{ route('budget-calculator.index', $budgetEstimate->id) }}"
                                                    class="btn btn-sm btn-success">
                                                    <i class="fa fa-calculator"></i>
                                                     Calculate
                                                </a>

                                                <div class="d-inline-block">
                                                    <form action="{{ route('budget-estimate.destroy', $budgetEstimate->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete?')">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
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
@endsection

@push('js')
@endpush
