@extends('layouts.app2')

@section('content')
<div class="page-wrapper">
    <div class="container mt-5">
        <h2>إضافة شعبة دراسية جديدة</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.class_sections.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="school_id" class="form-label">
                    المدرسة <span class="text-danger">*</span>
                </label>
                <select name="school_id" id="school_id" class="form-select @error('school_id') is-invalid @enderror" required>
                    <option value="">-- اختر المدرسة --</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                    @endforeach
                </select>
                @error('school_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="grade_id" class="form-label">
                    الصف الدراسي <span class="text-danger">*</span>
                </label>
                <select name="grade_id" id="grade_id" class="form-select @error('grade_id') is-invalid @enderror" required>
                    <option value="">-- اختر الصف --</option>
                    @foreach($grades as $grade)
                        <option value="{{ $grade->id }}" {{ old('grade_id') == $grade->id ? 'selected' : '' }}>{{ $grade->name }}</option>
                    @endforeach
                </select>
                @error('grade_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">
                    اسم الشعبة <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <i class="feather-arrow-left"></i> {{ __('messages.back') }}
                </a>
                <button type="submit" class="btn btn-success">حفظ</button>
            </div>
        </form>
    </div>
</div>
@endsection
