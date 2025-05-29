@extends('layouts.app2')

@section('content')
<div class="page-wrapper">
    <div class="container mt-5">
        <h2>نقل الطالب إلى شعبة أخرى</h2>

        <form action="{{ route('student.class_sections.update', $student->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>الطالب:</label>
                <p>{{ $student->name }}</p>
            </div>

            <div class="form-group">
                <label>الشعبة الحالية:</label>
                <p>{{ $currentClassSection?->name ?? 'غير مسجل' }}</p>
            </div>

            <div class="form-group mt-3">
                <label for="class_section_id">اختر الشعبة الجديدة:</label>
                <select name="class_section_id" id="class_section_id" class="form-control" required>
                    @foreach($availableClassSections as $section)
                        <option value="{{ $section->id }}"
                            {{ $currentClassSection && $section->id == $currentClassSection->id ? 'disabled' : '' }}>
                            {{ $section->grade->name }} - {{ $section->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mt-4">
                <a href="{{ url()->previous() }}" class="btn btn-secondary">رجوع</a>
                <button type="submit" class="btn btn-primary">نقل الطالب</button>
            </div>
        </form>
    </div>
</div>
@endsection
