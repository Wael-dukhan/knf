@extends('layouts.app2')

@section('title', __('messages.create_material'))

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <h2 class="page-title">{{ __('messages.create_material') }}</h2>
            <div class="page-header-title">

            </div>
        </div>

        <!-- Form Card -->
        <div class="card">
            <div class="card-header">
                <h5>{{ __('messages.material_details') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('materials.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="name">{{ __('messages.material_name') }} <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">{{ __('messages.description') }}</label>
                        <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="grade_id">{{ __('messages.select_grade') }} <span class="text-danger">*</span></label>
                        <select id="grade_id" name="grade_id" class="form-control @error('grade_id') is-invalid @enderror" required>
                            <option value="">{{ __('messages.select_grade') }}</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade->id }}" {{ old('grade_id') == $grade->id ? 'selected' : '' }}>
                                    {{ $grade->school->name }} - {{ $grade->name }} - {{ $grade->academicYear->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('grade_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="main_book" class="form-label">كتاب المادة الرئيسي (PDF فقط)</label>
                        <input type="file" name="main_book" id="main_book" class="form-control" accept="application/pdf">
                    </div>

                    <div class="mb-3">
                        <label for="activity_book" class="form-label">كتاب الأنشطة (PDF فقط)</label>
                        <input type="file" name="activity_book" id="activity_book" class="form-control" accept="application/pdf">
                    </div>

                    <div class="form-group">
                        <a href="{{ route('materials.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> {{ __('messages.back') }}
                        </a>
                        <button type="submit" class="btn btn-primary ">{{ __('messages.save_material') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#grade_id').select2({
            placeholder: "{{ __('messages.select_grade') }}",
            allowClear: true
        });
    });
</script>
@endpush
