@extends('layouts.app2')

@section('content')
<div class="page-wrapper">
    <div class="container mt-5">
        <h2>{{ __('messages.transfer_student') }}</h2>

        <form action="{{ route('student.class_sections.update', $student->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>{{ __('messages.student') }}:</label>
                <p>{{ $student->name }}</p>
            </div>

            <div class="form-group">
                <label>{{ __('messages.current_section') }}:</label>
                <p>{{ $currentClassSection?->name ?? __('messages.not_registered') }}</p>
            </div>

            <div class="form-group mt-3">
                <label for="class_section_id">{{ __('messages.select_new_section') }}:</label>
                <select name="class_section_id" id="class_section_id" class="form-control" required>
                    @foreach($availableClassSections as $section)
                        <option value="{{ $section->id }}"
                            {{ $currentClassSection && $section->id == $currentClassSection->id ? 'disabled' : '' }}>
                            {{ $section->grade->name }} - {{ $section->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mt-4">
                <a href="{{ url()->previous() }}" class="btn btn-secondary">{{ __('messages.back') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('messages.transfer') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
