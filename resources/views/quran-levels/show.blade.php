@extends('layouts.app2')

@section('title', __('quran_levels.details'))

@section('content')
<div class="page-wrapper">
    <div class="container mt-5">
        <h2>{{ __('quran_levels.details') }}</h2>

        <div class="card shadow-sm p-4 mb-4">
            <h4>{{ __('quran_levels.basic_information') }}</h4>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <strong>{{ __('quran_levels.school') }}:</strong>
                    <div>{{ $quranLevel->school->name ?? '-' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>{{ __('quran_levels.academic_year') }}:</strong>
                    <div>{{ $quranLevel->academicYear->name ?? '-' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>{{ __('quran_levels.name') }}:</strong>
                    <div>{{ $quranLevel->name }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>{{ __('quran_levels.level_order') }}:</strong>
                    <div>{{ $quranLevel->level_order }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>{{ __('quran_levels.description') }}:</strong>
                    <div>{{ $quranLevel->description ?? __('messages.no_description') }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>{{ __('quran_levels.total_students') }}:</strong>
                    <div>{{ $totalStudents }}</div>
                </div>
            </div>
        </div>


        <div class="card shadow-sm p-4 mb-4">
            <h4>{{ __('quran_levels.quran_classes') }}</h4>

            @if($quranLevel->quranClasses->isEmpty())
                <div class="alert alert-info">{{ __('quran_levels.no_classes') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('quran_levels.class_name') }}</th>
                                <th>{{ __('messages.teacher') }}</th>
                                <th>{{ __('messages.description') }}</th>
                                <th>{{ __('messages.student_count') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quranLevel->quranClasses as $class)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $class->name }}</td>
                                    <td>{{ $class->quranTeacher->name ?? '-' }}</td>
                                    <td>{{ $class->description ?? __('messages.no_description') }}</td>
                                    <td>{{ $class->student_count }}</td>
                                    <td>
                                        <a href="{{ route('quran_student_attendance.index', $class->id) }}" class="btn btn-sm btn-secondary">
                                            <i class="feather-users"></i> {{ __('messages.student_attendance_log') }}
                                        </a>
                                        <a href="{{ route('quran-classes.show', $class->id) }}" class="btn btn-sm btn-info">
                                            <i class="feather-eye"></i> {{ __('messages.view') }}
                                        </a>
                                        <a href="{{ route('quran-classes.edit', $class->id) }}" class="btn btn-sm btn-warning">
                                            <i class="feather-edit"></i> {{ __('messages.edit') }}
                                        </a>
                                        <form action="{{ route('quran-classes.destroy', $class->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('messages.confirm_delete') }}')">
                                                <i class="feather-trash-2"></i> {{ __('messages.delete') }}
                                            </button>
                                        </form>
                                        
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('schools.quran-levels',$quranLevel->school->id) }}" class="btn btn-secondary">
                <i class="feather-arrow-left"></i> {{ __('quran_levels.back') }}
            </a>
        </div>
    </div>
</div>
@endsection
