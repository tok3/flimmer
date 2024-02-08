<x-filament-panels::page>


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
    <div id="app">
        <header>
            <!-- Hier können Sie einen Header hinzufügen, falls benötigt -->
        </header>
        <main class="container mx-auto">
            @yield('content')
        </main>
        <footer>
            <app-video-chat></app-video-chat>
        </footer>
    </div>


    @vite(['resources/js/app.js'])

    <script>
        window.User = {
            id: {{optional(Auth::user())->id}},
        }



        var csrfToken = '{{ csrf_token() }}';
    </script>
</x-filament-panels::page>
