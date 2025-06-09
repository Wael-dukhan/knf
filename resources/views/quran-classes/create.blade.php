@extends('layouts.app2')

@section('title', __('messages.create_quran_class'))

@section('content')
<div class="page-wrapper">
    <div class="container mt-5">
        <h2>{{ __('messages.create_quran_class') }}</h2>

        <form action="{{ route('quran-classes.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">
                    {{ __('messages.class_name') }} <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="quran_level_id" class="form-label">
                    {{ __('messages.choose_quran_level') }} <span class="text-danger">*</span>
                </label>
                <select name="quran_level_id" id="quran_level_id"
                        class="form-control @error('quran_level_id') is-invalid @enderror" required>
                    <option value="">{{ __('messages.choose_quran_level') }}</option>
                    @foreach($quranLevels as $level)
                        <option value="{{ $level->id }}" {{ old('quran_level_id') == $level->id ? 'selected' : '' }}>
                            {{ $level->name }}
                        </option>
                    @endforeach
                </select>
                @error('quran_level_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="teacher_id" class="form-label">
                    {{ __('messages.teacher') }} <span class="text-danger">*</span>
                </label>
                <select name="teacher_id" id="teacher_id"
                        class="form-control @error('teacher_id') is-invalid @enderror" required>
                    <option value="">{{ __('messages.select_teacher') }}</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
                @error('teacher_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <a href="{{ route('quran-classes.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('messages.create') }}</button>
        </form>
    </div>
</div>
@endsection
