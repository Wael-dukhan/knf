@extends('layouts.app2')

@section('content')
<div class="container">
    <h3>قائمة تعيينات المواد للمعلمين</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>الشعبة</th>
                <th>المادة</th>
                <th>المعلم</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assignments as $assignment)
                <tr>
                    <td>{{ $assignment->classSection->name ?? '-' }}</td>
                    <td>{{ $assignment->material->name ?? '-' }}</td>
                    <td>{{ $assignment->teacher->name ?? '-' }}</td>
                    <td>
                        <a href="{{ route('material-assignments.edit', $assignment->class_section_id) }}" class="btn btn-sm btn-primary">تعديل</a>

                        <form action="{{ route('material-assignments.destroy', $assignment->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('هل أنت متأكد من الحذف؟')" class="btn btn-sm btn-danger">حذف</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">لا توجد بيانات حالياً.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
