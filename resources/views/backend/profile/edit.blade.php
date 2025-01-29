<form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-md-12">
                <label for="name" class="form-label">Name <sup class="text-danger">*</sup></label>
                <input type="text" name="name" id="name" value="{{ $user->name }}" class="form-control"
                    placeholder="Enter name" required>
            </div>
            <div class="form-group col-md-12">
                <label for="email" class="form-label">Email <sup class="text-danger">*</sup></label>
                <input type="email" name="email" value="{{ $user->email }}" id="email" class="form-control"
                    placeholder="Enter email" required>
            </div>


        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" name="phone" value="{{ $user->phone }}" id="phone" class="form-control"
                    placeholder="Enter phone" required>
            </div>
            <div class="form-group col-md-12">
                <label for="gender" class="form-label">Gender</label>
                <select name="gender" class="form-control" id="gender">
                    <option value="">Please select</option>
                    <option value="Male" {{ $user->gender == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ $user->gender == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Others" {{ $user->gender == 'Others' ? 'selected' : '' }}>Others</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <label for="address" class="form-label">Address</label>
                <input type="text" name="address" value="{{ $user->address }}" id="address" class="form-control"
                    placeholder="Enter address" required>
            </div>
            <div class="form-group col-md-12">
                <label for="avatar">Avatar</label>
                <input id="avatar" type="file" class="dropify" name="avatar" autofocus data-height="150"
                    data-default-file="{{ @$user->avatar != null ? asset('upload/user_images/' . @$user->avatar) : '' }}">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>
