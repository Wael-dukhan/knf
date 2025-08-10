<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Reports')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" />

    <!-- Feather Icons -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/feather/feather.css') }}" />

    <!-- Flags Icons -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/icons/flags/flags.css') }}" />

    <!-- FontAwesome -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}" />

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/datatables.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" />

    <!-- Google Fonts Amiri -->
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet" />

    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />

    <style>
        body {
            font-family: 'Amiri', serif;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .amiri-regular {
            font-weight: 400;
            font-style: normal;
        }
        .amiri-bold {
            font-weight: 700;
            font-style: normal;
        }
        .amiri-regular-italic {
            font-weight: 400;
            font-style: italic;
        }
        .amiri-bold-italic {
            font-weight: 700;
            font-style: italic;
        }

        /* Adjust navbar items alignment for RTL/LTR */
        .navbar-nav {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
        html[dir="rtl"] .ms-auto {
            margin-left: auto !important;
            margin-right: initial !important;
        }
        html[dir="ltr"] .ms-auto {
            margin-right: auto !important;
            margin-left: initial !important;
        }
        html[dir="rtl"] .user-menu .dropdown-menu.show {
            transform: translate3d(120px, 40px, 0) !important;
        }
        nav.navbar.navbar-expand-lg.navbar-light.bg-white.shadow-sm .container {
            column-gap: 10px;
        }

        /* Container for chart + table */
        .report-chart {
            margin-bottom: 2rem;
            min-height: 350px; /* enough space for chart */
        }

        /* DataTables customization */
        table.dataTable thead th {
            background-color: #e9ecef;
            font-weight: 700;
            text-align: center;
        }
        table.dataTable tbody td {
            text-align: center;
            vertical-align: middle;
        }
        div#marksTable_filter {
            display: inline;
        }
        div#marksTable_length {
            display: inline;
            padding: 15px;
        }
        div.dataTables_wrapper {
            padding-top: 15px;
        }
    </style>

    @stack('styles')

    <!-- Highcharts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    @if(app()->getLocale() == 'ar')
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/accessibility.js"></script>
        <script src="https://code.highcharts.com/modules/offline-exporting.js"></script>
        <script src="https://code.highcharts.com/js/modules/bidi.js"></script> <!-- bidi for RTL support -->
    @endif
</head>
<body>

    @include('layouts.partials.header')

    <div class="wrapper">
        <div class="main-content">
            @include('layouts.partials.sidebar')
            <div class="page-wrapper">
                <div class="container-fluid mt-5 custom-table">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript dependencies --}}
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>

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
