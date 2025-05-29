<!DOCTYPE html>
<html  lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <title>{{ __('messages.knf')}}</title>
        
        <link rel="shortcut icon" href="{{asset('assets/img/favicon.png')}}">
        
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;0,900;1,400;1,500;1,700&display=swap" rel="stylesheet">
        
        <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css')}}">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        
        <link rel="stylesheet" href="{{ asset('assets/plugins/feather/feather.css')}}">
        
        <link rel="stylesheet" href="{{ asset('assets/plugins/icons/flags/flags.css')}}">
        
        <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css')}}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css')}}">
        
        <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/datatables.min.css')}}">
        
        <link rel="stylesheet" href="{{ asset('assets/css/style.css')}}">
        @stack('styles')
    </head>
<body>
    @include('layouts.partials.header')
    <div class="wrapper">
        <div class="main-content">
            @include('layouts.partials.sidebar')
            @yield('content')
        </div>
    </div>
    @include('layouts.partials.footer')

    <!-- إضافة جافا سكربت هنا -->
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js')}}"></script>

    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <script src="{{ asset('assets/js/feather.min.js')}}"></script>

    <script src="{{ asset('assets/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>

    <script src="{{ asset('assets/plugins/datatables/datatables.min.js')}}"></script>

    <script src="{{ asset('assets/js/script.js')}}"></script>
    @stack('scripts')
</body>
</html>
