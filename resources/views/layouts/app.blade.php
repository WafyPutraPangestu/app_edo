<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0a0f1e">
    <meta name="description" content="{{ $description ?? 'AeroImport — Sistem Manajemen Import & Logistik' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="/favicon.ico" sizes="any">

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

    {{-- Slot kalau ada halaman yang butuh <meta>/<style> tambahan, tanpa harus edit layout ini --}}
    @stack('head')
</head>

<body class="min-h-screen flex flex-col">

    @unless (request()->routeIs('login'))
        @livewire('components.navbar')
    @endunless

    <main class="flex-1 container mx-auto px-4 py-4 md:py-10">
        {{ $slot }}
    </main>

    @livewireScripts

    {{-- Slot kalau ada halaman yang butuh <script> tambahan, tanpa harus edit layout ini --}}
    @stack('scripts')
</body>

</html>
