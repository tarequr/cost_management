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
                        <h4 class="page-title">Section Manage</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class=" float-right">
                            @if (Auth::user()->hasPermission('sections.create'))
                            <a href="{{ route('sections.create') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-plus-circle"></i>
                                Create
                            </a>
                            @endif
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
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach ($sections as $section)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $section->name }}</td>
                                            <td>
                                                @if ($section->status == 1)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if (Auth::user()->hasPermission('sections.edit'))
                                                <a href="{{ route('sections.edit', $section->id) }}"
                                                    class="btn btn-sm btn-success">
                                                    <i class="fa fa-edit"></i>
                                                    Edit
                                                </a>
                                                @endif

                                                {{-- <button type="button" onclick="deleteData({{ $section->id }})"
                                                    class="btn btn-danger btn-sm" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                    <span>Delete</span>
                                                </button>

                                                <form id="delete-form-{{ $section->id }}" method="POST"
                                                    action="{{ route('sections.destroy', $section->id) }}"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form> --}}
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
