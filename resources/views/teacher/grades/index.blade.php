@extends('layouts.table-layout2')

@section('title', __('messages.my_grades_sections'))

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">{{ __('messages.my_assigned_grades_sections') }}</h2>

    @forelse($grades as $gradeId => $classSections)
        @php
            // الصف الأول من المجموعة لنستخدم بياناته لعرض اسم الصف، المدرسة، والسنة
            $firstSection = $classSections->first();
            $grade = $firstSection->grade ?? null;
        @endphp

        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4>
                        {{ $grade->school->name ?? '-' }} - {{ $grade->name ?? '-' }} 
                        [{{ $grade->academicYear->name ?? '-' }}]
                    </h4>
                </div>
                {{-- لو تحب زر إضافة أو أي شيء خاص بالصف --}}
            </div>

            <div class="card-body p-0">
                @if($classSections->isEmpty())
                    <div class="alert alert-info m-3">{{ __('messages.no_class_sections_available') }}</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.class_section_name') }}</th>
                                    <th>{{ __('messages.number_of_students') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($classSections as $index => $section)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $section->name }}</td>
                                        <td>{{ $section->student_count }}</td>
                                        <td>
                                            <a href="{{ route('attendance.index', $section->id) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="feather-edit"></i> {{ __('messages.attendance_log') }}
                                            </a>
                                            <a href="{{ route('teacher.class_sections.show', $section) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="feather-eye"></i> {{ __('messages.view_students') }}
                                            </a>
                                            <a href="{{ route('material-assignments.show', $section->id) }}"
                                                class="btn btn-sm btn-secondary">
                                                <i class="feather-edit"></i> {{ __('messages.view_materials') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="alert alert-warning">{{ __('messages.no_assigned_grades_sections') }}</div>
    @endforelse

</div>
@endsection
