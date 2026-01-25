@extends('layouts.app')

@section('title', 'تفاصيل طلب إعادة تعيين كلمة المرور')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">تفاصيل طلب إعادة تعيين كلمة المرور #{{ $resetRequest->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('passwords.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> العودة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>معلومات المستخدم</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>الاسم</th>
                                    <td>{{ $resetRequest->name }}</td>
                                </tr>
                                <tr>
                                    <th>رقم الهاتف</th>
                                    <td>{{ $resetRequest->phone }}</td>
                                </tr>
                                <tr>
                                    <th>نوع المستخدم</th>
                                    <td>
                                        <span class="badge badge-{{ $resetRequest->user->type === 'representative' ? 'success' : ($resetRequest->user->type === 'supervisor' ? 'warning' : 'info') }}">
                                            {{ $resetRequest->user->type === 'representative' ? 'مندوب' : ($resetRequest->user->type === 'supervisor' ? 'مشرف' : 'موظف') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>معرف المستخدم</th>
                                    <td>{{ $resetRequest->user_id }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>معلومات الطلب</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>الحالة</th>
                                    <td>
                                        @switch($resetRequest->status)
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
                                </tr>
                                <tr>
                                    <th>تاريخ الطلب</th>
                                    <td>{{ $resetRequest->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>تاريخ الإرسال</th>
                                    <td>{{ $resetRequest->sent_at ? $resetRequest->sent_at->format('Y-m-d H:i:s') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>تاريخ الإكمال</th>
                                    <td>{{ $resetRequest->completed_at ? $resetRequest->completed_at->format('Y-m-d H:i:s') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>كلمة المرور الجديدة</h5>
                            <div class="alert alert-info">
                                <strong>كلمة المرور:</strong> {{ $resetRequest->new_password }}
                            </div>
                        </div>
                    </div>

                    @if($resetRequest->whatsapp_response)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>استجابة WhatsApp</h5>
                            <div class="alert alert-secondary">
                                <pre>{{ $resetRequest->whatsapp_response }}</pre>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>الإجراءات</h5>
                            <div class="btn-group" role="group">
                                @if($resetRequest->status === 'sent')
                                    <form action="{{ route('passwords.complete', $resetRequest->id) }}" 
                                          method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check"></i> تحديد كمكتمل
                                        </button>
                                    </form>
                                @endif
                                
                                @if(in_array($resetRequest->status, ['pending', 'failed']))
                                    <form action="{{ route('passwords.resend', $resetRequest->id) }}" 
                                          method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-redo"></i> إعادة إرسال
                                        </button>
                                    </form>
                                @endif
                                
                                <a href="{{ route('passwords.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> العودة للقائمة
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
