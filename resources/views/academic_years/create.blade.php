@extends('layouts.app2')

@section('title', __('messages.academic_years.create_title'))

@section('content')
<div class="page-wrapper">
    <div class="container my-5">
        <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">{{ __('messages.academic_years.create_title') }}</h4>
        </div>
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

            <form action="{{ route('admin.academic_years.store') }}" method="POST" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('messages.academic_years.name') }}</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required maxlength="20" placeholder="{{ __('messages.academic_years.name_placeholder') }}">
                </div>

                <div class="mb-3">
                    <label for="school_id" class="form-label">{{ __('messages.academic_years.school') }}</label>
                    <select id="school_id" name="school_id" class="form-select" required>
                        <option value="" selected disabled>{{ __('messages.academic_years.select_school') }}</option>
                        @foreach ($schools as $school)
                            <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">{{ __('messages.academic_years.start_date') }}</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="end_date" class="form-label">{{ __('messages.academic_years.end_date') }}</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="status" class="form-label">{{ __('messages.academic_years.status') }}</label>
                    <select id="status" name="status" class="form-select" required>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>{{ __('messages.academic_years.active') }}</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>{{ __('messages.academic_years.inactive') }}</option>
                    </select>
                </div>

                <hr>

                <h5 class="mb-3">{{ __('messages.academic_years.term1_title') }}</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="term1_start" class="form-label">{{ __('messages.academic_years.term_start') }}</label>
                        <input type="date" id="term1_start" name="term1_start" class="form-control" value="{{ old('term1_start') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="term1_end" class="form-label">{{ __('messages.academic_years.term_end') }}</label>
                        <input type="date" id="term1_end" name="term1_end" class="form-control" value="{{ old('term1_end') }}" required>
                    </div>
                </div>

                <h5 class="mb-3">{{ __('messages.academic_years.term2_title') }}</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="term2_start" class="form-label">{{ __('messages.academic_years.term_start') }}</label>
                        <input type="date" id="term2_start" name="term2_start" class="form-control" value="{{ old('term2_start') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="term2_end" class="form-label">{{ __('messages.academic_years.term_end') }}</label>
                        <input type="date" id="term2_end" name="term2_end" class="form-control" value="{{ old('term2_end') }}" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100">{{ __('messages.academic_years.save_button') }}</button>
            </form>
        </div>
    </div>
</div>
</div>
@endsection
