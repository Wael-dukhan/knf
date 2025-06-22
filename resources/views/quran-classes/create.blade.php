@extends('layouts.app2')

@section('title', __('messages.create_quran_class'))

@section('content')
<div class="page-wrapper">
    <div class="container mt-5">
        <h2>{{ __('messages.create_quran_class') }}</h2>

        <form action="{{ route('quran-classes.store') }}" method="POST">
            @csrf

            {{-- اختيار المدرسة --}}
            <div class="mb-3">
                <label for="school_id" class="form-label">
                    {{ __('messages.school') }} <span class="text-danger">*</span>
                </label>
                <select name="school_id" id="school_id"
                        class="form-control @error('school_id') is-invalid @enderror" required>
                    <option value="">{{ __('messages.choose_school') }}</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}">
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
                @error('school_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- المستوى --}}
            <div class="mb-3">
                <label for="quran_level_id" class="form-label">
                    {{ __('messages.choose_quran_level') }} <span class="text-danger">*</span>
                </label>
                <select name="quran_level_id" id="quran_level_id"
                        class="form-control @error('quran_level_id') is-invalid @enderror" required>
                    <option value="">{{ __('messages.select_school_first') }}</option>
                </select>
                @error('quran_level_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- المعلم --}}
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

@push('scripts')
<script>
    $(document).ready(function () {
        $('#school_id').on('change', function () {
            const schoolId = $(this).val();
            const $levelSelect = $('#quran_level_id');
            const loadingText = "{{ __('messages.loading') }}";
            $levelSelect.empty().append(`<option value="">${loadingText}</option>`);

            if (!schoolId) {
                $levelSelect.html(`<option value="">{{ __('messages.select_school_first') }}</option>`);
                return;
            }
            const url = '{{ url("schools") }}/' + schoolId + '/quran-levels/json';

            $.ajax({
                url: url,
                type: 'GET',
                data: { schoolId: schoolId },
                success: function (response) {
                    $levelSelect.empty().append(`<option value="">{{ __('messages.choose_quran_level') }}</option>`);
                    console.log(response);

                    if (Array.isArray(response)) {
                        response.forEach(level => {
                            if (level && level.name && typeof level.name === 'string' && level.name.trim() !== '') {
                                $levelSelect.append(`<option value="${level.id}">${level.name}</option>`);
                            }
                        });
                    } else {
                        console.error('الاستجابة ليست مصفوفة JSON صحيحة');
                    }
                },
                error: function () {
                    $levelSelect.html(`<option value="">{{ __('messages.error_loading_levels') }}</option>`);
                }
            });

            const $teacherSelect = $('#teacher_id');
            $teacherSelect.empty().append(`<option value="">{{ __('messages.loading') }}</option>`);

            const teacherUrl = '{{ route("admin.schools.quran-teachers", ["schoolId" => ":schoolId"]) }}'.replace(':schoolId', schoolId);

            $.ajax({
                url: teacherUrl,
                type: 'GET',
                success: function (teachers) {
                    $teacherSelect.empty().append(`<option value="">{{ __('messages.select_teacher') }}</option>`);
                    if (Array.isArray(teachers)) {
                        teachers.forEach(teacher => {
                            if (teacher && teacher.name) {
                                $teacherSelect.append(`<option value="${teacher.id}">${teacher.name}</option>`);
                            }
                        });
                    }
                },
                error: function () {
                    $teacherSelect.html(`<option value="">{{ __('messages.error_loading_teachers') }}</option>`);
                }
            });

        });
    });
</script>
@endpush
