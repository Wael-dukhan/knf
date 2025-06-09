@extends('layouts.app2')

@section('content')
<div class="page-wrapper">
    <div class="container mt-5">
       <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">{{ __('messages.user_details') }}</h2>
        <a href="{{ route('admin.class_sections.show',$educationHistory->last()->class_section_id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
        </a>
    </div>

    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-body">
            <table class="table table-bordered mb-0">
                <tbody>
                    <tr>
                        <th>{{ __('messages.name') }}</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.email') }}</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.gender') }}</th>
                        <td>{{ __('messages.'.$user->gender) }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.role') }}
                        <td> {{ $firstRole ? __('messages.'.$firstRole) : __('messages.not_assigned') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.parents') }}</th>
                        <td>
                            @forelse($parents as $parent)
                                <div>{{ $parent->name }}</div>
                            @empty
                                <span class="text-muted">{{ __('messages.no_parents_assigned') }}</span>
                            @endforelse
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.created_at') }}</th>
                        <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- جدول سجل التعليم --}}
    <div class="card shadow-sm rounded-3">
        <div class="card-header">
            <h5>{{ __('messages.education_history') }}</h5>
        </div>
        <div class="card-body p-0">
            @if($educationHistory->isEmpty())
                <p class="text-center py-3">{{ __('messages.no_education_history_found') }}</p>
            @else
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.school') }}</th>
                            <th>{{ __('messages.academic_year') }}</th>
                            <th>{{ __('messages.grade') }}</th>
                            <th>{{ __('messages.class_section') }}</th>
                            <th>{{ __('messages.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($educationHistory as $record)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $record->school_name ?? __('messages.not_assigned') }}</td>
                                <td>{{ $record->year_name }}</td>
                                <td>{{ $record->grade_name }}</td>
                                <td>{{ $record->section_name }}</td>
                                <td>{{ $record->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
