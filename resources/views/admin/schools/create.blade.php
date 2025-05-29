@extends('layouts.app2')

@section('title', __('messages.create_school'))

@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ __('messages.create_school') }}</h3>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.schools.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="name">
                                {{ __('messages.school_name') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}" required>
                        </div>
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror

                        <div class="form-group">
                            <label for="location">
                                {{ __('messages.location') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="location" class="form-control" id="location" value="{{ old('location') }}" required>
                        </div>
                        @error('location')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror


                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> {{ __('messages.save') }}
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
