<div class="row">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-info-circle"></i> معلومات أساسية</h6>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-4"><strong>رقم الهاتف:</strong></div>
                    <div class="col-8">
                        <a href="{{ $log->whatsapp_link }}" target="_blank" class="text-success">
                            <i class="fab fa-whatsapp"></i> {{ $log->phone }}
                        </a>
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-4"><strong>الحالة:</strong></div>
                    <div class="col-8">
                        <span class="badge badge-{{ $log->status_badge_class }}">
                            {{ $log->status_text }}
                        </span>
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-4"><strong>الخدمة:</strong></div>
                    <div class="col-8">
                        <span class="badge badge-info">{{ $log->service }}</span>
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-4"><strong>المحاولات:</strong></div>
                    <div class="col-8">
                        <span class="badge badge-secondary">{{ $log->attempts }}</span>
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-4"><strong>تاريخ الإنشاء:</strong></div>
                    <div class="col-8">{{ $log->created_at->format('Y-m-d H:i:s') }}</div>
                </div>
                
                @if($log->sent_at)
                <div class="row mb-2">
                    <div class="col-4"><strong>تاريخ الإرسال:</strong></div>
                    <div class="col-8">{{ $log->sent_at->format('Y-m-d H:i:s') }}</div>
                </div>
                @endif
                
                @if($log->failed_at)
                <div class="row mb-2">
                    <div class="col-4"><strong>تاريخ الفشل:</strong></div>
                    <div class="col-8">{{ $log->failed_at->format('Y-m-d H:i:s') }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-cog"></i> معلومات تقنية</h6>
            </div>
            <div class="card-body">
                @if($log->ip_address)
                <div class="row mb-2">
                    <div class="col-4"><strong>عنوان IP:</strong></div>
                    <div class="col-8"><code>{{ $log->ip_address }}</code></div>
                </div>
                @endif
                
                @if($log->user_agent)
                <div class="row mb-2">
                    <div class="col-4"><strong>المتصفح:</strong></div>
                    <div class="col-8"><small>{{ Str::limit($log->user_agent, 50) }}</small></div>
                </div>
                @endif
                
                @if($log->response)
                <div class="row mb-2">
                    <div class="col-4"><strong>الاستجابة:</strong></div>
                    <div class="col-8">
                        <pre class="bg-light p-2 rounded"><code>{{ $log->response }}</code></pre>
                    </div>
                </div>
                @endif
                
                @if($log->error)
                <div class="row mb-2">
                    <div class="col-4"><strong>الخطأ:</strong></div>
                    <div class="col-8">
                        <div class="alert alert-danger p-2 mb-0">
                            <small>{{ $log->error }}</small>
                        </div>
                    </div>
                </div>
                @endif
                
                @if($log->metadata)
                <div class="row mb-2">
                    <div class="col-4"><strong>بيانات إضافية:</strong></div>
                    <div class="col-8">
                        <pre class="bg-light p-2 rounded"><code>{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="fas fa-comment"></i> نص الرسالة</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-light" style="white-space: pre-wrap; font-family: 'Courier New', monospace; font-size: 14px; line-height: 1.6;">
{{ $log->message }}
                </div>
            </div>
        </div>
    </div>
</div>

@if($log->canResend())
<div class="row mt-3">
    <div class="col-12 text-center">
        <button class="btn btn-success btn-lg" onclick="resendMessage({{ $log->id }})">
            <i class="fas fa-redo"></i> إعادة إرسال الرسالة
        </button>
    </div>
</div>
@endif
