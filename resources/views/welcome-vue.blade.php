<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    @vite([
    'resources/css/app.css',
    'resources/sass/app.scss',
    ])

    <!-- Tailwind Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        clifford: "#da373d",
                    },
                },
            },
        }
    </script>
</head>

<body>
@if(!Auth::check())
    <a href="{{url('admin/login')}}">Login</a>
@else
    <div id="app">
        <header>
            <!-- Hier können Sie einen Header hinzufügen, falls benötigt -->
        </header>
        <main class="container mx-auto">
            @yield('content')
        </main>
        <footer>
            <video-chat></video-chat>
        </footer>
    </div>
@endif

<!-- Scripts -->
@vite(['resources/js/app.js'])

<script>
    window.User = {
        id: {{optional(Auth::user())->id}},
    }
</script>
</body>

</html>
