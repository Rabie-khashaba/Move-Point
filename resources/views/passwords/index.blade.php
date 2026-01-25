@extends('layouts.app')

@section('title', 'إدارة طلبات إعادة تعيين كلمة المرور')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">إدارة طلبات إعادة تعيين كلمة المرور</h3>
                    <div class="card-tools">
                        {{--
                        <a href="{{ route('passwords.statistics') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-bar"></i> الإحصائيات
                        </a>
                        --}}
                    </div>
                </div>
                <div class="card-body">
                    <!-- Status Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="btn-group" role="group">
                                <a href="{{ route('passwords.index') }}" 
                                   class="btn btn-{{ $status === 'all' ? 'primary' : 'outline-primary' }}">
                                    الكل
                                </a>
                                <a href="{{ route('passwords.index', ['status' => 'pending']) }}" 
                                   class="btn btn-{{ $status === 'pending' ? 'warning' : 'outline-warning' }}">
                                    في الانتظار
                                </a>
                                <a href="{{ route('passwords.index', ['status' => 'sent']) }}" 
                                   class="btn btn-{{ $status === 'sent' ? 'info' : 'outline-info' }}">
                                    تم الإرسال
                                </a>
                                <a href="{{ route('passwords.index', ['status' => 'completed']) }}" 
                                   class="btn btn-{{ $status === 'completed' ? 'success' : 'outline-success' }}">
                                    مكتمل
                                </a>
                                <a href="{{ route('passwords.index', ['status' => 'failed']) }}" 
                                   class="btn btn-{{ $status === 'failed' ? 'danger' : 'outline-danger' }}">
                                    فشل
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Password Reset Requests Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>رقم الهاتف</th>
                                    <th>نوع المستخدم</th>
                                    <th>الحالة</th>
                                    <th>آخر تحديث</th>
                                    <th>تاريخ الإرسال</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($resetRequests as $user)
                                @php
                                    $displayName = $user->employee->name ?? $user->supervisor->name ?? $user->representative->name ?? '—';
                                    $statusValue = $user->last_reset_status ?? ($user->forget_password ? 'pending' : 'completed');
                                @endphp
                                <tr>
                                    <td>{{ $displayName }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>
                                        <span class="badge badge-{{ $user->type === 'representative' ? 'success' : ($user->type === 'supervisor' ? 'warning' : 'info') }}">
                                            {{ $user->type === 'representative' ? 'مندوب' : ($user->type === 'supervisor' ? 'مشرف' : 'موظف') }}
                                        </span>
                                    </td>
                                    <td>
                                        @switch($statusValue)
                                            @case('pending')
                                                <span class="badge badge-warning">في الانتظار</span>
                                                @break
                                            @case('sent')
                                                <span class="badge badge-info">تم الإرسال</span>
                                                @break
                                            @case('resent')
                                                <span class="badge badge-info">أُعيد الإرسال</span>
                                                @break
                                            @case('failed')
                                                <span class="badge badge-danger">فشل</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ $statusValue }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ optional($user->updated_at)->format('Y-m-d H:i') }}</td>
                                    <td>{{ optional($user->last_reset_at)->format('Y-m-d H:i') ?: '-' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if($statusValue === 'sent' || $statusValue === 'resent')
                                                <form action="{{ route('passwords.complete', $user->id) }}" 
                                                      method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" 
                                                            title="تحديد كمكتمل">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if(in_array($statusValue, ['pending', 'failed']))
                                                <form action="{{ route('passwords.reset', $user->id) }}" 
                                                      method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning" 
                                                            title="إرسال عبر واتساب">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">لا توجد طلبات إعادة تعيين كلمة مرور</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $resetRequests->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-refresh every 30 seconds for pending requests
    @if($status === 'pending')
    setInterval(function() {
        location.reload();
    }, 30000);
    @endif
});
</script>
@endsection
