@extends('layouts.table-layout2')

@section('title', __('messages.edit_term'))

@section('content')
    <div class="container mt-5">
        <h2>{{ __('messages.edit_term') }}: {{ $term->name }}</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.terms.update', $term->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- اسم الفصل -->
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $term->name) }}" class="form-control" required>
            </div>

            <!-- السنة الدراسية -->
            <div class="mb-3">
                <label for="academic_year_id" class="form-label">{{ __('messages.academic_year') }} <span class="text-danger">*</span></label>
                <select name="academic_year_id" id="academic_year_id" class="form-select" required>
                    <option value="">-- {{ __('messages.select_academic_year') }} --</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ $year->id == $term->academic_year_id ? 'selected' : '' }}>
                            {{ $year->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- المدرسة -->
            <div class="mb-3">
                <label for="school_id" class="form-label">{{ __('messages.school') }} <span class="text-danger">*</span></label>
                <select name="school_id" id="school_id" class="form-select" required>
                    <option value="">{{ __('messages.select_school') }}</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ $school->id == $term->school_id ? 'selected' : '' }}>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- تاريخ البدء -->
            <div class="mb-3">
                <label for="start_date" class="form-label">{{ __('messages.start_date') }} <span class="text-danger">*</span></label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d', $term->start_date)) }}" class="form-control" required>
            </div>

            <!-- تاريخ الانتهاء -->
            <div class="mb-3">
                <label for="end_date" class="form-label">{{ __('messages.end_date') }} <span class="text-danger">*</span></label>
                <input type="date" name="end_date" id="end_date" value="{{ old('end_date', date('Y-m-d', $term->end_date)) }}" class="form-control" required>
            </div>

            <!-- الأزرار -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.terms.index') }}" class="btn btn-secondary">
                    <i class="feather-arrow-left"></i> {{ __('messages.back') }}
                </a>
                <button type="submit" class="btn btn-success">{{ __('messages.save_changes') }}</button>
            </div>
        </form>
    </div>
@endsection
