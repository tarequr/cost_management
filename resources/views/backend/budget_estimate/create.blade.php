@extends('backend.master')

@push('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css" rel="stylesheet">
@endpush

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <ol class=" float-right">
                            <a href="{{ route('sections.index') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-list"></i>
                                View
                            </a>
                        </ol>
                    </div>
                </div> <!-- end row -->
            </div>
            <!-- end page-title -->

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card m-b-30">
                        <div class="card-body shadow">
                            <h4 class="m-2 text-center header-title">Create Section</h4>
                            <form method="POST" action="{{ route('sections.store') }}" enctype="multipart/form-data">
                                @csrf

                                <div class="form-group">
                                    <label for="name">Name <sup class="text-danger">*</sup></label>
                                    <div>
                                        <input type="text" id="name" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name') }}" placeholder="Enter section name."
                                            autocomplete="off" />
                                    </div>

                                    @error('name')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="status" id="status"
                                            checked>
                                        <label class="custom-control-label" for="status">Status</label>
                                    </div>
                                </div>

                                <div class="form-group m-b-0">
                                    <div>
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                                            Save
                                        </button>
                                        <button type="reset" class="btn btn-secondary waves-effect m-l-5">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div>
        <!-- container-fluid -->
    </div>
@endsection

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
    <script>
        $('#file').dropify();
    </script>
@endpush
