<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Table Layout')</title>    
    <!-- Styles -->
    <link rel="shortcut icon" href="{{asset('assets/img/favicon.png')}}">
            
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css')}}">
    
    <link rel="stylesheet" href="{{ asset('assets/plugins/feather/feather.css')}}">
    
    <link rel="stylesheet" href="{{ asset('assets/plugins/icons/flags/flags.css')}}">
    
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/datatables.min.css')}}">
    
    <link rel="stylesheet" href="{{ asset('assets/css/style.css')}}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <style>
        body {
            font-family: 'Amiri', sans-serif;
        }
        .amiri-regular {
            font-family: "Amiri", serif;
            font-weight: 400;
            font-style: normal;
        }

        .amiri-bold {
            font-family: "Amiri", serif;
            font-weight: 700;
            font-style: normal;
        }

        .amiri-regular-italic {
            font-family: "Amiri", serif;
            font-weight: 400;
            font-style: italic;
        }

        .amiri-bold-italic {
            font-family: "Amiri", serif;
            font-weight: 700;
            font-style: italic;
        }

        /* Ensuring that the navbar items are properly aligned based on language direction */
        .navbar-nav {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
        html[dir="rtl"] .ms-auto {
            margin-left: auto !important;
        }
        html[dir="rtl"] .ms-auto {
            margin-right: initial !important;
        }
        html[dir="ltr"] .ms-auto {
            margin-right: auto !important;
        }
        html[dir="ltr"] .ms-auto {
            margin-left: initial !important;
        }
        nav.navbar.navbar-expand-lg.navbar-light.bg-white.shadow-sm .container {
            column-gap: 10px;
        }
    </style>

    @include('layouts.partials.header')
    <div class="wrapper">
        <div class="main-content">
            @include('layouts.partials.sidebar')
            <div class="page-wrapper">
                <div class="container-fluid mt-5">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    @include('layouts.partials.footer')

     <!-- إضافة جافا سكربت هنا -->
     <script src="{{ asset('assets/js/jquery-3.6.0.min.js')}}"></script>

     <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
 
     <script src="{{ asset('assets/js/feather.min.js')}}"></script>
 
     <script src="{{ asset('assets/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>
 
     <script src="{{ asset('assets/plugins/datatables/datatables.min.js')}}"></script>
 
     <script src="{{ asset('assets/js/script.js')}}"></script>

    <!-- Scripts -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}
    {{-- <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script> --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    @stack('scripts')
</body>
</html>
