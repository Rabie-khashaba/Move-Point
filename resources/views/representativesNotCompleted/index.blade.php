@extends('layouts.app')

@section('title', 'المندوبين')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">المندوبين</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">المندوبين</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex d-md-none">
                    <a href="javascript:void(0)" class="page-header-right-close-toggle">
                        <i class="feather-arrow-left me-2"></i>
                        <span>رجوع</span>
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="feather-filter"></i>
                    </a>

                     <a href="{{ route('representatives-not-completed.export') }}" class="btn btn-success">
                        <i class="feather-download me-2"></i>تصدير Excel
                    </a>
                    
                    @can('create_representatives_no')
                    <a href="{{ route('representatives-not-completed.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>إضافة مندوب</span>
                    </a>
                    @endcan
                </div>
            </div>
            <div class="d-md-none d-flex align-items-center">
                <a href="javascript:void(0)" class="page-header-right-open-toggle">
                    <i class="feather-align-right fs-20"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->

     <!-- Statistics Cards -->
    <div id="collapseOne" class="accordion-collapse show  collapse page-header-collapse mb-4">
        <div class="accordion-body pb-2">
            <div class="row">
                <div class="col-xxl-2 col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-text avatar-xl rounded">
                                        <i class="feather-users"></i>
                                    </div>
                                    <a href="javascript:void(0);" class="fw-bold d-block">
                                        <span class="d-block">الاجمالي</span>
                                        <span class="fs-24 fw-bolder d-block" id="totalLeads">{{$totalNotCompleted}}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-text avatar-xl rounded bg-primary">
                                        <i class="feather-user-check"></i>
                                    </div>
                                    <a href="javascript:void(0);" class="fw-bold d-block text-blue">
                                        <span class="d-block">الاشخاص لديهم اوراق ناقصه</span>
                                        <span class="fs-24 fw-bolder d-block" id="activeLeads">{{$missingDocsCount}}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-text avatar-xl rounded bg-info">
                                        <i class="feather-user-plus"></i>
                                    </div>
                                    <a href="javascript:void(0);" class="fw-bold d-block text-black">
                                        <span class="d-block">لم يحضر التدريب</span>
                                        <span class="fs-24 fw-bolder d-block" id="qualifiedLeads">{{$notTrainedCount}}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-2 col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-text avatar-xl rounded bg-danger">
                                        <i class="feather-user-minus"></i>
                                    </div>
                                    <a href="javascript:void(0);" class="fw-bold d-block text-red">
                                        <span class="d-block">حضر التدريب</span>
                                        <span class="fs-24 fw-bolder d-block" id="inactiveLe ads">{{$trainedCount}}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-2 col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-text avatar-xl rounded bg-warning">
                                        <i class="feather-user-plus"></i>
                                    </div>
                                    <a href="javascript:void(0);" class="fw-bold d-block text-black">
                                        <span class="d-block">جاهز  للعمل</span>
                                        <span class="fs-24 fw-bolder d-block" id="qualifiedLeads">{{$readyToWorkCount}}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Filter Collapse -->
    <div class="collapse show" id="filterCollapse">
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('representatives-not-completed.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" class="form-control" placeholder="البحث في المندوبين..." value="{{ request('search') }}">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">من تاريخ</label>
                        <input type="date"
                               name="date_from"
                               class="form-control {{ request('date_from') ? 'filter-active' : '' }}"
                               value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date"
                               name="date_to"
                               class="form-control {{ request('date_to') ? 'filter-active' : '' }}"
                               value="{{ request('date_to') }}">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">الشركة</label>
                        <select name="company_id" class="form-control">
                            <option value="">جميع الشركات</option>
                            @foreach(\App\Models\Company::where('is_active', true)->get() as $company)
                                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">المستند الناقص</label>
                        <select name="missing_doc" class="form-control">
                            <option value="">كل الأوراق</option>
                            @foreach(\App\Models\Representative::requiredDocs() as $doc)
                                <option value="{{ $doc }}" {{ request('missing_doc') == $doc ? 'selected' : '' }}>
                                    {{ $doc }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    
                    <div class="col-md-2">
                        <label class="form-label">الاوراق</label>
                        <select name="docs" class="form-control">
                            <option value="">جميع الحالات</option>
                            <option value="NotCompleted" {{ request('docs') === 'NotCompleted' ? 'selected' : '' }}>أوراق ناقصه </option>
                            <option value="completed" {{ request('docs') === 'completed' ? 'selected' : '' }}>أوراق مكتمله</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">التدريب</label>
                        <select name="training" class="form-control">
                            <option value="">جميع الحالات</option>
                            <option value="attended" {{ request('training') === 'attended' ? 'selected' : '' }}>حضر التدريب</option>
                            <option value="not_attended" {{ request('training') === 'not_attended' ? 'selected' : '' }}>لم يحضر التدريب</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">الجاهزية للعمل</label>
                        <select name="ready" class="form-control">
                            <option value="">جميع الحالات</option>
                            <option value="ready" {{ request('ready') === 'ready' ? 'selected' : '' }}>جاهز للعمل</option>
                            <option value="not_ready" {{ request('ready') === 'not_ready' ? 'selected' : '' }}> غير جاهز للعمل</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">استلام الاوراق</label>
                        <select name="document_received" class="form-control">
                            <option value="">جميع الحالات</option>
                            <option value="received" {{ request('document_received') === 'received' ? 'selected' : '' }}>تم الاستلام</option>
                            <option value="pending" {{ request('document_received') === 'pending' ? 'selected' : '' }}> لم  يتم  استلام الاوراق</option>
                        </select>
                    </div>



                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('representatives-not-completed.index') }}" class="btn btn-light">مسح</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">قائمة المندوبين</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($representatives->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>اسم المندوب</th>
                                            <th>رقم التليفون</th>
                                            <th>الشركة التي يعمل بها</th>
                                            <th>الأوراق الناقصه</th>
                                            <th>التدريب</th>
                                            <th>إيصالات  الايداع</th>
                                            <th>استلام الاوراق</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($representatives as $representative)
                                            @php
                                                // حضّر الـ notes لكل مندوب — ضبط التوقيت واستخراج اسم المنشئ
                                                $notes = $representative->notes->map(function($n) {
                                                    return [
                                                        'note' => $n->note,
                                                        // اضبط المنطقة الزمنية إلى Africa/Cairo ونسّق التاريخ
                                                        'created_at' => $n->created_at->setTimezone('Africa/Cairo')->format('Y-m-d H:i'),
                                                        'user' => $n->createdBy->name ?? 'غير معروف',
                                                    ];
                                                });
                                            @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-sm rounded-circle bg-primary me-3">
                                                        <i class="feather-user"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">
                                                            <a href="{{ route('representatives-not-completed.show', $representative->id) }}">
                                                                {{ $representative->name }}
                                                            </a>
                                                            
                                                        </h6>

                                                        <small class="text-muted">رقم البطاقة: {{ $representative->national_id ?? 'غير محدد' }}</small>
                                                        @if($representative->delivery_deposits_count == 7 && count($representative->missingDocs()) == 0 && $representative->is_training == 1)
                                                                <p class="text-success ">جاهز للتحويل كمندوب فعلي</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="feather-phone me-2 text-muted"></i>
                                                    <a href="tel:{{ $representative->phone }}" class="text-decoration-none">{{ $representative->phone }}</a>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $representative->company->name ?? 'غير محدد' }}</span>
                                            </td>
                                            <td>
                                                @if(count($representative->missingDocs()) > 0)
                                                    @foreach($representative->missingDocs() as $doc)
                                                            <span>{{ $doc }}</span><br>
                                                    @endforeach
                                                @else
                                                    <span class="badge bg-success">كل الأوراق مكتملة</span>
                                                @endif
                                            </td>

                                            <td>
                                                <span class="badge {{ $representative->is_training ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $representative->is_training ? 'تم الحضور' : 'لم يحضر' }}
                                                </span>
                                             </td>
                                            <td>

                                                <span class="badge bg-primary">{{ $representative->delivery_deposits_count }}</span>
                                            </td>
                                                                                        <td>
    @if($representative->documents_received === 'received')
        <span class="text-success fw-bold">
            تم استلام الأوراق
        </span>
    @else
        <span class="text-danger fw-bold">
            لم يتم  الاستلام
        </span>
    @endif
</td>

                                            <td>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @can('view_representatives_no')
                                                        @if( count($representative->missingDocs()) == 0 && $representative->is_training == 1  )
                                                            <button type="button"
                                                                    class="btn btn-sm btn-warning"
                                                                    title="رسالة المخزن"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#interviewModal"
                                                                    data-id="{{ $representative->id }}">
                                                                <i class="feather-clock"></i>
                                                            </button>
                                                        @else
                                                            <button type="button"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    title="رسالة المخزن"
                                                                    onclick="alert('الورق غير مكتمل ')">
                                                                <i class="feather-external-link"></i>
                                                            </button>
                                                        @endif
                                                    @endcan
                                                    @can('view_representatives_btnStore')
                                                     <button type="button"
                                                                    class="btn btn-sm btn-warning"
                                                                    title="رسالة المخزن"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#interviewModal"
                                                                    data-id="{{ $representative->id }}">
                                                                <i class="feather-clock"></i>
                                                            </button>
                                                    @endcan
                                                    @can('edit_representatives_no')
                                                       {{-- <button type="button"
                                                                class="btn btn-sm btn-success"
                                                                title="تحويل لمندوب فعلي"
                                                                data-id="{{ $representative->id }}"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#ReactiveModal">
                                                            <i class="feather-user-check"></i>
                                                        </button> --}}
                                                        
                                                        
                                                         <button type="button"
                                                                class="btn btn-sm btn-success"
                                                                title="تحويل لعميل فعلي"
                                                                data-id="{{ $representative->id }}"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#ConvertToActiveModal{{ $representative->id }}">
                                                            <i class="feather-user-check"></i>
                                                        </button>
                                                    @endcan


                                                    @can('edit_representatives_no')
                                                            <button type="button"
                                                                    class="btn btn-sm btn-info"
                                                                    title="إرسال بيانات التدريب"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#SendMessageTrainingModal"
                                                                    data-id="{{ $representative->id }}"
                                                                    data-name="{{ $representative->name }}">
                                                                <i class="feather-send"></i>
                                                            </button>
                                                        @endcan


                                                    @can('edit_trainings')
                                                        <form method="post" action="{{ route('representatives-not-completed.toggleTraining',$representative->id) }}" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="is_training" value="{{ $representative->is_training ? 0 : 1 }}">
                                                            <button type="submit"
                                                                    class="btn btn-sm {{ $representative->is_training ? 'btn-success' : 'btn-danger' }}"
                                                                    title="حضور التدريب">
                                                                <i class="feather-check-circle"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                    
                                                    <form method="POST"
      action="{{ route('representatives.toggleDocumentsStatus', $representative->id) }}"
      class="d-inline">
    @csrf
    @method('PUT')

    <button type="submit"
            class="btn btn-sm {{ $representative->documents_status === 'received' ? 'btn-success' : 'btn-primary' }}"
            title="تغيير حالة استلام الورق">
        @if($representative->documents_status === 'received')
            <i class="feather-check-circle"></i>
        @else
            <i class="feather-clock"></i>
        @endif
    </button>
</form>

                                                    @can('edit_representatives_no')
                                                            <button type="button"
                                                                    class="btn btn-sm btn-info"
                                                                    title="إرسال ملاحظات"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#SendNotesModal"
                                                                    data-id="{{ $representative->id }}"
                                                                    data-name="{{ $representative->name }}"
                                                                    data-phone="{{ $representative->phone }}"
                                                                    data-gov="{{ $representative->governorate->name ?? 'غير محدد' }}"
                                                                    data-notes='@json($notes)'>
                                                                <i class="feather-flag"></i>
                                                            </button>

                                                        @endcan



                                                </div>
                                            </td>

                                        </tr>


                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($representatives->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $representatives->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-text avatar-xl mx-auto mb-3">
                                    <i class="feather-user-plus"></i>
                                </div>
                                <h5>لا توجد مندوبين</h5>
                                <p class="text-muted">ابدأ بإضافة أول مندوب.</p>
                                <a href="{{ route('representatives-not-completed.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>إضافة مندوب
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Representative Modal -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferModalLabel">تغيير مشرف المندوب</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="transferForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المندوب</label>
                        <input type="text" class="form-control" id="representativeName" readonly>
                        <input type="hidden" id="representativeId" name="representative_id">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">المشرف الحالي</label>
                        <input type="text" class="form-control" id="currentSupervisor" readonly>
                    </div>

                                         <div class="mb-3">
                         <label class="form-label">المحافظة</label>
                         <select id="filterGovernorate" class="form-control">
                             <option value="">جميع المحافظات</option>
                             @foreach(\App\Models\Governorate::all() as $governorate)
                                 <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                             @endforeach
                         </select>
                     </div>

                                           <div class="mb-3">
                          <label class="form-label">المنطقة</label>
                          <select id="filterLocation" class="form-control">
                              <option value="">جميع المناطق</option>
                          </select>
                      </div>

                     <div class="mb-3">
                         <label class="form-label">المشرف الجديد <span class="text-danger">*</span></label>
                         <select name="new_supervisor_id" id="newSupervisorId" class="form-control" required>
                             <option value="">اختر المشرف الجديد</option>
                             @foreach(\App\Models\Supervisor::where('is_active', true)->get() as $supervisor)
                                 <option value="{{ $supervisor->id }}" data-governorate="{{ $supervisor->governorate_id }}" data-location="{{ $supervisor->location_id }}">
                                     {{ $supervisor->name }} - {{ $supervisor->location_name }}{{ $supervisor->governorate ? ' (' . $supervisor->governorate->name . ')' : '' }}
                                 </option>
                             @endforeach
                         </select>
                     </div>

                    <div class="mb-3">
                        <label class="form-label">سبب النقل</label>
                        <textarea name="reason" id="transferReason" class="form-control" rows="3" placeholder="أدخل سبب النقل (اختياري)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-save me-2"></i>
                        نقل المندوب
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="interviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">رساله مخزن</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="interviewForm"   method="POST">
                @csrf
               <input type="hidden" name="representative_id" ">
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
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الشركه</label>
                            <select name="company_id" id="company_id" class="form-control" required>
                                <option value="">اختر الشركه</option>
                                @foreach(\App\Models\Company::all() as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">تاريخ</label>
                            <input type="datetime-local" name="date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الرسالة</label>
                            <select name="message_id" id="interview_message_id" class="form-control select2" required>
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
                    <button type="submit" class="btn btn-warning"
                            onclick="return confirm('هل أنت متأكد ؟')">
                        <i class="feather-calendar me-1"></i>رساله بدء العمل
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- <div class="modal fade" id="ReactiveModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحويل لعميل فعلي</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="interviewForm"  method="POST">
                @csrf
                <input type="hidden" name="representative_id" >
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">المحافظة</label>
                            <select name="government_id" id="government_id" class="form-control" required>
                                <option value="">اختر المحافظة</option>
                                @foreach(\App\Models\Governorate::all() as $governorate)
                                    <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">المنطقة (اختياري)</label>
                            <select name="location_id" id="location_id" class="form-control">
                                <option value="">اختر المنطقة (اختياري)</option>
                            </select>
                            <small class="text-muted">يمكن اختيار المحافظة فقط أو المحافظة والمنطقة معاً</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الشركه</label>
                            <select name="company_id" id="companyW_id" class="form-control" required>
                                <option value="">اختر الشركه</option>
                                @foreach(\App\Models\Company::all() as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">تاريخ </label>
                            <input type="datetime-local" name="date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الرسالة</label>
                            <select name="message_id" id="message_id" class="form-control select2" required>
                                <option value="">اختر الرسالة</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">معاينة الرسالة</label>
                        <div id="messagePreview2" class="border rounded p-3 bg-light">
                            <small class="text-muted">اختر المحافظة لعرض الرسائل المتاحة</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success"
                            onclick="return confirm('هل أنت متأكد ؟')">
                        <i class="feather-calendar me-1"></i>تحويل لعميل فعلي
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> --}}

<!-- Convert To Active Modal -->
@foreach($representatives as $representative)
<div class="modal fade" id="ConvertToActiveModal{{ $representative->id }}" tabindex="-1" aria-labelledby="ConvertToActiveModalLabel{{ $representative->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ConvertToActiveModalLabel{{ $representative->id }}">تحويل لعميل فعلي</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('representatives-not-completed.transferToActive', $representative->id) }}" method="POST" id="convertToActiveForm{{ $representative->id }}">
                @csrf
                <input type="hidden" name="representative_id" value="{{ $representative->id }}">
                <input type="hidden" name="message_id" value="">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="feather-info me-2"></i>
                        سيتم تحويل <strong>{{ $representative->name }}</strong> إلى مندوب فعلي.
                    </div>
                    <div class="mb-3">
    <label class="form-label">تاريخ التحويل</label>
    <input type="date" name="date" class="form-control" value="{{ now()->format('Y-m-d') }}" id="dateInput" required>
    
    <!-- Hidden input to store the selected date -->
    <input type="hidden" name="date" id="hiddenDate" value="{{ now()->format('Y-m-d') }}"> 
</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">
                        <i class="feather-user-check me-2"></i>تحويل لعميل فعلي
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach


<div class="modal fade" id="SendMessageTrainingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ارسال بيانات التدريب</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="trainingForm" method="POST">
                @csrf
                <input type="hidden" name="representative_id" id="trainingRepId">
                <div class="modal-body">
                    <div class="row">
                        <!-- نفس الفورم بتاعك -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">نوع التدريب</label>
                            <select name="type" id="trainingType" class="form-control" required>
                                <option value="">اختر النوع</option>
                                <option value="أونلاين">تدريب Online</option>
                                <option value="في المقر">تدريب في المقر</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">المحافظة</label>
                            <select name="government_id" id="governmentT_id" class="form-control" required>
                                <option value="">اختر المحافظة</option>
                                @foreach(\App\Models\Governorate::all() as $governorate)
                                    <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">المنطقة (اختياري)</label>
                            <select name="location_id" id="locationT_id" class="form-control">
                                <option value="">اختر المنطقة (اختياري)</option>
                            </select>
                            <small class="text-muted">يمكن اختيار المحافظة فقط أو المحافظة والمنطقة معاً</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الشركه</label>
                            <select name="company_id" id="companyT_id" class="form-control" required>
                                <option value="">اختر الشركه</option>
                                @foreach(\App\Models\Company::all() as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach 
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">تاريخ التدريب</label>
                            <input type="datetime-local" name="date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الرسالة</label>
                            <select name="message_id" id="messageT_id" class="form-control select2" required>
                                <option value="">اختر الرسالة</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">معاينة الرسالة</label>
                        <div id="messagePreviewT" class="border rounded p-3 bg-light">
                            <small class="text-muted">اختر المحافظة لعرض الرسائل المتاحة</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-info"
                            onclick="return confirm('هل أنت متأكد ؟')">
                        <i class="feather-calendar me-1"></i> ارسال بيانات التدريب
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="SendNotesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ارسال ملاحظات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="sendNotesForm" method="POST">
                @csrf
                <input type="hidden" name="representative_id" id="repId">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">اسم المندوب</label>
                            <p id="repName" class="form-control-plaintext">--</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">المحافظة</label>
                            <p id="repGov" class="form-control-plaintext">--</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">رقم الهاتف</label>
                            <p id="repPhone" class="form-control-plaintext">--</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">الملاحظات السابقة</label>
                            <div id="previousNotes" class="border rounded p-2 bg-light" style="max-height: 200px; overflow-y: auto;">
                                <small class="text-muted">لا توجد ملاحظات سابقة</small>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">الملاحظات</label>
                            <textarea name="note" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-info">
                        <i class="feather-save me-1"></i> حفظ الملاحظة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



@endsection

@push('scripts')
<script>
// Set up CSRF token for all AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    // Handle modal data
    $('#transferModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var representativeId = button.data('representative-id');
        var representativeName = button.data('representative-name');
        var currentSupervisor = button.data('current-supervisor');

        $('#representativeId').val(representativeId);
        $('#representativeName').val(representativeName);
        $('#currentSupervisor').val(currentSupervisor);
        $('#newSupervisorId').val('');
        $('#transferReason').val('');
        $('#filterGovernorate').val('');
        $('#filterLocation').val('');

        // Show all supervisor options initially
        $('#newSupervisorId option').show();
    });

    // Handle governorate filter
    $('#filterGovernorate').on('change', function() {
        var governorateId = $(this).val();
        loadFilterLocations(governorateId);
        filterSupervisors();
    });

    // Handle location filter
    $('#filterLocation').on('change', function() {
        filterSupervisors();
    });

    function loadFilterLocations(governorateId) {
        if (!governorateId) {
            $('#filterLocation').empty().append('<option value="">جميع المقار</option>');
            return;
        }

        $.ajax({
            url: '/getlocations/' + governorateId,
            type: 'GET',
            success: function(response) {
                $('#filterLocation').empty().append('<option value="">جميع المقار</option>');

                response.forEach(function(location) {
                    var option = new Option(location.name, location.id, false, false);
                    $('#filterLocation').append(option);
                });
            },
            error: function() {
                $('#filterLocation').empty().append('<option value="">خطأ في تحميل المقار</option>');
            }
        });
    }

    function filterSupervisors() {
        var governorateId = $('#filterGovernorate').val();
        var locationId = $('#filterLocation').val();

        if (locationId) {
            // Load supervisors by location via AJAX
            $.ajax({
                url: '{{ route("supervisors.by-location", ":locationId") }}'.replace(':locationId', locationId),
                type: 'GET',
                success: function(response) {
                    $('#newSupervisorId').empty().append('<option value="">اختر المشرف الجديد</option>');

                    response.forEach(function(supervisor) {
                        var governorateInfo = supervisor.governorate ? ' (' + supervisor.governorate.name + ')' : '';
                        var option = new Option(
                            supervisor.name + ' - ' + (supervisor.location_name || 'غير محدد') + governorateInfo,
                            supervisor.id,
                            false,
                            false
                        );
                        $('#newSupervisorId').append(option);
                    });
                },
                error: function() {
                    $('#newSupervisorId').empty().append('<option value="">خطأ في تحميل المشرفين</option>');
                }
            });
        } else if (governorateId) {
            // Load supervisors by governorate via AJAX
            $.ajax({
                url: '{{ route("supervisors.by-governorate", ":governorateId") }}'.replace(':governorateId', governorateId),
                type: 'GET',
                success: function(response) {
                    $('#newSupervisorId').empty().append('<option value="">اختر المشرف الجديد</option>');

                    response.forEach(function(supervisor) {
                        var governorateInfo = supervisor.governorate ? ' (' + supervisor.governorate.name + ')' : '';
                        var option = new Option(
                            supervisor.name + ' - ' + (supervisor.location_name || 'غير محدد') + governorateInfo,
                            supervisor.id,
                            false,
                            false
                        );
                        $('#newSupervisorId').append(option);
                    });
                },
                error: function() {
                    $('#newSupervisorId').empty().append('<option value="">خطأ في تحميل المشرفين</option>');
                }
            });
        } else {
            // Show all supervisors if no filters are selected
            $('#newSupervisorId option').show();
        }

        // Reset selection if current selection is hidden
        var selectedOption = $('#newSupervisorId option:selected');
        if (selectedOption.length && selectedOption.is(':hidden')) {
            $('#newSupervisorId').val('');
        }
    }

    // Handle form submission
    $('#transferForm').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();

        // Disable button and show loading
        submitBtn.prop('disabled', true).html('<i class="feather-loader me-2"></i>جاري النقل...');

        $.ajax({
            url: '{{ route("supervisors.transfer-representative") }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم النقل بنجاح!',
                            text: response.message,
                            confirmButtonText: 'حسناً'
                        }).then(() => {
                            // Reload page to show updated data
                            location.reload();
                        });
                    } else {
                        alert('تم النقل بنجاح! ' + response.message);
                        location.reload();
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ!',
                            text: response.message || 'حدث خطأ أثناء نقل المندوب',
                            confirmButtonText: 'حسناً'
                        });
                    } else {
                        alert('خطأ! ' + (response.message || 'حدث خطأ أثناء نقل المندوب'));
                    }
                }
            },
            error: function(xhr) {
                var errorMessage = 'حدث خطأ أثناء نقل المندوب';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ!',
                        text: errorMessage,
                        confirmButtonText: 'حسناً'
                    });
                } else {
                    alert('خطأ! ' + errorMessage);
                }
            },
            complete: function() {
                // Re-enable button
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});

// Handle status change in follow-up modal
// document.addEventListener('DOMContentLoaded', function() {

//     const interviewModal = document.getElementById('interviewModal');

//     interviewModal.addEventListener('show.bs.modal', function (event) {
//         const button = event.relatedTarget; // الزرار اللي فتح المودال
//         const repId = button.getAttribute('data-id');

//         // حط id جوه الفورم
//         const hiddenInput = interviewModal.querySelector('input[name="representative_id"]');
//         hiddenInput.value = repId;

//         // حدّث الفورم عشان يبعت للـ route الصح
//         const form = interviewModal.querySelector('form');
//         form.action = "{{ route('representatives-not-completed.startRealRepresentative', ':id') }}"
//             .replace(':id', repId);
//     });
//     // Interview modal functionality
//     const interviewGovSelect = document.getElementById('interview_government_id');
//     const interviewLocSelect = document.getElementById('interview_location_id');
//     const interviewMessageSelect = document.getElementById('interview_message_id');
//     const messagePreview = document.getElementById('messagePreview');

//     // Load locations when governorate changes
//     if (interviewGovSelect && interviewLocSelect) {
//         interviewGovSelect.addEventListener('change', function() {
//             const governorateId = this.value;

//             if (!governorateId) {
//                 interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
//                 // Clear messages when governorate is cleared
//                 if (interviewMessageSelect) {
//                     interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
//                     messagePreview.innerHTML = '<small class="text-muted">اختر المحافظة لعرض الرسائل المتاحة</small>';
//                 }
//                 return;
//             }

//             fetch(`{{ url('getlocations') }}/${governorateId}`)
//                 .then(res => res.json())
//                 .then(data => {
//                     interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
//                     data.forEach(loc => {
//                         interviewLocSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
//                     });

//                     // Load messages for government only (without location)
//                     loadMessagesForGovernment(governorateId);
//                 })
//                 .catch(err => {
//                     console.error(err);
//                     interviewLocSelect.innerHTML = '<option value="">خطأ في تحميل البيانات</option>';
//                 });
//         });
//     }

//     // Load messages when location changes
//     if (interviewLocSelect && interviewMessageSelect) {
//         interviewLocSelect.addEventListener('change', function() {
//             const locationId = this.value;
//             const governorateId = interviewGovSelect.value;

//             if (!governorateId) {
//                 interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
//                 messagePreview.innerHTML = '<small class="text-muted">اختر المحافظة أولاً</small>';
//                 return;
//             }

//             if (!locationId) {
//                 // If location is cleared, load messages for government only
//                 loadMessagesForGovernment(governorateId);
//                 return;
//             }

//             // Load messages for specific government and location
//             loadMessagesForGovernmentAndLocation(governorateId, locationId);
//         });
//     }

//     // Function to load messages for government only
//     function loadMessagesForGovernment(governorateId) {
//         if (!interviewMessageSelect) return;

//         fetch(`{{ url('getmessagesStartWork') }}?government_id=${governorateId}`)
//             .then(res => res.json())
//             .then(data => {
//                 interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
//                 data.forEach(msg => {
//                     interviewMessageSelect.innerHTML += `<option value="${msg.id}">${msg.description}</option>`;
//                 });
//                 messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
//             })
//             .catch(err => {
//                 console.error(err);
//                 interviewMessageSelect.innerHTML = '<option value="">خطأ في تحميل الرسائل</option>';
//                 messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
//             });
//     }

//     // Function to load messages for specific government and location
//     function loadMessagesForGovernmentAndLocation(governorateId, locationId) {
//         if (!interviewMessageSelect) return;

//         fetch(`{{ url('getmessagesStartWork') }}?government_id=${governorateId}&location_id=${locationId}`)
//             .then(res => res.json())
//             .then(data => {
//                 interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
//                 data.forEach(msg => {
//                     interviewMessageSelect.innerHTML += `<option value="${msg.id}">${msg.description}</option>`;
//                 });
//                 messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
//             })
//             .catch(err => {
//                 console.error(err);
//                 interviewMessageSelect.innerHTML = '<option value="">خطأ في تحميل الرسائل</option>';
//                 messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
//             });
//     }

//     // Show message preview when message is selected
//     if (interviewMessageSelect && messagePreview) {
//         interviewMessageSelect.addEventListener('change', function() {
//             const messageId = this.value;

//             if (!messageId) {
//                 messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
//                 return;
//             }

//             fetch(`{{ url('getmessageStartWork') }}/${messageId}`)
//                 .then(res => res.json())
//                 .then(data => {
//                     messagePreview.innerHTML = `
//                          <div class="mb-2"><strong>الوصف:</strong> ${data.description}</div>
//                          ${data.google_map_url ? `<div><strong>رابط الخريطة:</strong> <a href="${data.google_map_url}" target="_blank">${data.google_map_url}</a></div>` : ''}
//                      `;
//                 })
//                 .catch(err => {
//                     console.error(err);
//                     messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسالة</small>';
//                 });
//         });
//     }


// });



document.addEventListener('DOMContentLoaded', function () {

var interviewModal = document.getElementById('interviewModal');
var interviewForm  = document.getElementById('interviewForm');

// عند فتح المودال: ضبط الفورم + id
interviewModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var id   = button.getAttribute('data-id');

    document.querySelector('input[name="representative_id"]').value = id;

    interviewForm.action = "{{ route('representatives-not-completed.startRealRepresentative', ':id') }}"
        .replace(':id', id);
});


const govSelect = document.getElementById('interview_government_id');
const locSelect = document.getElementById('interview_location_id');
const companySelect = document.getElementById('company_id');
const messageSelect = document.getElementById('interview_message_id');
const messagePreview = document.getElementById('messagePreview');


// تحميل الرسائل
function loadMessages(governorateId, locationId = null, companyId = null) {

    if (!messageSelect) return;

    let url = `{{ url('getmessagesStartWork') }}?government_id=${governorateId}`;
    if (locationId) url += `&location_id=${locationId}`;
    if (companyId) url += `&company_id=${companyId}`;

    fetch(url)
        .then(res => res.json())
        .then(data => {
            messageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
            data.forEach(msg => {
                messageSelect.innerHTML += `
                    <option value="${msg.id}">
                        ${msg.description}
                    </option>`;
            });

            messagePreview.innerHTML =
                '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
        })
        .catch(err => {
            messageSelect.innerHTML = '<option value="">خطأ في التحميل</option>';
            messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
        });
}

// تغيير المحافظة: تحميل المناطق فقط
if (govSelect) {
    govSelect.addEventListener('change', function () {
        const governorateId = this.value;

        if (!governorateId) {
            locSelect.innerHTML = '<option value="">اختر المنطقة</option>';
            messageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
            messagePreview.innerHTML = '<small class="text-muted">اختر المحافظة</small>';
            return;
        }

        fetch(`{{ url('getlocations') }}/${governorateId}`)
            .then(res => res.json())
            .then(data => {
                locSelect.innerHTML = '<option value="">اختر المنطقة (اختياري)</option>';
                data.forEach(loc => {
                    locSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
                });

                messageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                messagePreview.innerHTML =
                    '<small class="text-muted">اختر المنطقة والشركة لعرض الرسائل</small>';
            })
            .catch(err => {
                locSelect.innerHTML = '<option value="">خطأ في تحميل المناطق</option>';
            });
    });
}

// عند تغيير المنطقة
if (locSelect) {
    locSelect.addEventListener('change', function () {
        const governorateId = govSelect.value;
        const locationId = this.value;
        const companyId = companySelect.value;

        if (governorateId && companyId) {
            loadMessages(governorateId, locationId, companyId);
        }
    });
}

// عند تغيير الشركة
if (companySelect) {
    companySelect.addEventListener('change', function () {
        const governorateId = govSelect.value;
        const locationId = locSelect.value;
        const companyId = this.value;

        if (governorateId && companyId) {
            loadMessages(governorateId, locationId, companyId);
        }
    });
}

// عرض المعاينة عند اختيار الرسالة
if (messageSelect) {
    messageSelect.addEventListener('change', function () {
        const messageId = this.value;

        if (!messageId) {
            messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرضها</small>';
            return;
        }

        fetch(`{{ url('getmessageStartWork') }}/${messageId}`)
            .then(res => res.json())
            .then(data => {
                messagePreview.innerHTML = `
                    <div><strong>الرسالة:</strong></div>
                    <div class="mt-2">${data.description}</div>
                    ${data.google_map_url
                        ? `<div class="mt-2"><strong>الخريطة:</strong> <a target="_blank" href="${data.google_map_url}">${data.google_map_url}</a></div>`
                        : ''
                    }
                `;
            })
            .catch(err => {
                messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسالة</small>';
            });
    });
}

});

// document.addEventListener('DOMContentLoaded', function() {

//     const reactiveModal = document.getElementById('ReactiveModal');

//     reactiveModal.addEventListener('show.bs.modal', function (event) {
//         const button = event.relatedTarget;
//         const repId = button.getAttribute('data-id');

//         const form = reactiveModal.querySelector('form');
//         const hiddenInput = form.querySelector('input[name="representative_id"]');

//         hiddenInput.value = repId;

//         // حدّث الـ action بالرابط الصحيح
//         form.action = "{{ route('representatives-not-completed.transferToActive', ':id') }}"
//             .replace(':id', repId);
//     });

//     // Interview modal functionality
//     const interviewGovSelect = document.getElementById('government_id');
//     const interviewLocSelect = document.getElementById('location_id');
//     const interviewMessageSelect = document.getElementById('message_id');
//     const messagePreview = document.getElementById('messagePreview2');

//     // Load locations when governorate changes
//     if (interviewGovSelect && interviewLocSelect) {
//         interviewGovSelect.addEventListener('change', function() {
//             const governorateId = this.value;

//             if (!governorateId) {
//                 interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
//                 // Clear messages when governorate is cleared
//                 if (interviewMessageSelect) {
//                     interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
//                     messagePreview.innerHTML = '<small class="text-muted">اختر المحافظة لعرض الرسائل المتاحة</small>';
//                 }
//                 return;
//             }

//             fetch(`{{ url('getlocations') }}/${governorateId}`)
//                 .then(res => res.json())
//                 .then(data => {
//                     interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
//                     data.forEach(loc => {
//                         interviewLocSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
//                     });

//                     // Load messages for government only (without location)
//                     loadMessagesForGovernment(governorateId);
//                 })
//                 .catch(err => {
//                     console.error(err);
//                     interviewLocSelect.innerHTML = '<option value="">خطأ في تحميل البيانات</option>';
//                 });
//         });
//     }

//     // Load messages when location changes
//     if (interviewLocSelect && interviewMessageSelect) {
//         interviewLocSelect.addEventListener('change', function() {
//             const locationId = this.value;
//             const governorateId = interviewGovSelect.value;

//             if (!governorateId) {
//                 interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
//                 messagePreview.innerHTML = '<small class="text-muted">اختر المحافظة أولاً</small>';
//                 return;
//             }

//             if (!locationId) {
//                 // If location is cleared, load messages for government only
//                 loadMessagesForGovernment(governorateId);
//                 return;
//             }

//             // Load messages for specific government and location
//             loadMessagesForGovernmentAndLocation(governorateId, locationId);
//         });
//     }

//     // Function to load messages for government only
//     function loadMessagesForGovernment(governorateId) {
//         if (!interviewMessageSelect) return;

//         fetch(`{{ url('getmessagesStartWork') }}?government_id=${governorateId}`)
//             .then(res => res.json())
//             .then(data => {
//                 interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
//                 data.forEach(msg => {
//                     interviewMessageSelect.innerHTML += `<option value="${msg.id}">${msg.description}</option>`;
//                 });
//                 messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
//             })
//             .catch(err => {
//                 console.error(err);
//                 interviewMessageSelect.innerHTML = '<option value="">خطأ في تحميل الرسائل</option>';
//                 messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
//             });
//     }

//     // Function to load messages for specific government and location
//     function loadMessagesForGovernmentAndLocation(governorateId, locationId) {
//         if (!interviewMessageSelect) return;

//         fetch(`{{ url('getmessagesStartWork') }}?government_id=${governorateId}&location_id=${locationId}`)
//             .then(res => res.json())
//             .then(data => {
//                 interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
//                 data.forEach(msg => {
//                     interviewMessageSelect.innerHTML += `<option value="${msg.id}">${msg.description}</option>`;
//                 });
//                 messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
//             })
//             .catch(err => {
//                 console.error(err);
//                 interviewMessageSelect.innerHTML = '<option value="">خطأ في تحميل الرسائل</option>';
//                 messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
//             });
//     }

//     // Show message preview when message is selected
//     if (interviewMessageSelect && messagePreview) {
//         interviewMessageSelect.addEventListener('change', function() {
//             const messageId = this.value;

//             if (!messageId) {
//                 messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
//                 return;
//             }

//             fetch(`{{ url('getmessageStartWork') }}/${messageId}`)
//                 .then(res => res.json())
//                 .then(data => {
//                     messagePreview.innerHTML = `
//                          <div class="mb-2"><strong>الوصف:</strong> ${data.description}</div>
//                          ${data.google_map_url ? `<div><strong>رابط الخريطة:</strong> <a href="${data.google_map_url}" target="_blank">${data.google_map_url}</a></div>` : ''}
//                      `;
//                 })
//                 .catch(err => {
//                     console.error(err);
//                     messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسالة</small>';
//                 });
//         });
//     }


// });



document.addEventListener('DOMContentLoaded', function() {

const reactiveModal = document.getElementById('ReactiveModal');

{{-- reactiveModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const repId = button.getAttribute('data-id');

    const form = reactiveModal.querySelector('form');
    const hiddenInput = form.querySelector('input[name="representative_id"]');

    hiddenInput.value = repId;

    form.action = "{{ route('representatives-not-completed.transferToActive', ':id') }}"
        .replace(':id', repId);
});  --}}

// Elements
const interviewGovSelect = document.getElementById('government_id');
const interviewLocSelect = document.getElementById('location_id');
const interviewCompanySelect = document.getElementById('companyW_id'); // تمت إضافة الشركة
const interviewMessageSelect = document.getElementById('message_id');
const messagePreview = document.getElementById('messagePreview2');


// -----------------------------
// تحميل الرسائل
// -----------------------------
function loadMessages(governorateId, locationId = null, companyId = null) {

    if (!interviewMessageSelect) return;

    let url = `{{ url('getmessagesStartWork') }}?government_id=${governorateId}`;
    if (locationId) url += `&location_id=${locationId}`;
    if (companyId)  url += `&company_id=${companyId}`;

    fetch(url)
        .then(res => res.json())
        .then(data => {
            interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
            data.forEach(msg => {
                interviewMessageSelect.innerHTML += `<option value="${msg.id}">${msg.description}</option>`;
            });

            messagePreview.innerHTML =
                '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
        })
        .catch(() => {
            interviewMessageSelect.innerHTML = '<option value="">خطأ في تحميل الرسائل</option>';
            messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
        });
}


// -----------------------------
// عند تغيير المحافظة
// -----------------------------
if (interviewGovSelect) {
    interviewGovSelect.addEventListener('change', function() {

        const governorateId = this.value;

        if (!governorateId) {
            interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
            interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
            messagePreview.innerHTML = '<small class="text-muted">اختر المحافظة</small>';
            return;
        }

        fetch(`{{ url('getlocations') }}/${governorateId}`)
            .then(res => res.json())
            .then(data => {

                interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
                data.forEach(loc => {
                    interviewLocSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
                });

                interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                messagePreview.innerHTML =
                    '<small class="text-muted">اختر المنطقة والشركة لعرض الرسائل</small>';
            })
            .catch(() => {
                interviewLocSelect.innerHTML = '<option value="">خطأ في تحميل البيانات</option>';
            });
    });
}



// -----------------------------
// عند تغيير المنطقة
// -----------------------------
if (interviewLocSelect) {
    interviewLocSelect.addEventListener('change', function() {

        const governorateId = interviewGovSelect.value;
        const locationId    = this.value;
        const companyId     = interviewCompanySelect.value;

        if (governorateId && companyId) {
            loadMessages(governorateId, locationId, companyId);
        }
    });
}


// -----------------------------
// عند تغيير الشركة
// -----------------------------
if (interviewCompanySelect) {
    interviewCompanySelect.addEventListener('change', function() {

        const governorateId = interviewGovSelect.value;
        const locationId    = interviewLocSelect.value;
        const companyId     = this.value;

        if (governorateId && companyId) {
            loadMessages(governorateId, locationId, companyId);
        }
    });
}


// -----------------------------
// عرض المعاينة عند اختيار الرسالة
// -----------------------------
if (interviewMessageSelect && messagePreview) {

    interviewMessageSelect.addEventListener('change', function() {

        const messageId = this.value;

        if (!messageId) {
            messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
            return;
        }

        fetch(`{{ url('getmessageStartWork') }}/${messageId}`)
            .then(res => res.json())
            .then(data => {
                messagePreview.innerHTML = `
                     <div class="mb-2"><strong>الوصف:</strong> ${data.description}</div>
                     ${data.google_map_url ?
                        `<div><strong>رابط الخريطة:</strong>
                            <a href="${data.google_map_url}" target="_blank">${data.google_map_url}</a>
                        </div>`
                     : '' }
                `;
            })
            .catch(() => {
                messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسالة</small>';
            });
    });

}

});



// document.addEventListener('DOMContentLoaded', function () {
//     var trainingModal = document.getElementById('SendMessageTrainingModal');
//     var trainingForm  = document.getElementById('trainingForm');
//     var trainingRepId = document.getElementById('trainingRepId');

//     // تحديث البيانات عند فتح المودال
//     trainingModal.addEventListener('show.bs.modal', function (event) {
//         var button = event.relatedTarget;
//         var id   = button.getAttribute('data-id');
//         var name = button.getAttribute('data-name');

//         // hidden input
//         trainingRepId.value = id;

//         // تعديل الفورم action
//         trainingForm.action = "{{ route('representatives-not-completed.send_message_training', ':id') }}".replace(':id', id);
//     });

//     const interviewGovSelect = document.getElementById('governmentT_id');
//     const interviewLocSelect = document.getElementById('locationT_id');
//     const interviewMessageSelect = document.getElementById('messageT_id');
//     const messagePreview = document.getElementById('messagePreviewT');
//     const trainingTypeSelect = document.getElementById('trainingType');

//     // Function to load messages
//     function loadMessages(governorateId, locationId = null, type = null) {
//         if (!interviewMessageSelect) return;

//         let url = `{{ url('getmessagesTraining') }}?government_id=${governorateId}`;
//         if (locationId) url += `&location_id=${locationId}`;
//         if (type) url += `&type=${type}`;

//         fetch(url)
//             .then(res => res.json())
//             .then(data => {
//                 interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
//                 data.forEach(msg => {
//                     let optionText = msg.type === "online" ? msg.description_training : msg.description_location;
//                     interviewMessageSelect.innerHTML += `<option value="${msg.id}" data-type="${msg.type}">${optionText}</option>`;
//                 });
//                 messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
//             })
//             .catch(err => {
//                 console.error(err);
//                 interviewMessageSelect.innerHTML = '<option value="">خطأ في تحميل الرسائل</option>';
//                 messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
//             });
//     }

//     // عند تغيير المحافظة
//     if (interviewGovSelect) {
//         interviewGovSelect.addEventListener('change', function() {
//             const governorateId = this.value;
//             if (!governorateId) {
//                 interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
//                 interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
//                 messagePreview.innerHTML = '<small class="text-muted">اختر المحافظة لعرض الرسائل المتاحة</small>';
//                 return;
//             }

//             fetch(`{{ url('getlocations') }}/${governorateId}`)
//                 .then(res => res.json())
//                 .then(data => {
//                     interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
//                     data.forEach(loc => {
//                         interviewLocSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
//                     });

//                     const type = trainingTypeSelect.value;
//                     loadMessages(governorateId, null, type);
//                 })
//                 .catch(err => {
//                     console.error(err);
//                     interviewLocSelect.innerHTML = '<option value="">خطأ في تحميل البيانات</option>';
//                 });
//         });
//     }

//     // عند تغيير المنطقة
//     if (interviewLocSelect) {
//         interviewLocSelect.addEventListener('change', function() {
//             const locationId = this.value;
//             const governorateId = interviewGovSelect.value;
//             const type = trainingTypeSelect.value;

//             if (!governorateId) return;

//             loadMessages(governorateId, locationId || null, type);
//         });
//     }

//     // عند تغيير النوع
//     if (trainingTypeSelect) {
//         trainingTypeSelect.addEventListener('change', function() {
//             const governorateId = interviewGovSelect.value;
//             const locationId = interviewLocSelect.value;
//             const type = this.value;

//             if (governorateId) {
//                 loadMessages(governorateId, locationId || null, type);
//             }
//         });
//     }

//     // عرض المعاينة عند اختيار الرسالة
//     if (interviewMessageSelect) {
//         interviewMessageSelect.addEventListener('change', function() {
//             const messageId = this.value;
//             if (!messageId) {
//                 messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
//                 return;
//             }

//             fetch(`{{ url('getmessageTraining') }}/${messageId}`)
//                 .then(res => res.json())
//                 .then(data => {
//                     if (data.type === "أونلاين") {
//                         messagePreview.innerHTML = `
//                             <div class="mb-2"><strong>الوصف:</strong> ${data.description_training}</div>
//                             ${data.link_training ? `<div><strong>الرابط:</strong> <a href="${data.link_training}" target="_blank">${data.link_training}</a></div>` : ''}
//                         `;
//                     } else {
//                         messagePreview.innerHTML = `
//                             <div class="mb-2"><strong>الوصف:</strong> ${data.description_location}</div>
//                             ${data.google_map_url ? `<div><strong>رابط الخريطة:</strong> <a href="${data.google_map_url}" target="_blank">${data.google_map_url}</a></div>` : ''}
//                         `;
//                     }
//                 })
//                 .catch(err => {
//                     console.error(err);
//                     messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسالة</small>';
//                 });
//         });
//     }
// });


document.addEventListener('DOMContentLoaded', function () {
    var trainingModal = document.getElementById('SendMessageTrainingModal');
    var trainingForm  = document.getElementById('trainingForm');
    var trainingRepId = document.getElementById('trainingRepId');

    // تحديث البيانات عند فتح المودال
    trainingModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id   = button.getAttribute('data-id');
        var name = button.getAttribute('data-name');

        // hidden input
        trainingRepId.value = id;

        // تعديل الفورم action
        trainingForm.action = "{{ route('representatives-not-completed.send_message_training', ':id') }}".replace(':id', id);
    });

    const interviewGovSelect = document.getElementById('governmentT_id');
    const interviewLocSelect = document.getElementById('locationT_id');
    const interviewMessageSelect = document.getElementById('messageT_id');
    const messagePreview = document.getElementById('messagePreviewT');
    const trainingTypeSelect = document.getElementById('trainingType');
    const companySelect = document.getElementById('companyT_id');

    // Function to load messages
    function loadMessages(governorateId, locationId = null, type = null , companyId = null) {
        if (!interviewMessageSelect) return;

        let url = `{{ url('getmessagesTraining') }}?government_id=${governorateId}`;
        if (locationId) url += `&location_id=${locationId}`;
        if (type) url += `&type=${type}`;
        if (companyId) url += `&company_id=${companyId}`;


        fetch(url)
            .then(res => res.json())
            .then(data => {
                interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                data.forEach(msg => {
                    let optionText = msg.type === "online" ? msg.description_training : msg.description_location;
                    interviewMessageSelect.innerHTML += `<option value="${msg.id}" data-type="${msg.type}">${optionText}</option>`;
                });
                messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
            })
            .catch(err => {
                console.error(err);
                interviewMessageSelect.innerHTML = '<option value="">خطأ في تحميل الرسائل</option>';
                messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
            });
    }

    // عند تغيير المحافظة
    if (interviewGovSelect) {
        interviewGovSelect.addEventListener('change', function() {
            const governorateId = this.value;

            if (!governorateId) {
                interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
                interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                messagePreview.innerHTML = '<small class="text-muted">اختر المحافظة والمنطقة والشركة لعرض الرسائل المتاحة</small>';
                return;
            }

            // تحميل المناطق فقط
            fetch(`{{ url('getlocations') }}/${governorateId}`)
                .then(res => res.json())
                .then(data => {
                    interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
                    data.forEach(loc => {
                        interviewLocSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
                    });

                    // مسح الرسائل وإضافة رسالة توضيحية
                    interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                    messagePreview.innerHTML = '<small class="text-muted">يجب اختيار المنطقة والشركة لعرض الرسائل</small>';
                })
                .catch(err => {
                    console.error(err);
                    interviewLocSelect.innerHTML = '<option value="">خطأ في تحميل البيانات</option>';
                });
        });
    }

    // عند تغيير المنطقة
    if (interviewLocSelect) {
        interviewLocSelect.addEventListener('change', function() {
            const locationId = this.value;
            const governorateId = interviewGovSelect.value;
            const companyId = companySelect.value;
            const type = trainingTypeSelect.value;

            // التحقق من اختيار الثلاثة معًا
            if (governorateId && locationId && companyId) {
                loadMessages(governorateId, locationId, type, companyId);
            } else if (!locationId) {
                interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                messagePreview.innerHTML = '<small class="text-muted">يجب اختيار المنطقة والشركة لعرض الرسائل</small>';
            } else if (!companyId) {
                interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                messagePreview.innerHTML = '<small class="text-muted">يجب اختيار الشركة لعرض الرسائل</small>';
            }
        });
    }

    // عند تغيير النوع
    if (trainingTypeSelect) {
        trainingTypeSelect.addEventListener('change', function() {
            const governorateId = interviewGovSelect.value;
            const locationId = interviewLocSelect.value;
            const companyId = companySelect.value;
            const type = this.value;

            // التحقق من اختيار الثلاثة معًا
            if (governorateId && locationId && companyId) {
                loadMessages(governorateId, locationId, type, companyId);
            } else {
                interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                messagePreview.innerHTML = '<small class="text-muted">يجب اختيار المحافظة والمنطقة والشركة لعرض الرسائل</small>';
            }
        });
    }

    // عند تغيير الشركة
    if (companySelect) {
        companySelect.addEventListener('change', function() {
            const governorateId = interviewGovSelect.value;
            const locationId = interviewLocSelect.value;
            const type = trainingTypeSelect.value;
            const companyId = this.value;

            // التحقق من اختيار الثلاثة معًا
            if (governorateId && locationId && companyId) {
                loadMessages(governorateId, locationId, type, companyId);
            } else if (!companyId) {
                interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                messagePreview.innerHTML = '<small class="text-muted">يجب اختيار الشركة لعرض الرسائل</small>';
            } else if (!governorateId || !locationId) {
                interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                messagePreview.innerHTML = '<small class="text-muted">يجب اختيار المحافظة والمنطقة والشركة لعرض الرسائل</small>';
            }
        });
    }

    // عرض المعاينة عند اختيار الرسالة
    if (interviewMessageSelect) {
        interviewMessageSelect.addEventListener('change', function() {
            const messageId = this.value;
            if (!messageId) {
                messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                return;
            }

            fetch(`{{ url('getmessageTraining') }}/${messageId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.type === "أونلاين") {
                        messagePreview.innerHTML = `
                            <div class="mb-2"><strong>الوصف:</strong> ${data.description_training}</div>
                            ${data.link_training ? `<div><strong>الرابط:</strong> <a href="${data.link_training}" target="_blank">${data.link_training}</a></div>` : ''}
                        `;
                    } else {
                        messagePreview.innerHTML = `
                            <div class="mb-2"><strong>الوصف:</strong> ${data.description_location}</div>
                            ${data.google_map_url ? `<div><strong>رابط الخريطة:</strong> <a href="${data.google_map_url}" target="_blank">${data.google_map_url}</a></div>` : ''}
                        `;
                    }
                })
                .catch(err => {
                    console.error(err);
                    messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسالة</small>';
                });
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    var sendNotesModal = document.getElementById('SendNotesModal');
    sendNotesModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;

    var id = button.getAttribute('data-id');
    var name = button.getAttribute('data-name');
    var phone = button.getAttribute('data-phone');
    var gov = button.getAttribute('data-gov');

    document.getElementById('repId').value = id;
    document.getElementById('repName').innerText = name;
    document.getElementById('repPhone').innerText = phone;
    document.getElementById('repGov').innerText = gov;

    document.getElementById('sendNotesForm').action = "{{ route('representatives-not-completed.save-note', ':id') }}".replace(':id', id);

        var notes = button.getAttribute('data-notes'); // مثال: تخزن كل الملاحظات كـ JSON string
        var previousNotesDiv = document.getElementById('previousNotes');

        if(notes) {
            notes = JSON.parse(notes); // إذا كانت JSON string
            previousNotesDiv.innerHTML = ''; // امسح الافتراضي
            notes.forEach(function(note){
                var noteDiv = document.createElement('div');
                noteDiv.classList.add('mb-2', 'p-2', 'border', 'rounded', 'bg-white');
               noteDiv.innerHTML = `
                    <div>${note.note}</div>
                    <small class="text-muted">${note.user} - ${note.created_at}</small>
                `;
                previousNotesDiv.appendChild(noteDiv);
            });
        } else {
            previousNotesDiv.innerHTML = '<small class="text-muted">لا توجد ملاحظات سابقة</small>';
        }
});
});
</script>



<script>
    // Get the elements
    const dateInput = document.getElementById('dateInput');
    const hiddenDateInput = document.getElementById('hiddenDate');
    
    // Update the hidden input when the date changes
    dateInput.addEventListener('change', function() {
        hiddenDateInput.value = dateInput.value;
    });
</script>






@endpush
