@extends('layouts.app2')

@section('title', __('messages.edit_user'))

@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">{{ __('messages.edit_user') }}</h3>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="card">
            <div class="card-body">
                <form action="{{ route('users.update', $user->id) }}" method="POST" class="row">
                    @csrf
                    @method('PUT') <!-- This is for PUT request to update the resource -->

                    <div class="form-group col-md-6">
                        <label for="name">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label for="email">{{ __('messages.email') }} <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group position-relative col-md-6">
                        <label for="password">{{ __('messages.password') }}</label>
                        <input type="password" name="password" id="password" class="form-control">
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
                                <option value="{{ $school->id }}" {{ old('school_id', $user->school_id) == $school->id ? 'selected' : '' }}>
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('school_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="grade">{{ __('messages.select_gender') }} <span class="text-danger">*</span></label>
                        <select name="grade_id" id="grade" class="form-control" required>
                            <option value="">{{ __('messages.select_gender') }}</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('messages.male') }}</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('messages.female') }}</option>
                        </select>
                        @error('grade_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="role">{{ __('messages.role') }} <span class="text-danger">*</span></label>
                        <select name="role_id" id="role" class="form-control" required>
                            <option value="">{{ __('messages.select_role') }}</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $user->roles->first()->id) == $role->id ? 'selected' : '' }}>
                                    {{ __("messages.".$role->name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    {{-- ولي الأمر --}}
                    <div class="form-group col-md-6" id="parentField" style="display: none;">
                        <label for="parent_id">{{ __('messages.select_parent') }} <span class="text-danger">*</span></label>
                        <select name="parent_id" id="parent_id" class="form-control select2">
                            <option value="">{{ __('messages.select_parent') }}</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}"
                                    {{ old('parent_id', $user->parents()->pluck('id')->first()) == $parent->id ? 'selected' : ''}}
                                    >{{ $parent->name }}</option>
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
                                <option value="{{ $year->id }}"
                                    {{ old('academic_year_id', $user->parents()->pluck('id')->first()) == $year->id ? 'selected' : ''}}
                                    >{{ $year->name }}</option>
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
                            <i class="fas fa-save me-1"></i> {{ __('messages.update') }}
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
        const parentField = document.getElementById('parentField');
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

        roleSelect.addEventListener('change', toggleStudentFields);
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
