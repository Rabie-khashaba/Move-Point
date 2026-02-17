@extends('layouts.app')

@section('title','استيراد Salary Records')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-body">
            <h5>استيراد ملف Excel (Sheet: Salary Records)</h5>

            @if(session('error'))
                @php
                    $importError = (string) session('error');
                    preg_match('/^\[([A-Z0-9\-]+)\]\s*(.*)$/u', $importError, $matches);
                    $errorCode = $matches[1] ?? null;
                    $errorMessage = $matches[2] ?? $importError;
                @endphp
                <div class="alert alert-danger">
                    @if($errorCode)
                        <div><strong>كود الخطأ:</strong> <span class="badge bg-danger">{{ $errorCode }}</span></div>
                    @endif
                    <div>{{ $errorMessage }}</div>
                </div>
            @endif
            @if($errors->any()) <div class="alert alert-danger">{{ implode(', ', $errors->all()) }}</div> @endif

            <form action="{{ route('salary-record1.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label>اختر ملف Excel</label>
                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                </div>

                <div class="mb-3">
                    <label>الشهر <span class="text-danger">*</span></label>
                    <input type="month" name="month" class="form-control" value="{{ now()->format('Y-m') }}" required>
                </div>

                <div class="mb-3">
                    <label>وضع الاستيراد</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="mode" id="append" value="append" checked>
                        <label class="form-check-label" for="append">إضافة</label>
                    </div>

                </div>

                <button class="btn btn-success">استيراد</button>
                <br>
                <a href="{{ route('salary-record1.index') }}" class="btn btn-light">إلغاء</a>
            </form>



           {{-- <h6>استيراد سريع من ملف موجود على السيرفر</h6>
            <hr>
            <form action="{{ route('salary-record1.import.server') }}" method="POST" class="d-flex gap-2">
                @csrf
                <select name="mode" class="form-select" style="width:220px;">
                    <option value="append">إضافة (Append)</option>
                    <option value="replace">استبدال (Replace)</option>
                </select>
                <button class="btn btn-info" onclick="return confirm('تأكيد استيراد الملف من /mnt/data ?')">استيراد من السيرفر</button>
            </form>

            --}}
        </div>
    </div>
</div>
@endsection
