@extends('layouts.app2')

@section('title', __('messages.assign_students_to_quran_class'))

@section('content')
<div class="page-wrapper">
    <div class="container mt-5">
        <h2>{{ __('messages.assign_students_to_quran_class') }}: {{ $quranClass->name }}</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('quranClass.assign_students.store', $quranClass->id) }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="student_ids">{{ __('messages.select_students') }} <span class="text-danger">*</span></label>
                <select name="student_ids[]" id="student_ids" class="form-control select2 @error('student_ids') is-invalid @enderror" multiple required>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">
                            {{ $student->name }} ({{ $student->email }})
                        </option>
                    @endforeach
                </select>
                @error('student_ids')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-4">
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <i class="feather-arrow-left"></i> {{ __('messages.back') }}
                </a>
                <button type="submit" class="btn btn-primary">{{ __('messages.assign_students') }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "{{ __('messages.select_students') }}",
            allowClear: true
        });
    });
</script>
@endpush
@endsection
