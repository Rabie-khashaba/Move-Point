@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5 class="mb-0">بطاقتي</h5>
                        <div class="text-end">
                            <div class="text-muted fs-5">إجمالي العملاء: <strong class="fs-4">{{ $total }}</strong></div>
                            <div class="text-muted fs-5">عدد المتابعات: <strong class="fs-4">{{ $followedLeads }}</strong></div>
                        </div>
                    </div>
                    <div class="row g-2">
                        @foreach($statusList as $status)
                            @php
                                $count = $byStatus[$status] ?? 0;
                                $badge = 'secondary';
                                switch ($status) {
                                    case 'جديد': $badge = 'info'; break;
                                    case 'متابعة': $badge = 'primary'; break;
                                    case 'مقابلة': $badge = 'warning'; break;
                                    case 'مفاوضات': $badge = 'dark'; break;
                                    case 'مغلق': $badge = 'success'; break;
                                    case 'غير مهتم': $badge = 'secondary'; break;
                                    case 'خسر': $badge = 'danger'; break;
                                    case 'قديم': $badge = 'light'; break;
                                    case 'لم يرد': $badge = 'secondary'; break;
                                }
                            @endphp
                            <div class="col-6">
                                <div class="d-flex justify-content-between px-2 py-1 border rounded">
                                    <span class="text-muted small">{{ $status }}</span>
                                    <span class="badge bg-{{ $badge }}">{{ $count }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">عملائي</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>الهاتف</th>
                                    <th>المحافظة</th>
                                    <th>المصدر</th>
                                    <th>الحالة</th>
                                    <th>تاريخ الإضافة</th>
                                    <th class="text-end">تفاصيل</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leads as $lead)
                                    <tr>
                                        <td>{{ $lead->name }}</td>
                                        <td><a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a></td>
                                        <td>{{ $lead->governorate?->name ?? '-' }}</td>
                                        <td>{{ $lead->source?->name ?? '-' }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'جديد' => 'info',
                                                    'متابعة' => 'primary',
                                                    'مقابلة' => 'warning',
                                                    'مفاوضات' => 'dark',
                                                    'مغلق' => 'success',
                                                    'غير مهتم' => 'secondary',
                                                    'خسر' => 'danger',
                                                    'قديم' => 'light',
                                                    'لم يرد' => 'secondary',
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$lead->status] ?? 'secondary' }}">{{ $lead->status }}</span>
                                        </td>
                                        <td>
                                            {{ $lead->created_at ? $lead->created_at->format('Y-m-d H:i') : '-' }}
                                        </td>
                                        <td class="text-end">
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('leads.show', $lead->id) }}">عرض</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div>
                        @if(isset($leads) && method_exists($leads, 'links'))
                            {{ $leads->links('pagination::bootstrap-5') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


