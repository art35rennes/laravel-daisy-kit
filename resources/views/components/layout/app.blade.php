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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <meta name="color-scheme" content="light dark">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{ $head ?? '' }}
    {{-- Place for per-page extra <head> content via named slot --}}
</head>
<body class="bg-base-100 text-base-content min-h-screen">
    <div class="{{ $container ? 'container mx-auto p-6' : '' }}">
        {{ $slot }}
    </div>
    {{ $scripts ?? '' }}
</body>
</html>


