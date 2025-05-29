@extends('layouts.table-layout')

@section('title', 'قائمة الأنشطة')

@section('content')
<div class="card p-4">
    <h2 class="mb-4">قائمة الأنشطة</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.activities.create') }}" class="btn btn-primary mb-3">إضافة نشاط جديد</a>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>اسم النشاط</th>
                    <th>نوع النشاط</th>
                    <th>الشعبة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $index => $activity)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $activity->name }}</td>
                        <td>{{ $activity->type }}</td>
                        <td>{{ $activity->classSection->name ?? '-' }}</td>
                        <td>
                            <a href="{{ route('admin.activities.edit', $activity->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                            <form action="{{ route('admin.activities.destroy', $activity->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">لا توجد أنشطة حالياً</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
