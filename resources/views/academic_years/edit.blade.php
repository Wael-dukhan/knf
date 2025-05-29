@extends('layouts.app')

@section('content')
<div class="container">
    <h2>
        {{ __('messages.edit_academic_year') }}
    </h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.academic_years.update', $academic_year->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>السنة الدراسية</label>
            <input type="text" name="name" value="{{ old('name', $academic_year->name) }}" class="form-control" required>
        </div>

        <div class="form-group mt-3">
            <label>المدرسة</label>
            <select name="school_id" class="form-control" required>
                <option value="">
                    {{ __('messages.select_school') }}
                </option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ $academic_year->school_id == $school->id ? 'selected' : '' }}>
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <br>
        <button type="submit" class="btn btn-primary">تحديث</button>
        <a href="{{ route('admin.academic_years.index') }}" class="btn btn-secondary">
            {{ __('messages.back') }}
        </a>
    </form>
</div>
@endsection
