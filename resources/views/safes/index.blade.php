@extends('layouts.app')

@section('title', 'الخزن')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">الخزن</h3>
            @can('manage_safes')
            <form class="d-flex" method="post" action="{{ route('safes.store') }}">
                @csrf
                <input type="text" name="name" class="form-control form-control-sm" placeholder="اسم الخزنة" required>
                <input type="number" step="0.01" min="0" name="balance" class="form-control form-control-sm ms-2" placeholder="رصيد افتتاحي">
                <button class="btn btn-sm btn-primary ms-2">إنشاء</button>
            </form>
            @endcan
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>الرصيد</th>
                        <th>عدد الحركات</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($safes as $safe)
                    <tr>
                        <td>{{ $safe->name }}</td>
                        <td>{{ number_format($safe->balance, 2) }}</td>
                        <td>{{ $safe->transactions_count }}</td>
                        <td>
                            @can('manage_safes')
                            <form method="post" action="{{ route('safes.deposit', $safe) }}" class="d-inline">
                                @csrf
                                <input type="number" step="0.01" min="0.01" name="amount" class="form-control form-control-sm d-inline w-auto" placeholder="مبلغ">
                                <button class="btn btn-sm btn-success">إيداع</button>
                            </form>
                            <form method="post" action="{{ route('safes.withdraw', $safe) }}" class="d-inline ms-2">
                                @csrf
                                <input type="number" step="0.01" min="0.01" name="amount" class="form-control form-control-sm d-inline w-auto" placeholder="مبلغ">
                                <button class="btn btn-sm btn-warning">سحب</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $safes->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection


