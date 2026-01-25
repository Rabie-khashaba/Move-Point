@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>مزامنة الصلاحيات</h4>
                </div>
                <div class="card-body">
                    <p>سيتم إنشاء أو تحديث الصلاحيات التالية:</p>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>اسم الصلاحية</th>
                                    <th>الاسم المعروض</th>
                                    <th>الوصف</th>
                                    <th>الوحدة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($defaultPermissions as $permission)
                                <tr>
                                    <td>{{ $permission['name'] }}</td>
                                    <td>{{ $permission['display_name'] }}</td>
                                    <td>{{ $permission['description'] }}</td>
                                    <td>{{ $permission['module'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <form method="POST" action="{{ route('permissions.sync') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sync"></i> مزامنة الصلاحيات
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
