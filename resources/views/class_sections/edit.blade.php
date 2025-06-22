@extends('layouts.app2')

@section('content')
<div class="page-wrapper">
    <div class="container mt-5">
        <h2>تعديل الشعبة الدراسية</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.class_sections.update', $class_section->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- المدرسة --}}
            <div class="mb-3">
                <label for="school_id" class="form-label">
                    المدرسة <span class="text-danger">*</span>
                </label>
                <select name="school_id" id="school_id" class="form-select @error('school_id') is-invalid @enderror" required>
                    <option value="">-- اختر المدرسة --</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ $school->id == $class_section->grade->school_id ? 'selected' : '' }}>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
                @error('school_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- الصف الدراسي --}}
            <div class="mb-3">
                <label for="grade_id" class="form-label">
                    الصف الدراسي <span class="text-danger">*</span>
                </label>
                <select name="grade_id" id="grade_id" class="form-select @error('grade_id') is-invalid @enderror" required>
                    <option value="">-- اختر الصف --</option>
                    @foreach($grades as $grade)
                        @if($grade->school_id == $class_section->grade->school_id)
                            <option value="{{ $grade->id }}" {{ $grade->id == $class_section->grade_id ? 'selected' : '' }}>
                                {{ $grade->name }}
                            </option>
                        @endif
                    @endforeach
                </select>
                @error('grade_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- اسم الشعبة --}}
            <div class="mb-3">
                <label for="name" class="form-label">
                    اسم الشعبة <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $class_section->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('admin.grades.show' , $class_section->grade_id) }}" class="btn btn-secondary">
                    <i class="feather-arrow-left"></i> رجوع
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> تحديث
                </button>
            </div>
        </form>
    </div>
</div>

{{-- سكربت لتحميل الصفوف حسب المدرسة --}}
<script>
    document.getElementById('school_id').addEventListener('change', function () {
        var schoolId = this.value;
        var gradeSelect = document.getElementById('grade_id');
        gradeSelect.innerHTML = '<option value="">جاري التحميل...</option>';
        const baseUrl = window.location.pathname.split('/').slice(0,3).join('/') + '/';

        if (schoolId) {
            const url = `{{ route('admin.grades.by_school', ':school_id') }}`.replace(':school_id', schoolId);
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    gradeSelect.innerHTML = '<option value="">-- اختر الصف --</option>';
                    data.forEach(function (grade) {
                        var option = document.createElement('option');
                        option.value = grade.id;
                        option.text = grade.name;
                        gradeSelect.appendChild(option);
                    });
                })
                .catch(() => {
                    gradeSelect.innerHTML = '<option value="">حدث خطأ أثناء تحميل الصفوف</option>';
                });
        } else {
            gradeSelect.innerHTML = '<option value="">-- اختر الصف --</option>';
        }
    });
</script>
@endsection
