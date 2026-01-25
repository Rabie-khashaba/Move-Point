@extends('layouts.app')

@section('title', 'User Details')

@section('content')
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Users</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                <li class="breadcrumb-item">View</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                    <i class="feather-edit me-2"></i>
                    <span>Edit User</span>
                </a>
                <a href="{{ route('users.index') }}" class="btn btn-light-brand">
                    <i class="feather-arrow-left me-2"></i>
                    <span>Back to Users</span>
                </a>
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->
    
    <!-- [ Main Content ] start -->
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-wrapper mb-3">
                        <img src="{{ $user->avatar_url }}" alt="User Avatar" 
                             class="rounded-circle" width="120" height="120">
                    </div>
                    <h4 class="mb-1">{{ $user->display_name }}</h4>
                    <p class="text-muted mb-2">{{ $user->phone }}</p>
                    <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }} mb-3">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                            <i class="feather-edit me-2"></i>Edit User
                        </a>
                        <button type="button" class="btn btn-danger delete-user" data-user-id="{{ $user->id }}">
                            <i class="feather-trash-2 me-2"></i>Delete User
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">User Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <p class="form-control-plaintext">{{ $user->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Phone Number</label>
                            <p class="form-control-plaintext">{{ $user->phone }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">User Type</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-info">{{ $user->full_type }}</span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Last Login</label>
                            <p class="form-control-plaintext">
                                @if($user->last_login_at)
                                    {{ $user->last_login_at->format('M d, Y H:i') }}
                                    <br><small class="text-muted">{{ $user->last_login_at->diffForHumans() }}</small>
                                @else
                                    <span class="text-muted">Never logged in</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Created At</label>
                            <p class="form-control-plaintext">
                                {{ $user->created_at->format('M d, Y H:i') }}
                                <br><small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Updated At</label>
                            <p class="form-control-plaintext">
                                {{ $user->updated_at->format('M d, Y H:i') }}
                                <br><small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($user->roles && $user->roles->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">User Roles</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($user->roles as $role)
                        <div class="col-md-6 mb-2">
                            <span class="badge bg-primary me-2">{{ $role->name }}</span>
                            @if($role->department)
                                <small class="text-muted">({{ $role->department->name }})</small>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    <!-- [ Main Content ] end -->
@endsection

@section('scripts')
<script>
    // Delete user functionality
    document.querySelector('.delete-user').addEventListener('click', function() {
        const userId = this.dataset.userId;
        
        if (confirmDelete('Are you sure you want to delete this user? This action cannot be undone.')) {
            fetch(`/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    // Redirect to users index
                    setTimeout(() => {
                        window.location.href = '{{ route("users.index") }}';
                    }, 1500);
                } else {
                    showAlert('error', data.message || 'Failed to delete user');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Failed to delete user');
            });
        }
    });
</script>
@endsection
