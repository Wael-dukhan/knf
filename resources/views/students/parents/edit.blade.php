@extends('layouts.app2')

@section('content')
<div class="page-wrapper">
    <div class="container mt-5">

        <!-- عنوان الصفحة مع الترجمة -->
        <h2>{{ __('messages.edit_parent', ['student' => $student->name]) }}</h2>

        <!-- نموذج التعديل مع الترجمة -->
        <form action="{{ route('students.parents.update', ['student' => $student->id, 'parent' => $parent->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <!-- ترجمة النص "Select Parent" -->
                <label for="parent_id">{{ __('messages.select_parent') }}</label>
                <select name="parent_id" id="parent_id" class="form-control select2" required>
                    @foreach($parents as $availableParent)
                        <option value="{{ $availableParent->id }}" 
                            @if($availableParent->id == $parent->id) selected @endif>
                            {{ $availableParent->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex justify-content-between">
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <i class="feather-arrow-left"></i> {{ __('messages.back') }}
                </a>
                <button type="submit" class="btn btn-success">{{ __('messages.update_parent') }}</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // تهيئة Select2 على الـ select
        $('#parent_id').select2({
            placeholder: "{{ __('messages.select_parent') }}",
            width: '100%',
        });
    });
</script>
@endpush
