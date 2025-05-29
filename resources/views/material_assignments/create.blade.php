@extends('layouts.app2')

@section('title', __('Create Material Assignment'))

@section('content')
<div class="page-wrapper">
    <div class="container mt-5">

        <div class="card mb-4">
            <div class="card-body">
                <p class="mb-0">
                    <strong>{{ __('messages.school') }}:</strong> {{ $classSection->grade->school->name ?? '-' }}
                </p>
                <hr />
                <p class="mb-1">
                    <strong>{{ __('messages.grade_level') }}:</strong> {{ $classSection->grade->grade_level }}
                </p>
                <hr />
                <p class="mb-1">
                    <strong>{{ __('messages.grade') }}:</strong> {{ $classSection->grade->name }}
                </p>
                <hr />
                <p class="mb-1">
                    <strong>{{ __('messages.class_section') }}:</strong> {{ $classSection->name }}
                </p>
                <hr />
                <p class="">
                    <strong>{{ __('messages.material') }}:</strong> {{ $material->name }}
                </p>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('material-assignment.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="class_section_id" value="{{ $classSection->id }}">
                    <input type="hidden" name="material_id" value="{{ $material->id }}">

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="academic_year_id" class="form-label">
                                {{ __('messages.select_academic_year') }} <span class="text-danger">*</span>
                            </label>
                            <select name="academic_year_id" id="academic_year_id" class="form-select" required>
                                <option value="">{{ __('messages.select_academic_year') }}</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="term_id" class="form-label">
                                {{ __('messages.select_term') }} <span class="text-danger">*</span>
                            </label>
                            <select name="term_id" id="term_id" class="form-select" required>
                                <option value="">{{ __('messages.select_term') }}</option>
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}" {{ old('term_id') == $term->id ? 'selected' : '' }}>
                                        {{ $term->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="teacher_id" class="form-label">
                            {{ __('messages.select_teacher') }} <span class="text-danger">*</span>
                        </label>
                        <select name="teacher_id" id="teacher_id" class="form-select" required>
                            <option value="">{{ __('messages.select_teacher') }}</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex justify-content-between mt-4 column-gap-20">
                        <a href="{{ route('material-assignments.show', [$classSection->id]) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('messages.save') }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
