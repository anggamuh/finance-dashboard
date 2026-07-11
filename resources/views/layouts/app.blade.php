<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Finance Dashboard</title>

    @vite(['resources/css/app.css','resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-100 dark:bg-gray-950">

<div x-data="{ sidebar: true }" class="flex">

    <x-sidebar />

    <div class="flex-1 flex flex-col min-h-screen">

        <x-navbar />

        <main class="p-8">
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

{{-- Global Confirm Delete --}}
<script>
 function confirmDelete(id) {
    Swal.fire({
        title: 'Yakin hapus?',
        text: "File ini akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.dispatch('delete', { id: id });
        }
    })
}
</script>

@stack('scripts')

</body>
</html>