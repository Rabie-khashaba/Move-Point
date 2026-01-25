@extends('layouts.app')

@section('content')
    <div class="nxl-content">
        <!-- [ Page Header ] start -->
        <div class="page-header d-flex align-items-center justify-content-between">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">العملاء المحتملين</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">الرئيسية</a></li>
                </ul>
            </div>

            <div class="page-header-right d-flex align-items-center gap-2">
                @can('create_representatives')
                    @if($lead->status == 'مقابلة' && !$lead->representative)
                        <a href="{{ route('representatives.create', ['lead_id' => $lead->id]) }}" class="btn btn-success">
                            <i class="feather-user-plus me-2"></i> إضافة كمندوب
                        </a>
                        <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-icon btn-light-brand">
                            <i class="feather-edit"></i> تعديل
                        </a>
                    @endif
                @endcan
                {{--
                <div class="dropdown">
                    <a class="btn btn-icon btn-light-brand" data-bs-toggle="dropdown">
                        <i class="feather-more-horizontal"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="javascript:void(0);" class="dropdown-item" onclick="updateStatus('lost')">
                            <i class="feather-user-x me-2"></i> تحويل إلى خاسر
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item" onclick="updateStatus('closed')">
                            <i class="feather-check-circle me-2"></i> تحويل إلى مغلق
                        </a>
                        <div class="dropdown-divider"></div>

                        <form action="{{ route('leads.destroy', $lead->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item" onclick="return confirm('هل أنت متأكد؟')">
                                <i class="feather-trash-2 me-2"></i> حذف العميل
                            </button>
                        </form>

                    </div>
                    --}}
            </div>
        </div>
    </div>
    <!-- [ Page Header ] end -->

    <!-- Tabs -->
    <div class="bg-white py-3 border-bottom mb-3">
        <ul class="nav nav-tabs nav-tabs-custom-style" id="leadTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profileTab" type="button" role="tab">الملف الشخصي</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="followup-tab" data-bs-toggle="tab" data-bs-target="#followupTab" type="button" role="tab">المتابعات</button>
            </li>
        </ul>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Profile Tab -->
        <div class="tab-pane fade show active" id="profileTab" role="tabpanel">
            <div class="card card-body lead-info">
                <h5 class="mb-3">معلومات العميل المحتمل</h5>
                <div class="row mb-3">
                    <div class="col-lg-2 fw-medium">الاسم</div>
                    <div class="col-lg-10">{{ $lead->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-2 fw-medium">الهاتف</div>
                    <div class="col-lg-10"><a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a></div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-2 fw-medium">المحافظة</div>
                    <div class="col-lg-10">{{ $lead->governorate->name ?? 'غير متوفر' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-2 fw-medium">المصدر</div>
                    <div class="col-lg-10">{{ $lead->source->name ?? 'غير متوفر' }}</div>
                </div>
                @php
                    $statusColors = [
                        'متابعة' => 'primary',
                        'غير مهتم' => 'secondary',
                        'عمل مقابلة' => 'info',
                        'مقابلة' => 'warning',
                        'مفاوضات' => 'warning',
                        'مغلق' => 'success',
                        'خسر' => 'danger',
                        'جديد' => 'dark',
                        'قديم' => 'dark',
                    ];
                    $statusColor = $statusColors[$lead->status] ?? 'dark';
                @endphp
                <div class="row mb-3">
                    <div class="col-lg-2 fw-medium">الحالة</div>
                    <div class="col-lg-10">
                        <span class="badge bg-{{ $statusColor }}">{{ $lead->status }}</span>
                        @if($lead->updated_at != $lead->created_at)
                            <br><small class="text-muted">آخر تحديث: {{ $lead->updated_at->diffForHumans() }}</small>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-2 fw-medium">مسؤول</div>
                    <div class="col-lg-10">
                        {{ $lead->assignedTo->employee->name ?? 'غير معين' }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-2 fw-medium">تاريخ الإنشاء</div>
                    <div class="col-lg-10">{{ $lead->created_at->format('d M, Y') }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-2 fw-medium">آخر تحديث</div>
                    <div class="col-lg-10">{{ $lead->updated_at->format('d M, Y H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Follow-up Tab -->
        <div class="tab-pane fade" id="followupTab" role="tabpanel">
            <div class="card card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h5>المتابعات</h5>
                    @can('create_leads')
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addFollowupModal">
                            <i class="feather-plus me-1"></i> إضافة متابعة
                        </button>
                    @endcan
                </div>

                @if($lead->followUps->count() > 0)
                    @foreach($lead->followUps as $followup)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    @if($followup->reason)
                                        <strong class="text-primary">{{ $followup->reason->name ?? 'غير محدد' }}</strong>
                                    @endif
                                    @if($followup->next_follow_up)
                                        <br><small class="text-muted">المتابعة القادمة: {{ \Carbon\Carbon::parse($followup->next_follow_up)->format('d M, Y') }}</small>
                                    @endif
                                    <br><small class="text-info">بواسطة: {{ $followup->user->employee->name ?? 'غير محدد' }}</small>
                                </div>
                                <small class="text-muted">{{ $followup->created_at->diffForHumans() }}</small>
                            </div>

                            @if($followup->notes)
                                <div class="mt-2">
                                    <strong>الملاحظات:</strong>
                                    <div class="mt-1" style="white-space: pre-line;">{{ $followup->notes }}</div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <h6>لا توجد متابعات حتى الآن!</h6>
                        @can('create_followups')
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addFollowupModal">
                                إضافة متابعة
                            </button>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>
    </div>

    <!-- Add Follow-up Modal -->
    <div class="modal fade" id="addFollowupModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('leads.addFollowup', $lead->id) }}" method="POST" id="followupForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">إضافة متابعة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">تاريخ المتابعة القادمة</label>
                            <input type="date" name="next_follow_up" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">تحديث حالة العميل</label>
                            <select name="status" class="form-control" id="statusSelect">
                                <option value="">-- الحفاظ على الحالة الحالية --</option>
                                <option value="متابعة">متابعة</option>
                                <option value="غير مهتم">غير مهتم</option>
                                <option value="مقابلة">مقابلة</option>
                                <option value="لم يرد">لم يرد</option>
                                <option value="شفت مسائي">شفت مسائي</option>
                                <option value="بدون وسيلة نقل">بدون وسيلة نقل</option>

                            </select>
                        </div>



                        <div class="mb-3" id="reasonField" style="display: none;">
                            <label class="form-label">السبب *</label>
                            <select name="reason_id" class="form-control" required>
                                <option value="">اختر السبب</option>
                                @foreach(\App\Models\Reason::all() as $reason)
                                    <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">هذا الحقل مطلوب عند اختيار "غير مهتم"</small>
                        </div>



                        <div class="mb-3">
                            <label class="form-label">الملاحظات</label>
                            <textarea name="notes" class="form-control" rows="4" placeholder="أدخل الملاحظات"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">إضافة متابعة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Interview Modal -->
    <div class="modal fade" id="interviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">جدولة مقابلة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="interviewForm" action="{{ route('interviews.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">المحافظة</label>
                                <select name="government_id" id="interview_government_id" class="form-control" required>
                                    <option value="">اختر المحافظة</option>
                                    @foreach(\App\Models\Governorate::all() as $governorate)
                                        <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">المنطقة (اختياري)</label>
                                <select name="location_id" id="interview_location_id" class="form-control">
                                    <option value="">اختر المنطقة (اختياري)</option>
                                </select>
                                <small class="text-muted">يمكن اختيار المحافظة فقط أو المحافظة والمنطقة معاً</small>
                            </div>

                            {{-- -المشرفين --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">المشرفين</label>
                                <select name="supervisor_id" id="interview_supervisor_id" class="form-control" required>
                                    <option value="">اختر المشرف</option>
                                    <!-- سيتم ملؤه ديناميكيًا -->
                                </select>
                                <small class="text-muted">اختر مشرف المنطقة المختارة</small>
                            </div>




                            <div class="col-md-6 mb-3">
                                <label class="form-label">تاريخ المقابلة</label>
                                <input type="datetime-local" name="date_interview" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الرسالة</label>
                                <select name="message_id" id="interview_message_id" class="form-control" required>
                                    <option value="">اختر الرسالة</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">معاينة الرسالة</label>
                            <div id="messagePreview" class="border rounded p-3 bg-light">
                                <small class="text-muted">اختر المحافظة لعرض الرسائل المتاحة</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">
                            <i class="feather-calendar me-1"></i> جدولة المقابلة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

<script>
    function updateStatus(status) {
        if (confirm('هل أنت متأكد أنك تريد تغيير الحالة إلى ' + status + '?')) {
            fetch('{{ route("leads.updateStatus", $lead->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: status })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) location.reload();
                });
        }
    }

    // Handle status change in follow-up modal
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('statusSelect');
        const reasonField = document.getElementById('reasonField');
        const followupModal = document.getElementById('addFollowupModal');
        const interviewModal = document.getElementById('interviewModal');

        if (statusSelect && reasonField) {
            // Function to handle status change
            function handleStatusChange() {
                const selectedValue = statusSelect.value;

                // Hide reason field first
                reasonField.style.display = 'none';

                // Reset required attributes
                const reasonSelect = reasonField.querySelector('select');
                if (reasonSelect) reasonSelect.required = false;

                if (selectedValue === 'غير مهتم') {
                    // Show reason field only for غير مهتم
                    reasonField.style.display = 'block';
                    if (reasonSelect) reasonSelect.required = true;
                } else if (selectedValue === 'مقابلة') {
                    // Close followup modal and open interview modal
                    if (followupModal) {
                        const bsModal = bootstrap.Modal.getInstance(followupModal);
                        if (bsModal) bsModal.hide();
                    }

                    // Open interview modal after a short delay
                    setTimeout(() => {
                        if (interviewModal) {
                            const bsInterviewModal = new bootstrap.Modal(interviewModal);
                            bsInterviewModal.show();
                        }
                    }, 300);
                }
            }

            // Add event listener for status change
            statusSelect.addEventListener('change', handleStatusChange);

            // Also handle on modal show to reset state
            if (followupModal) {
                followupModal.addEventListener('show.bs.modal', function() {
                    reasonField.style.display = 'none';
                    const reasonSelect = reasonField.querySelector('select');

                    if (reasonSelect) {
                        reasonSelect.required = false;
                        reasonSelect.value = '';
                    }
                    statusSelect.value = '';
                });
            }

            // Form validation
            const followupForm = document.getElementById('followupForm');
            if (followupForm) {
                followupForm.addEventListener('submit', function(e) {
                    const currentStatus = statusSelect.value;

                    if (currentStatus === 'غير مهتم' && !reasonField.querySelector('select')?.value) {
                        e.preventDefault();
                        alert('يرجى اختيار السبب');
                        return false;
                    }
                });
            }
        }

        // Interview modal functionality
        const interviewGovSelect = document.getElementById('interview_government_id');
        const interviewLocSelect = document.getElementById('interview_location_id');
        const interviewMessageSelect = document.getElementById('interview_message_id');
        const messagePreview = document.getElementById('messagePreview');

        // Load locations when governorate changes
        if (interviewGovSelect && interviewLocSelect) {
            interviewGovSelect.addEventListener('change', function() {
                const governorateId = this.value;

                if (!governorateId) {
                    interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
                    // Clear messages when governorate is cleared
                    if (interviewMessageSelect) {
                        interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                        messagePreview.innerHTML = '<small class="text-muted">اختر المحافظة لعرض الرسائل المتاحة</small>';
                    }
                    return;
                }

                fetch(`{{ url('getlocations') }}/${governorateId}`)
                    .then(res => res.json())
                    .then(data => {
                        interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
                        data.forEach(loc => {
                            interviewLocSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
                        });

                        // Load messages for government only (without location)
                        loadMessagesForGovernment(governorateId);
                    })
                    .catch(err => {
                        console.error(err);
                        interviewLocSelect.innerHTML = '<option value="">خطأ في تحميل البيانات</option>';
                    });
            });
        }

        // Load messages when location changes
        if (interviewLocSelect && interviewMessageSelect) {
            interviewLocSelect.addEventListener('change', function() {
                const locationId = this.value;
                const governorateId = interviewGovSelect.value;

                if (!governorateId) {
                    interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                    messagePreview.innerHTML = '<small class="text-muted">اختر المحافظة أولاً</small>';
                    return;
                }

                if (!locationId) {
                    // If location is cleared, load messages for government only
                    loadMessagesForGovernment(governorateId);
                    return;
                }

                // Load messages for specific government and location
                loadMessagesForGovernmentAndLocation(governorateId, locationId);
            });
        }

        // Function to load messages for government only
        function loadMessagesForGovernment(governorateId) {
            if (!interviewMessageSelect) return;

            fetch(`{{ url('getmessages') }}?government_id=${governorateId}`)
                .then(res => res.json())
                .then(data => {
                    interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                    data.forEach(msg => {
                        interviewMessageSelect.innerHTML += `<option value="${msg.id}">${msg.description}</option>`;
                    });
                    messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                })
                .catch(err => {
                    console.error(err);
                    interviewMessageSelect.innerHTML = '<option value="">خطأ في تحميل الرسائل</option>';
                    messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
                });
        }

        // Function to load messages for specific government and location
        function loadMessagesForGovernmentAndLocation(governorateId, locationId) {
            if (!interviewMessageSelect) return;

            fetch(`{{ url('getmessages') }}?government_id=${governorateId}&location_id=${locationId}`)
                .then(res => res.json())
                .then(data => {
                    interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                    data.forEach(msg => {
                        interviewMessageSelect.innerHTML += `<option value="${msg.id}">${msg.description}</option>`;
                    });
                    messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                })
                .catch(err => {
                    console.error(err);
                    interviewMessageSelect.innerHTML = '<option value="">خطأ في تحميل الرسائل</option>';
                    messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
                });
        }

        // Show message preview when message is selected
        if (interviewMessageSelect && messagePreview) {
            interviewMessageSelect.addEventListener('change', function() {
                const messageId = this.value;

                if (!messageId) {
                    messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                    return;
                }

                fetch(`{{ url('getmessage') }}/${messageId}`)
                    .then(res => res.json())
                    .then(data => {
                        messagePreview.innerHTML = `
                         <div class="mb-2"><strong>الوصف:</strong> ${data.description}</div>
                         ${data.google_map_url ? `<div><strong>رابط الخريطة:</strong> <a href="${data.google_map_url}" target="_blank">${data.google_map_url}</a></div>` : ''}
                     `;
                    })
                    .catch(err => {
                        console.error(err);
                        messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسالة</small>';
                    });
            });
        }

        // Handle interview form submission
        const interviewForm = document.getElementById('interviewForm');
        if (interviewForm) {
            interviewForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert('تم جدولة المقابلة بنجاح!');
                            location.reload();
                        } else {
                            alert('حدث خطأ: ' + (data.message || 'خطأ غير معروف'));
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('حدث خطأ في الاتصال');
                    });
            });
        }


        function loadSupervisors(governorateId, locationId) {
            const supervisorSelect = document.getElementById('interview_supervisor_id');
            if (!supervisorSelect) return;

            if (!locationId) {
                supervisorSelect.innerHTML = '<option value="">اختر المشرف</option>';
                return;
            }

            fetch(`{{ url('get-supervisors') }}?government_id=${governorateId}&location_id=${locationId}`)
                .then(res => res.json())
                .then(data => {
                    supervisorSelect.innerHTML = '<option value="">اختر المشرف</option>';
                    data.forEach(sup => {
                        supervisorSelect.innerHTML += `<option value="${sup.id}">${sup.name}</option>`;
                    });
                })
                .catch(err => {
                    console.error(err);
                    supervisorSelect.innerHTML = '<option value="">خطأ في تحميل المشرفين</option>';
                });
        }

        // ضمن event change للمنطقة
        interviewLocSelect.addEventListener('change', function() {
            const locationId = this.value;
            const governorateId = interviewGovSelect.value;

            if (!locationId) {
                loadMessagesForGovernment(governorateId);
                document.getElementById('interview_supervisor_id').innerHTML = '<option value="">اختر المشرف</option>';
                return;
            }

            loadMessagesForGovernmentAndLocation(governorateId, locationId);
            loadSupervisors(governorateId, locationId);
        });



    });
</script>

