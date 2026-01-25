@extends('layouts.app')

@section('title', 'إنشاء دور')

@section('content')
<div class="nxl-content">
    <div class="content-area" data-scrollbar-target="#psScrollbarInit">

        <div class="content-area-body p-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form id="createRoleForm" action="{{ route('roles.store') }}" method="POST">
                        @csrf
                        <!-- اسم الدور -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">اسم الدور</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="أدخل اسم الدور" required>
                        </div>

                        <!-- الأذونات -->
                        <x-permission-selector 
                            :permissions="$permissions" 
                            :selectedPermissions="old('permission_ids', [])" />

                        <!-- أزرار الحفظ والإلغاء -->
                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                            <button type="submit" class="btn btn-primary">حفظ الدور</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
