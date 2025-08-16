@extends('layouts.app2')

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <h2>{{ __('messages.assign_parent') }}</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('students.parents.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="student_id">
                    {{ __('messages.select_student') }} <span class="text-danger">*</span>
                </label>
                <select name="student_id" id="student_id" class="form-control select2 @error('student_id') is-invalid @enderror" required>
                    <option value="">{{ __('messages.select_student') }}</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" data-school-id="{{ $student->school_id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->name }}
                        </option>
                    @endforeach
                </select>
                @error('student_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mt-3">
                <label for="parent_id">
                    {{ __('messages.select_parent') }} <span class="text-danger">*</span>
                </label>
                <select name="parent_id" id="parent_id" class="form-control select2 @error('parent_id') is-invalid @enderror" required>
                    <option value="">{{ __('messages.select_parent') }}</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-start column-gap-2 mt-4">
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <i class="feather-arrow-left"></i> {{ __('messages.back') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ __('messages.assign') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#student_id').select2({
            width: '100%',
            placeholder: "{{ __('messages.select_student') }}",
            language: {
                noResults: () => "{{ app()->getLocale() == 'ar' ? 'لا توجد نتائج' : 'No results found' }}",
                searching: () => "{{ app()->getLocale() == 'ar' ? 'جاري البحث...' : 'Searching...' }}",
                inputTooShort: () => "{{ app()->getLocale() == 'ar' ? 'الرجاء كتابة المزيد' : 'Please enter more characters' }}"
            }
        });

        $('#parent_id').select2({
            width: '100%',
            placeholder: "{{ __('messages.select_parent') }}",
            language: {
                noResults: () => "{{ app()->getLocale() == 'ar' ? 'لا توجد نتائج' : 'No results found' }}",
                searching: () => "{{ app()->getLocale() == 'ar' ? 'جاري البحث...' : 'Searching...' }}",
                inputTooShort: () => "{{ app()->getLocale() == 'ar' ? 'الرجاء كتابة المزيد' : 'Please enter more characters' }}"
            }
        });
        var studentSelect = $('#student_id'); 
        let parent = $('#parent_id'); 
        function toggleSchoolFields() {
            const selectSchool = studentSelect.find(':selected').data('school-id');

            if (!selectSchool) {
                console.log("لم يتم اختيار مدرسة");
                return;
            }

            fetch(`/knf/public/get-parents-for-school/${selectSchool}`, {
                method: "GET",
                headers: {
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest", // يوضح أنه طلب Ajax
                    // "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content') // في حال كان POST
                }
            })
            .then(response => response.json())
            .then(data => {

                console.log("قائمة أولياء الأمور:", data);
                // هنا تقدر تملي select أو table بالبيانات
                parent.empty(); 
                // أضف العناصر للقائمة
                parent.append('<option value="">اختر ولي أمر</option>');

                if (data.length === 0) {
                    parent.append('<option value="">لا يوجد أولياء أمور</option>');
                } else {
                    $.each(data, function(index, parentData) {
                        parent.append(`<option value="${parentData.id}">${parentData.name}</option>`);
                    });
                }
            })
            .catch(error => {
                console.error("حدث خطأ:", error);
            });
        }
        studentSelect.on('change', toggleSchoolFields);

    });
</script>
@endpush
