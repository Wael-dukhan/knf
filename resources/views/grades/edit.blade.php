@extends('layouts.app2') {{-- أو layouts.admin إذا كنت تستخدم Layout مخصص --}}

    @section('title', __('messages.edit_grade'))

@section('content')
<div class="page-wrapper">
    <div class="container mt-5">
        <h2>{{ __('messages.edit_grade') }}</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.grades.update', $grade->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">{{ __('messages.name') }}</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $grade->name) }}" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">{{ __('messages.description') }}</label>
                <textarea name="description" id="description" class="form-control">{{ old('description', $grade->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="academic_year_id" class="form-label">{{ __('messages.academic_year') }}</label>
                <select name="academic_year_id" id="academic_year_id" class="form-control" required>
                    <option value="">{{ __('messages.choose_academic_year') }}</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ old('academic_year_id', $grade->academic_year_id) == $year->id ? 'selected' : '' }}>
                            {{ $year->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <a href="{{ route('grade_levels.show',[$grade->school_id , $grade->grade_level]) }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
        </form>
    </div>
</div>
@endsection
