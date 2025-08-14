@props([
    'title' => null,
    'container' => true,
    'theme' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if($theme) data-theme="{{ $theme }}" @endif>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' | ' : '' }}{{ config('app.name', 'Laravel') }}</title>
    {{-- Injection conditionnelle des assets du package --}}
    @include('daisy::components.partials.assets')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <meta name="color-scheme" content="light dark">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')
    {{ $head ?? '' }}
    {{-- Place for per-page extra <head> content via named slot --}}
</head>
<body class="bg-base-100 text-base-content min-h-screen overflow-x-hidden">
    <div class="{{ $container ? 'container mx-auto px-4 sm:px-6 py-4 sm:py-6' : '' }}">
        {{ $slot }}
    </div>
    {{ $scripts ?? '' }}
    @stack('scripts')
</body>
</html>


