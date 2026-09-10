<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Finance Dashboard</title>

    <script>
        (() => {
            const storedTheme = localStorage.getItem('finance-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', storedTheme ? storedTheme === 'dark' : prefersDark);
        })();
    </script>

    @vite(['resources/css/app.css','resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased">

<div x-data="{
        sidebar: true,
        sidebarOpen: false,
        darkMode: document.documentElement.classList.contains('dark'),
        toggleTheme() {
            this.darkMode = ! this.darkMode;
            document.documentElement.classList.toggle('dark', this.darkMode);
            localStorage.setItem('finance-theme', this.darkMode ? 'dark' : 'light');
        }
    }" class="flex min-h-screen min-w-0">

    <x-sidebar />

    <div x-cloak x-show="sidebarOpen" x-transition.opacity
        class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm lg:hidden"
        @click="sidebarOpen = false" aria-hidden="true"></div>

    <div class="flex min-h-screen min-w-0 flex-1 flex-col">

        <x-navbar />

        <main class="w-full min-w-0 flex-1 p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </main>

    </div>
</div>

@livewireScripts

{{-- SweetAlert Session Flash --}}
@if (session()->has('success'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: @json(session('success')),
            timer: 2000,
            showConfirmButton: false
        });
    });
</script>
@endif

@if (session()->has('error'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: @json(session('error')),
        });
    });
</script>
@endif

<script>
    Livewire.on('swal', (event) => {
        const payload = Array.isArray(event) ? event[0] : event;
        Swal.fire({
            icon: payload.icon ?? 'success', title: payload.title ?? '', text: payload.text ?? '',
            timer: 2000, showConfirmButton: false,
            background: document.documentElement.classList.contains('dark') ? '#111827' : '#ffffff',
            color: document.documentElement.classList.contains('dark') ? '#f8fafc' : '#0f172a',
        });
    });
</script>

@stack('scripts')

</body>
</html>
