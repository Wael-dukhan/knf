@extends('layouts.app2')

@section('title', __('messages.edit_material'))

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <h2 class="page-title">{{ __('messages.edit_material') }}</h2>
        </div>

        <!-- Form Card -->
        <div class="card">
            <div class="card-header">
                <h5>{{ __('messages.material_details') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('materials.update', $material->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') <!-- This method is needed to update the record -->

                    <div class="form-group">
                        <label for="name">{{ __('messages.material_name') }} <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $material->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">{{ __('messages.description') }}</label>
                        <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror">{{ old('description', $material->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="grade_id">{{ __('messages.grade') }} <span class="text-danger">*</span></label>
                        <select id="grade_id" name="grade_id" class="form-control select2 @error('grade_id') is-invalid @enderror" required>
                            <option value="">{{ __('messages.select_grade') }}</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade->id }}" {{ old('grade_id', $material->grade_id) == $grade->id ? 'selected' : '' }}>
                                    {{ $grade->name }} - {{ $grade->school->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('grade_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @if ($material->main_book_path)
                        <div class="mb-3">
                            <label class="form-label">الكتاب الرئيسي الحالي:</label><br>
                            <a href="{{ asset('storage/' . $material->main_book_path) }}" target="_blank" class="btn btn-outline-primary">عرض / تحميل</a>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label for="main_book" class="form-label">كتاب المادة الرئيسي (PDF فقط)</label>
                        <input type="file" name="main_book" id="main_book" class="form-control" accept="application/pdf">
                    </div>

                    @if ($material->activity_book_path)
                        <div class="mb-3">
                            <label class="form-label">كتاب الأنشطة الحالي:</label><br>
                            <a href="{{ asset('storage/' . $material->activity_book_path) }}" target="_blank" class="btn btn-outline-primary">عرض / تحميل</a>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label for="activity_book" class="form-label">كتاب الأنشطة (PDF فقط)</label>
                        <input type="file" name="activity_book" id="activity_book" class="form-control" accept="application/pdf">
                    </div>

                    <div class="">
                        <a href="{{ route('materials.index') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> {{ __('messages.back') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('messages.save_material') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- تهيئة select2 -->
<script>
    $(document).ready(function() {
        // تهيئة select2 للحقول
        $('.select2').select2({
            theme: 'bootstrap4' // اختياري، يستخدم مظهر Bootstrap
        });
    });
</script>
@endpush

@endsection
