@extends('layouts.app')

@section('title', 'تدريبات المندوبين')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">تدريبات المندوبين</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المندوب</th>
                        <th>الهاتف</th>
                        <th>الحالة</th>
                        <th>تاريخ الإكمال</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trainings as $t)
                    <tr>
                        <td>{{ $t->id }}</td>
                        <td>{{ optional($t->representative)->name }}</td>
                        <td>{{ optional(optional($t->representative)->user)->phone }}</td>
                        <td>
                            <span class="badge {{ $t->is_completed ? 'bg-success' : 'bg-warning' }}">
                                {{ $t->is_completed ? 'مكتمل' : 'غير مكتمل' }}
                            </span>
                        </td>
                        <td>{{ $t->completed_at ? $t->completed_at->format('Y-m-d H:i') : '—' }}</td>
                        <td>
                            @can('edit_trainings')
                            <form method="post" action="{{ route('trainings.update', $t) }}" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="is_completed" value="{{ $t->is_completed ? 0 : 1 }}">
                                <button class="btn btn-sm {{ $t->is_completed ? 'btn-warning' : 'btn-success' }}">
                                    {{ $t->is_completed ? 'تعطيل' : 'إكمال' }}
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $trainings->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection


