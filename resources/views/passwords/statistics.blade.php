@extends('layouts.app')

@section('title', 'إحصائيات طلبات إعادة تعيين كلمة المرور')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">إحصائيات طلبات إعادة تعيين كلمة المرور</h3>
                    <div class="card-tools">
                        <a href="{{ route('passwords.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> العودة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Summary Cards -->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $stats['total'] }}</h3>
                                    <p>إجمالي الطلبات</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-list"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $stats['pending'] }}</h3>
                                    <p>في الانتظار</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $stats['sent'] }}</h3>
                                    <p>تم الإرسال</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-paper-plane"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $stats['completed'] }}</h3>
                                    <p>مكتمل</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">توزيع الطلبات حسب الحالة</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="statusChart" style="height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">إحصائيات شهرية</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="monthlyChart" style="height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">آخر النشاطات</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>التاريخ</th>
                                                    <th>الاسم</th>
                                                    <th>رقم الهاتف</th>
                                                    <th>الحالة</th>
                                                    <th>نوع المستخدم</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(\App\Models\PasswordResetRequest::with(['user'])->latest()->take(10)->get() as $request)
                                                <tr>
                                                    <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                                    <td>{{ $request->name }}</td>
                                                    <td>{{ $request->phone }}</td>
                                                    <td>
                                                        @switch($request->status)
                                                            @case('pending')
                                                                <span class="badge badge-warning">في الانتظار</span>
                                                                @break
                                                            @case('sent')
                                                                <span class="badge badge-info">تم الإرسال</span>
                                                                @break
                                                            @case('completed')
                                                                <span class="badge badge-success">مكتمل</span>
                                                                @break
                                                            @case('failed')
                                                                <span class="badge badge-danger">فشل</span>
                                                                @break
                                                        @endswitch
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-{{ $request->user->type === 'representative' ? 'success' : ($request->user->type === 'supervisor' ? 'warning' : 'info') }}">
                                                            {{ $request->user->type === 'representative' ? 'مندوب' : ($request->user->type === 'supervisor' ? 'مشرف' : 'موظف') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['في الانتظار', 'تم الإرسال', 'مكتمل', 'فشل'],
            datasets: [{
                data: [
                    {{ $stats['pending'] }},
                    {{ $stats['sent'] }},
                    {{ $stats['completed'] }},
                    {{ $stats['failed'] }}
                ],
                backgroundColor: [
                    '#ffc107',
                    '#17a2b8',
                    '#28a745',
                    '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Monthly Chart (placeholder - you can enhance this with actual monthly data)
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
            datasets: [{
                label: 'طلبات إعادة تعيين كلمة المرور',
                data: [12, 19, 3, 5, 2, 3],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endsection
