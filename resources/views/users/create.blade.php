@extends('layouts.app2')

@section('title', __('messages.create_user'))

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <style>
            span.select2-dropdown.select2-dropdown--below {
                width: max-content!important;
                background: white!important;
            }
            
        </style>
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">{{ __('messages.create_user') }}</h3>
                </div>  
            </div>
        </div>

        <!-- Form -->
        <div class="card">
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST" class="row">
                    @csrf

                    <div class="form-group col-md-6">
                        <label for="name">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label for="email">{{ __('messages.email') }} <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group position-relative col-md-6">
                        <label for="password">{{ __('messages.password') }} <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control" required>
                        <span class="position-absolute top-50 end-0" style="cursor: pointer; margin-right: 92%!important;" onclick="togglePassword()">
                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                        </span>
                        @error('password')
                            <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label for="school">{{ __('messages.select_school') }} <span class="text-danger">*</span></label>
                        <select name="school_id" id="school" class="form-control" required>
                            <option value="">{{ __('messages.select_school') }}</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('school_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label for="gender">{{ __('messages.select_gender') }} <span class="text-danger">*</span></label>
                        <select name="gender" id="grade" class="form-control" required>
                            <option value="">{{ __('messages.select_gender') }}</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('messages.male') }}</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('messages.female') }}</option>
                        </select>
                        @error('gender')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="role">{{ __('messages.role') }} <span class="text-danger">*</span></label>
                        <select name="role_id" id="role" class="form-control" required>
                            <option value="">{{ __('messages.select_role') }}</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ __("messages.".$role->name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group col-md-6" id="parentField" style="display: none;">
                        <label for="parent_id">{{ __('messages.select_parent') }} <span class="text-danger">*</span></label>
                        <select name="parent_id" id="parent_id" class="form-control select2">
                            <option value="">{{ __('messages.select_parent') }}</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    {{-- السنة الدراسية
                    <div class="form-group col-md-6" id="academicYearField" style="display: none;">
                        <label for="academic_year_id">{{ __('messages.academic_year') }} <span class="text-danger">*</span></label>
                        <select name="academic_year_id" id="academic_year_id" class="form-control">
                            <option value="">{{ __('messages.select_academic_year') }}</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}">{{ $year->name }}</option>
                            @endforeach
                        </select>
                        @error('academic_year_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div> --}}

                    <div class="mt-4">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="feather-arrow-left"></i> {{ __('messages.back') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> {{ __('messages.save') }}
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('togglePasswordIcon');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    }
    
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role');
        const schoolSelect = document.getElementById('school');
        const parentField = document.getElementById('parentField');
        let parent = $('#parent_id'); 
        // const academicYearField = document.getElementById('academicYearField');

        function toggleStudentFields() {
            const selectedRole = roleSelect.options[roleSelect.selectedIndex].text.toLowerCase();
            if (selectedRole.includes('طالب') || selectedRole.includes('student')) {
                parentField.style.display = 'block';
                // academicYearField.style.display = 'block';
            } else {
                parentField.style.display = 'none';
                parentField.querySelector('select').value = '';
                // academicYearField.querySelector('select').value = '';
                // academicYearField.style.display = 'none';
            }
        }
        function toggleSchoolFields() {
            const selectSchool = schoolSelect.value;

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

        roleSelect.addEventListener('change', toggleStudentFields);
        schoolSelect.addEventListener('change', toggleSchoolFields);
        toggleStudentFields(); // Initial check on load
    });
    $(document).ready(function() {
        // تطبيق Select2 على الحقول المحددة
        $('.select2').select2({
            placeholder: "{{ __('messages.select_option') }}",
            allowClear: true
        });
    });
</script>
@endpush
