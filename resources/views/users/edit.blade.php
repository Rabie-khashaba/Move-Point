@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Users</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.show', $user->id) }}">View</a></li>
                <li class="breadcrumb-item">Edit</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <a href="{{ route('users.show', $user->id) }}" class="btn btn-light-brand">
                    <i class="feather-arrow-left me-2"></i>
                    <span>Back to User</span>
                </a>
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->
    
    <!-- [ Main Content ] start -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit User</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       placeholder="Enter full name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number *</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                       placeholder="Enter phone number" value="{{ old('phone', $user->phone) }}" required>
                                @error('phone')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">User Type *</label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="">Select user type</option>
                                    <option value="admin" {{ old('type', $user->type) == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="employee" {{ old('type', $user->type) == 'employee' ? 'selected' : '' }}>Employee</option>
                                    <option value="supervisor" {{ old('type', $user->type) == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                    <option value="representative" {{ old('type', $user->type) == 'representative' ? 'selected' : '' }}>Representative</option>
                                </select>
                                @error('type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                       placeholder="Leave empty to keep current password">
                                <small class="form-text text-muted">Only fill if you want to change the password</small>
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control" 
                                       placeholder="Confirm new password">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Profile Photo</label>
                                <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror" 
                                       accept="image/*" id="avatarInput">
                                <small class="form-text text-muted">Upload JPG, PNG, or GIF (max 2MB). Leave empty to keep current photo.</small>
                                @error('avatar')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Photo Preview</label>
                                <div class="avatar-preview-container">
                                    <img id="avatarPreview" src="{{ $user->avatar_url }}" 
                                         alt="Avatar Preview" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label">Active</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="feather-save me-2"></i>
                                Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
@endsection

@section('scripts')
<script>
    // Avatar preview functionality
    document.getElementById('avatarInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('avatarPreview');
        
        if (file) {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                alert('Please select a valid image file.');
                this.value = '';
            }
        }
    });
</script>
@endsection
