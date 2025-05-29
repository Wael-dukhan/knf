@extends('layouts.table-layout')

@section('title', 'إضافة نشاط جديد')

@section('content')
<div class="card p-4">
    <h2 class="mb-4">إضافة نشاط جديد</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.activities.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">اسم النشاط</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">نوع النشاط</label>
            <select name="type" class="form-select" required>
                <option value="">-- اختر نوع النشاط --</option>
                <option value="تعليمي">تعليمي</option>
                <option value="تحفيظ القرآن">تحفيظ القرآن</option>
                <option value="ترفيهي">ترفيهي</option>
                <option value="تربوي">تربوي</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="class_section_id" class="form-label">الشعبة</label>
            <select name="class_section_id" class="form-select" required>
                <option value="">-- اختر الشعبة --</option>
                @foreach($classSections as $section)
                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.activities.index') }}" class="btn btn-secondary">رجوع</a>
            <button type="submit" class="btn btn-success">حفظ</button>
        </div>
    </form>
</div>
@endsection
