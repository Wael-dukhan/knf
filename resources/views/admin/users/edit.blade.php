@extends('layouts.app')

@section('content')
<div class="container">

    <h2>{{ __('messages.edit_user') }}</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>{{ __('messages.name') }}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="form-group">
            <label>{{ __('messages.email') }}</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="form-group">
            <label>{{ __('messages.role') }}</label>
            <select name="role_id" class="form-control" required>
                <option value="">{{ __('messages.select_role') }}</option>
                @foreach($roles as $id => $name)
                    <option value="{{ $id }}" {{ $user->roles->first() && $user->roles->first()->id == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-3">{{ __('messages.update_user') }}</button>
    </form>
</div>
@endsection
