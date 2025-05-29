<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

    <!-- Fonts & CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js']) <!-- لو كنت تستخدم Vite -->

</head>

<body class="bg-light">
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

        html[dir="ltr"] .ms-auto {
            margin-right: auto !important;
        }

        nav.navbar.navbar-expand-lg.navbar-light.bg-white.shadow-sm .container {
            column-gap: 10px;
        }
    </style>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">


            <!-- زر تسجيل الخروج -->
            <div class="mx-3">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        {{ __('Logout') }}
                    </button>
                </form>
            </div>
            <!-- روابط أخرى -->
            <!-- قائمة اللغة -->
            <div class="dropdown ms-auto">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="languageDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    🌐 {{ strtoupper(app()->getLocale()) }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                    <li><a class="dropdown-item" href="{{ route('setLocale', ['locale' => 'en']) }}">English</a></li>
                    <li><a class="dropdown-item" href="{{ route('setLocale', ['locale' => 'ar']) }}">العربية</a></li>
                </ul>

            </div>

            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
        </div>

    </nav>

    <!-- المحتوى -->
    <main class="py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>