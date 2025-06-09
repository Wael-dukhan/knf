@extends('layouts.app2')

@section('title', __('quran_levels.create_new'))

@section('content')
<div class="page-wrapper">
    <div class="container mt-5">
        <h2 class="mb-4">{{ __('quran_levels.create_new') }}</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('quran-levels.store') }}" method="POST">
            @csrf

            {{-- اختيار المدرسة --}}
            <div class="mb-3">
                <label for="school_id" class="form-label">
                    {{ __('quran_levels.school') }} <span class="text-danger">*</span>
                </label>
                <select name="school_id" id="school_id" class="form-select @error('school_id') is-invalid @enderror" required onchange="filterAcademicYears()">
                    <option value="">{{ __('quran_levels.select_school') }}</option>
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

            {{-- اختيار السنة الدراسية --}}
            <div class="mb-3">
                <label for="academic_year_id" class="form-label">
                    {{ __('quran_levels.academic_year') }} <span class="text-danger">*</span>
                </label>
                <select name="academic_year_id" id="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                    <option value="">{{ __('quran_levels.select_year') }}</option>
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

            {{-- اسم المستوى --}}
            <div class="mb-3">
                <label for="name" class="form-label">
                    {{ __('quran_levels.name') }} <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" required value="{{ old('name') }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- ترتيب المستوى --}}
            <div class="mb-3">
                <label for="level_order" class="form-label">
                    {{ __('quran_levels.level_order') }} <span class="text-danger">*</span>
                </label>
                <input type="number" name="level_order" id="level_order" class="form-control @error('level_order') is-invalid @enderror" required value="{{ old('level_order') }}">
                @error('level_order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- وصف اختياري --}}
            <div class="mb-3">
                <label for="description" class="form-label">{{ __('quran_levels.description') }}</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <i class="feather-arrow-left"></i> {{ __('quran_levels.back') }}
                </a>
                <button type="submit" class="btn btn-success">{{ __('quran_levels.save') }}</button>
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
