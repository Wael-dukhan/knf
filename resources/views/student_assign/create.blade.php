@extends('layouts.app2')

@section('content')
<div class="page-wrapper">
    <div class="container mt-5">
        <h2>{{ __('messages.assign_student_to_class_section') }}</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('student.assign', $gradeId) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="class_section_id">{{ __('messages.select_class_section') }} <span class="text-danger">*</span></label>
                <select name="class_section_id" id="class_section_id" class="form-control select2 @error('class_section_id') is-invalid @enderror" required>
                    <option value="">{{ __('messages.select_class_section') }}</option>
                    @foreach($classSections as $section)
                        <option value="{{ $section->id }}">
                            {{ $school->name }} - {{ $section->grade->name }} - {{ $section->name }}
                        </option>
                    @endforeach
                </select>
                @error('class_section_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mt-3">
                <label for="student_id">{{ __('messages.select_student') }} <span class="text-danger">*</span></label>
                <select name="student_id[]" id="student_id" class="form-control select2 @error('student_id') is-invalid @enderror" multiple required>
                    <option value="">{{ __('messages.select_student') }}</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">
                            {{ $student->name }} - {{ $student->school->name }}
                        </option>
                    @endforeach
                </select>
                @error('student_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mt-4">
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <i class="feather-arrow-left"></i> {{ __('messages.back') }}
                </a>
                <button type="submit" class="btn btn-primary">{{ __('messages.assign') }}</button>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // تطبيق Select2 على الحقول المحددة
        $('.select2').select2({
            placeholder: "{{ __('messages.select_option') }}",
            allowClear: true
        });
    });
</script>
@endpush

@endsection
