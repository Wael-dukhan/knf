@extends('layouts.app2')

@section('title', __('Create Grade'))

@section('content')
<div class="page-wrapper">
    <div class="container mt-5">
        <h2 class="mb-4">{{ __('messages.create_new_grade') }}</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.grades.store') }}" method="POST">
            @csrf

            {{-- حقل اختيار المدرسة --}}
            <div class="mb-3">
                <label for="school_id" class="form-label">
                    {{__('messages.school')}} <span class="text-danger">*</span>
                </label>
                <select name="school_id" id="school_id" class="form-select @error('school_id') is-invalid @enderror" required onchange="filterAcademicYears()">
                    <option value="">{{__('messages.select_school')}}</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
                @error('school_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- حقل اختيار السنة الدراسية --}}
            <div class="mb-3">
                <label for="academic_year_id" class="form-label">
                    {{__('messages.academic_year')}} <span class="text-danger">*</span>
                </label>
                <select name="academic_year_id" id="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                    <option value="">{{__('messages.select_academic_year')}}</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" data-school="{{ $year->school_id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                            {{ $year->name }}
                        </option>
                    @endforeach
                </select>
                @error('academic_year_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- حقل اختيار المرحلة الدراسية --}}
            <div class="mb-3">
                <label for="grade_level" class="form-label">
                    {{__('messages.grade_level')}} <span class="text-danger">*</span>
                </label>
                <select name="grade_level" id="grade_level" class="form-select @error('grade_level') is-invalid @enderror" required>
                    <option value="">{{__('messages.select_grade_levels')}}</option>
                    @foreach($gradeLevels as $index => $level)
                        <option value="{{ $index }}" {{ old('grade_level') == $index ? 'selected' : '' }}>
                            {{ $level }}
                        </option>
                    @endforeach
                </select>
                @error('grade_level')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- اسم المرحلة --}}
            <div class="mb-3">
                <label for="name" class="form-label">
                    {{ __('messages.name') }} <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" required value="{{ old('name') }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- وصف اختياري --}}
            <div class="mb-3">
                <label for="description" class="form-label">{{ __('messages.description') }}</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <i class="feather-arrow-left"></i> {{ __('messages.back') }}
                </a>
                <button type="submit" class="btn btn-success">{{ __('messages.save') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function filterAcademicYears() {
        const selectedSchoolId = document.getElementById('school_id').value;
        const yearSelect = document.getElementById('academic_year_id');
        const options = yearSelect.querySelectorAll('option');

        yearSelect.value = ""; // إعادة تعيين الاختيار

        options.forEach(option => {
            if (option.value === "") return;
            const schoolId = option.getAttribute('data-school');
            option.hidden = schoolId !== selectedSchoolId;
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        filterAcademicYears();

        const selectedYearId = "{{ old('academic_year_id') }}";
        if (selectedYearId) {
            document.getElementById('academic_year_id').value = selectedYearId;
        }
    });
</script>
@endpush
