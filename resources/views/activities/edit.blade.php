@extends('layouts.table-layout')

@section('title', 'تعديل النشاط')

@section('content')
<div class="card p-4">
    <h2 class="mb-4">تعديل النشاط</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.activities.update', $activity->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">اسم النشاط</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name', $activity->name) }}">
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">نوع النشاط</label>
            <select name="type" class="form-select" required>
                <option value="">-- اختر نوع النشاط --</option>
                <option value="تعليمي" {{ $activity->type == 'تعليمي' ? 'selected' : '' }}>تعليمي</option>
                <option value="تحفيظ القرآن" {{ $activity->type == 'تحفيظ القرآن' ? 'selected' : '' }}>تحفيظ القرآن</option>
                <option value="ترفيهي" {{ $activity->type == 'ترفيهي' ? 'selected' : '' }}>ترفيهي</option>
                <option value="تربوي" {{ $activity->type == 'تربوي' ? 'selected' : '' }}>تربوي</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="class_section_id" class="form-label">الشعبة</label>
            <select name="class_section_id" class="form-select" required>
                @foreach($classSections as $section)
                    <option value="{{ $section->id }}" {{ $activity->class_section_id == $section->id ? 'selected' : '' }}>
                        {{ $section->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.activities.index') }}" class="btn btn-secondary">رجوع</a>
            <button type="submit" class="btn btn-primary">تحديث</button>
        </div>
    </form>
</div>
@endsection
