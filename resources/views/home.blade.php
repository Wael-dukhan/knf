@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}

                    <!-- عرض الدور -->
                    <div class="mt-3">
                        <strong>{{ __('Role:') }}</strong>
                        <p>{{ $role }}</p>
                    </div>

                    <!-- عرض الصلاحيات -->
                    <div class="mt-3">
                        <strong>{{ __('Permissions:') }}</strong>
                        <ul>
                            @foreach($permissions as $permission)
                                <li>{{ $permission->name }}</li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
