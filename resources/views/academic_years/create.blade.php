@extends('layouts.app2')

@section('title', __('messages.create_academic_year'))

@section('content')
<div class="page-wrapper">
    <div class="container mt-5">

        <div class="card shadow-sm p-4">
            <h4 class="mb-4">{{ __('messages.create_academic_year') }}</h4>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.academic_years.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">
                        {{ __('messages.academic_year_name') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="school_id" class="form-label">
                        {{ __('messages.school') }} <span class="text-danger">*</span>
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

                <!-- إضافة حقل تاريخ البدء -->
                <div class="mb-3">
                    <label for="start_date" class="form-label">
                        {{ __('messages.start_date') }} <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" class="form-control" required>
                </div>

                <!-- إضافة حقل تاريخ النهاية -->
                <div class="mb-3">
                    <label for="end_date" class="form-label">
                        {{ __('messages.end_date') }} <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" class="form-control" required>
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
</div>
@endsection
