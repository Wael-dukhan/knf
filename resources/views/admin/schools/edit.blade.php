@extends('layouts.app2')

@section('title', __('Edit School'))

@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ __('Edit School') }}</h3>
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
                            <label for="name">{{ __('School Name') }}</label>
                            <input type="text" name="name" class="form-control" id="name"
                                   value="{{ old('name', $school->name) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="location">{{ __('Location') }}</label>
                            <input type="text" name="location" class="form-control" id="location"
                                   value="{{ old('location', $school->location) }}" required>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> {{ __('messages.update') }}
                            </button>
                            <a href="javascript:history.back()" class="btn btn-secondary">
                                <i class="feather-arrow-left"></i> {{ __('messages.back') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
