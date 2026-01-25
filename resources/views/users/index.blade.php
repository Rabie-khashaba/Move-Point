@extends('layouts.app')

@section('title', 'Users Management')

@section('content')
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Users</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">Users</li>
                <li class="breadcrumb-item">List</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="feather-plus me-2"></i>
                    <span>Add New User</span>
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
                    <h5 class="card-title mb-0">Users List</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Avatar</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>
                                            <div class="avatar-wrapper">
                                                <img src="{{ $user->avatar_url }}" alt="Avatar" 
                                                     class="rounded-circle" width="40" height="40">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <h6 class="mb-0">{{ $user->display_name }}</h6>
                                                    <small class="text-muted">{{ $user->phone }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $user->phone }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $user->full_type }}</span>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input status-toggle" type="checkbox" 
                                                       data-user-id="{{ $user->id }}"
                                                       {{ $user->is_active ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            @if($user->last_login_at)
                                                <span class="text-muted">{{ $user->last_login_at->diffForHumans() }}</span>
                                            @else
                                                <span class="text-muted">Never</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('users.show', $user->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="View">
                                                    <i class="feather-eye"></i>
                                                </a>
                                                <a href="{{ route('users.edit', $user->id) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="feather-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-user" 
                                                        data-user-id="{{ $user->id }}" title="Delete">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="feather-users" style="font-size: 3rem;"></i>
                                                <p class="mt-2">No users found</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($users->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $users->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
@endsection

@section('scripts')
<script>
    // Status toggle functionality
    document.querySelectorAll('.status-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const userId = this.dataset.userId;
            const isActive = this.checked;
            
            fetch(`/users/${userId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ is_active: isActive })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                } else {
                    showAlert('error', data.message || 'Failed to update status');
                    // Revert toggle
                    this.checked = !isActive;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Failed to update status');
                // Revert toggle
                this.checked = !isActive;
            });
        });
    });

    // Delete user functionality
    document.querySelectorAll('.delete-user').forEach(button => {
        button.addEventListener('click', function() {
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
                        // Remove the row from the table
                        this.closest('tr').remove();
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
    });

    // Auto-hide alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.classList.contains('alert-success') || alert.classList.contains('alert-danger')) {
                alert.remove();
            }
        });
    }, 5000);
</script>
@endsection
