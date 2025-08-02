@extends('layouts.app2')

@section('content')
<div class="page-wrapper">
    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">{{ __('messages.edit_academic_year') }}</h3>
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

                <form action="{{ route('admin.academic_years.update', $academic_year->id) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('messages.academic_year_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $academic_year->name) }}" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="school_id" class="form-label">{{ __('messages.school') }} <span class="text-danger">*</span></label>
                        <select name="school_id" id="school_id" class="form-select" required>
                            <option value="">{{ __('messages.select_school') }}</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ old('school_id', $academic_year->school_id) == $school->id ? 'selected' : '' }}>
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="start_date" class="form-label">{{ __('messages.start_date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $academic_year->start_date) }}" class="form-control" required>
                    </div>

                    <div class="mb-4">
                        <label for="end_date" class="form-label">{{ __('messages.end_date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $academic_year->end_date) }}" class="form-control" required>
                    </div>

                    {{-- Term 1 dates --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="term1_start" class="form-label">{{ __('messages.term1_start') }} <span class="text-danger">*</span></label>
                            <input type="date" name="term1_start" id="term1_start" class="form-control"
                                value="{{ old('term1_start', $terms[0]->start_date ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="term1_end" class="form-label">{{ __('messages.term1_end') }} <span class="text-danger">*</span></label>
                            <input type="date" name="term1_end" id="term1_end" class="form-control"
                                value="{{ old('term1_end', $terms[0]->end_date ?? '') }}" required>
                        </div>
                    </div>

                    {{-- Term 2 dates --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="term2_start" class="form-label">{{ __('messages.term2_start') }} <span class="text-danger">*</span></label>
                            <input type="date" name="term2_start" id="term2_start" class="form-control"
                                value="{{ old('term2_start', $terms[1]->start_date ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="term2_end" class="form-label">{{ __('messages.term2_end') }} <span class="text-danger">*</span></label>
                            <input type="date" name="term2_end" id="term2_end" class="form-control"
                                value="{{ old('term2_end', $terms[1]->end_date ?? '') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">{{ __('messages.status') }} <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="">{{ __('messages.select_status') }}</option>
                            <option value="active" {{ old('status', $academic_year->status) == 'active' ? 'selected' : '' }}>
                                {{ __('messages.active') }}
                            </option>
                            <option value="inactive" {{ old('status', $academic_year->status) == 'inactive' ? 'selected' : '' }}>
                                {{ __('messages.inactive') }}
                            </option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.academic_years.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> {{ __('messages.update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
