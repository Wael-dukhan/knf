@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ __('messages.user_details') }}</h2>

    <div class="card mt-4">
        <div class="card-body">
            <p><strong>{{ __('messages.name') }}:</strong> {{ $user->name }}</p>
            <p><strong>{{ __('messages.email') }}:</strong> {{ $user->email }}</p>
            <p><strong>{{ __('messages.created_at') }}:</strong> {{ $user->created_at->format('Y-m-d H:i') }}</p>
        </div>
    </div>

    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary mt-3">{{ __('messages.back_to_list') }}</a>
</div>
@endsection
