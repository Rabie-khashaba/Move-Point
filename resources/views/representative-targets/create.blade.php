@extends('layouts.app')

@section('title', 'إضافة هدف جديد للمندوبين')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">إضافة هدف جديد للمندوبين</h3>
                    <div class="card-tools">
                        <a href="{{ route('representative-targets.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> العودة للقائمة
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('representative-targets.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>السنة <span class="text-danger">*</span></label>
                                    <select name="year" class="form-control" required>
                                        @for($year = now()->year - 2; $year <= now()->year + 1; $year++)
                                            <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>الشهر <span class="text-danger">*</span></label>
                                    <select name="month" class="form-control" required>
                                        @for($month = 1; $month <= 12; $month++)
                                            <option value="{{ $month }}" {{ $month == $currentMonth ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::create()->month($month)->locale('ar')->monthName }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>عدد المندوبين المطلوب <span class="text-danger">*</span></label>
                                    <input type="number" name="representatives_count" 
                                           class="form-control @error('representatives_count') is-invalid @enderror"
                                           value="{{ old('representatives_count') }}" 
                                           step="1" min="0" placeholder="عدد المندوبين" required>
                                    @error('representatives_count')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>المبلغ الثابت للمكافأة <span class="text-danger">*</span></label>
                                    <input type="number" name="bonus_amount" 
                                           class="form-control @error('bonus_amount') is-invalid @enderror"
                                           value="{{ old('bonus_amount') }}" 
                                           step="0.01" min="0" placeholder="مبلغ المكافأة" required>
                                    @error('bonus_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>ملاحظات</label>
                                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                              rows="3" placeholder="ملاحظات إضافية">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> حفظ الهدف
                            </button>
                            <a href="{{ route('representative-targets.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
