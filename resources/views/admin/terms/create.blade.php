@extends('layouts.app2')

@section('content')
<div class="page-wrapper">
    <div class="container mt-5">
        <h2>{{ __('messages.create_term') }}</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.terms.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">
                    {{ __('messages.term_name') }} <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label for="school_id" class="form-label">
                    {{ __('messages.schools') }} <span class="text-danger">*</span>
                </label>
                <select name="school_id" id="school_id" class="form-select" required>
                    <option value="">{{ __('messages.select_school') }}</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="academic_year_id" class="form-label">
                    {{ __('messages.academic_year') }} <span class="text-danger">*</span>
                </label>
                <select name="academic_year_id" id="academic_year_id" class="form-select" required>
                    <option value="">{{ __('messages.select_academic_year') }}</option>
                    {{-- يمكن ملء القائمة ديناميكياً عبر JavaScript --}}
                </select>
            </div>
            <div class="mb-3">
                <label for="start_date" class="form-label">
                    {{ __('messages.start_date') }} <span class="text-danger">*</span>
                </label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}" required>
            </div>

            <div class="mb-3">
                <label for="end_date" class="form-label">
                    {{ __('messages.end_date') }} <span class="text-danger">*</span>
                </label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}" required>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.terms.index') }}" class="btn btn-secondary">
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
    document.addEventListener("DOMContentLoaded", function () {
        const schoolSelect = document.getElementById('school_id');
        const academicYearSelect = document.getElementById('academic_year_id');
        const selectYearText = @json(__('messages.select_academic_year'));

        schoolSelect.addEventListener('change', function () {
            let schoolId = this.value;

            if (!schoolId) {
                academicYearSelect.innerHTML = `<option value="">${selectYearText}</option>`;
                return;
            }

            fetch(`{{ url('api/schools') }}/${schoolId}/academic-years`)
                .then(response => response.json())
                .then(data => {
                    academicYearSelect.innerHTML = `<option value="">${selectYearText}</option>`;

                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(year => {
                            const option = document.createElement('option');
                            option.value = year.id;
                            option.textContent = year.name;

                            // إعادة اختيار العنصر إذا كان قد تم اختياره مسبقاً
                            if (year.id == {{ old('academic_year_id', 'null') }}) {
                                option.selected = true;
                            }

                            academicYearSelect.appendChild(option);
                        });
                    } else {
                        academicYearSelect.innerHTML = `<option value="">${selectYearText}</option>`;
                    }
                })
                .catch(err => {
                    console.error('Error fetching academic years:', err);
                });
        });

        // Trigger change if old value exists
        if (schoolSelect.value) {
            schoolSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush
