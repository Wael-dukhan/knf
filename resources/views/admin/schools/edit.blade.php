@extends('layouts.app2')

@section('title', __('messages.edit_school'))

@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ __('messages.edit_school') }}</h3>
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.schools.update', $school->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">{{ __('messages.school_name') }}</label>
                            <input type="text" name="name" class="form-control" id="name"
                                   value="{{ old('name', $school->name) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="location">{{ __('messages.location') }}</label>
                            <input type="text" name="location" class="form-control" id="location"
                                   value="{{ old('location', $school->location) }}" required>
                        </div>

                        <div class="mt-4">
                            <a href="javascript:history.back()" class="btn btn-secondary">
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
