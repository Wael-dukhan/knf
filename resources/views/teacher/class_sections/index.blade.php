@extends('layouts.app2') {{-- أو أي layout تستخدمه --}}

@section('title', __('messages.teacher_class_sections'))

@section('content')
<div class="container">
    <h2>{{ __('messages.my_class_sections') }}</h2>

    @if ($classSections->isEmpty())
        <div class="alert alert-info">{{ __('messages.no_class_sections_assigned') }}</div>
    @else
        @foreach ($classSections as $section)
            <div class="card my-4">
                <div class="card-header">
                    <h4>
                        {{ $section->grade_name ?? '-' }} - {{ $section->name ?? '-' }} 
                        [{{ $section->academic_year_name ?? '-' }}]
                    </h4>
                </div>
                <div class="card-body">
                    @if ($section->students->isEmpty())
                        <div class="alert alert-info">{{ __('messages.no_students_in_this_class_section') }}</div>
                    @else
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.student_name') }}</th>
                                    <th>{{ __('messages.email') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($section->students as $index => $student)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $student->name }}</td>
                                        <td>{{ $student->email }}</td>
                                        <td>{{ $student->status ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
