@extends('backend.master')

@push('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css" rel="stylesheet">
    <style>
        .dropify-message p {
            font-size: 18px;
        }
    </style>
@endpush

@section('content')
    <div class="app-page-title mt-5">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-users icon-gradient bg-mean-fruit">
                    </i>
                </div>
                <div>Users Manage</div>
            </div>
            @php
                $user = Auth::user();
            @endphp
            <div class="page-title-actions d-flex justify-content-end mt-4 " style="margin-right: 2rem;">
                <button type="button" class="btn mr-2 mb-2 btn-success edit mt-2" data-toggle="modal"
                    data-target="#editUser">
                    <i class="fa fa-edit"></i>
                    <span>Edit user</span>
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5 mx-auto">
            <div class="main-card mb-3 card" style="padding: 10px;">
                <div class="card-body p-0">
                    <div class="d-flex justify-content-center mb-2">
                        <img src="{{ $user->avatar != null ? asset('upload/user_images/' . @$user->avatar) : asset('backend/assets/images/placeholder.png') }}"
                            class="img-fluid img-thumbnail rounded-circle" alt="User Avatar"
                            style="width: 160px; height: 160px;">
                    </div>
                    <table class="table table-hover mb-0">
                        <tbody>
                            <tr>
                                <th scope="row">Name: </th>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Email: </th>
                                <td>{{ $user->email }}</td>
                            </tr>
                            @if ($user->phone != null)
                                <tr>
                                    <th scope="row">Phone: </th>
                                    <td>{{ $user->phone }}</td>
                                </tr>
                            @endif

                            @if ($user->gender != null)
                                <tr>
                                    <th scope="row">Gender: </th>
                                    <td>{{ $user->gender }}</td>
                                </tr>
                            @endif

                            @if ($user->address != null)
                                <tr>
                                    <th scope="row">Address: </th>
                                    <td>{{ $user->address }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th scope="row">Role: </th>
                                <td>
                                    @if ($user->role)
                                        <span class="badge badge-info">{{ $user->role->name }}</span>
                                    @else
                                        <span class="badge badge-warning">No role found :</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Status: </th>
                                <td>
                                    @if ($user->status == true)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Last Modify At: </th>
                                <td>{{ $user->updated_at ? $user->updated_at->diffForHumans() : 'No modify' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Join At: </th>
                                <td>{{ $user->created_at->diffForHumans() }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editUser" tabindex="-1" role="dialog" aria-labelledby="editUserLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserLabel">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
    <script>
        $(document).ready(function() {
            $('body').on('click', '.edit', function() {
                $.get("profile/edit", function(data) {
                    // console.log(data); // Check the data in the console
                    $('.modal-body').html(data);
                    $('.dropify').dropify();
                });
            });
            $('.dropify').dropify();
        });
    </script>
@endpush
