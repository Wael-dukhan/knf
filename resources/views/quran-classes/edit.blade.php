@extends('layouts.app2')

@section('content')
<div class="page-wrapper">
    <div class="container mt-5">
        <h2>{{ __('quran_levels.edit_quran_class') }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>{{ __('messages.validation_errors') }}</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('quran-classes.update', $quranClass->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- الاسم (عربي أو إنجليزي) --}}
        <div class="form-group mb-3">
            <label for="name">{{ __('quran_levels.class_name') }}</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $quranClass->name) }}" required>
        </div>

        {{-- اختيار المستوى القرآني --}}
        <div class="form-group mb-3">
            <label for="quran_level_id">{{ __('messages.quran_level') }}</label>
            <select name="quran_level_id" id="quran_level_id" class="form-control" required>
                <option value="">{{ __('messages.select_level') }}</option>
                @foreach ($quranLevel as $level)
                    <option value="{{ $level->id }}" {{ old('quran_level_id', $quranClass->quran_level_id) == $level->id ? 'selected' : '' }}>
                        {{ $level->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- اختيار المعلم --}}
        <div class="form-group mb-3">
            <label for="teacher_id">{{ __('messages.teacher') }}</label>
            <select name="teacher_id" id="teacher_id" class="form-control" required>
                <option value="">{{ __('messages.select_teacher') }}</option>
                @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ old('teacher_id', $quranClass->teacher_id) == $teacher->id ? 'selected' : '' }}>
                        {{ $teacher->name }}
                    </option>
                @endforeach
            </select>
        </div>

            
        {{-- تاريخ البداية --}}
        <div class="mb-3">
            <label for="start_date" class="form-label">
                {{ __('messages.start_date') }} <span class="text-danger">*</span>
            </label>
            <input type="date" name="start_date" id="start_date"
                class="form-control @error('start_date') is-invalid @enderror"
                value="{{ old('start_date', isset($quranClass) ? $quranClass->start_date : '') }}"
                required>
            @error('start_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- تاريخ النهاية --}}
        <div class="mb-3">
            <label for="end_date" class="form-label">
                {{ __('messages.end_date') }} <span class="text-danger">*</span>
            </label>
            <input type="date" name="end_date" id="end_date"
                class="form-control @error('end_date') is-invalid @enderror"
                value="{{ old('end_date', isset($quranClass) ? $quranClass->end_date : '') }}"
                required>
            @error('end_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        {{-- الوصف (عربي أو إنجليزي) --}}
        <div class="form-group mb-3">
            <label for="description">{{ __('messages.description') }}</label>
            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $quranClass->description) }}</textarea>
        </div>

        <a href="{{ route('quran-levels.show' , $quranClass->quranLevel->id) }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
        <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
    </form>
    </div>
</div>
@endsection
