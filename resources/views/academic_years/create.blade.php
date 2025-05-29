@extends('layouts.app2')

@section('title', __('messages.academic_years_list'))

@section('content')
<div class="page-wrapper">
    <div class="container mt-5">

        <div class="card shadow-sm p-4">
            <h4 class="mb-4">{{ __('messages.academic_years_list') }}</h4>

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
