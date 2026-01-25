@extends('layouts.app')

@section('title', 'إدارة الصلاحيات')

@section('content')
<div class="nxl-content">
    <div class="content-area" data-scrollbar-target="#psScrollbarInit">

        <div class="content-area-body p-3">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">إدارة الصلاحيات</h5>
                    <div>
                        <a href="{{ route('permissions.sync.preview') }}" class="btn btn-outline-primary btn-sm me-2">
                            <i class="fas fa-sync"></i> مزامنة الصلاحيات
                        </a>
                        <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> إضافة صلاحية
                        </a>
                    </div>
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
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اسم الصلاحية</th>
                                    <th>الاسم المعروض</th>
                                    <th>الوصف</th>
                                    <th>الوحدة</th>
                                    <th>نوع العملية</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permissions as $permission)
                                    <tr>
                                        <td>{{ $permission->id }}</td>
                                        <td>
                                            <code>{{ $permission->name }}</code>
                                        </td>
                                        <td>
                                            <strong>{{ $permission->display_name ?? $permission->name }}</strong>
                                        </td>
                                        <td>
                                            @if($permission->description)
                                                <small class="text-muted">{{ $permission->description }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($permission->module)
                                                <span class="badge bg-info">{{ $permission->module_name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $permission->action_type }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('permissions.show', $permission->id) }}" 
                                                   class="btn btn-outline-info" title="عرض">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('permissions.edit', $permission->id) }}" 
                                                   class="btn btn-outline-warning" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        onclick="deletePermission({{ $permission->id }})" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            <i class="fas fa-info-circle me-2"></i>
                                            لا توجد صلاحيات مسجلة
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($permissions->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $permissions->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deletePermission(id) {
    if (confirm('هل أنت متأكد من حذف هذه الصلاحية؟')) {
        fetch(`/permissions/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ أثناء حذف الصلاحية');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء حذف الصلاحية');
        });
    }
}
</script>
@endpush
@endsection
