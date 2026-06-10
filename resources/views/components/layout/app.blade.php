@props([
    'title' => null,
    'container' => true,
    'theme' => null,
    'htmlClass' => null,
    'bodyClass' => null,
    'fontUrl' => 'https://fonts.bunny.net/css?family=instrument-sans:400,500,600',
    'loadDefaultFont' => true,
])

@php
    $resolvedTheme = $theme === null
        ? \Art35rennes\DaisyKit\Helpers\ThemeHelper::getDefaultTheme()
        : $theme;

    $resolvedTheme = is_string($resolvedTheme) || $resolvedTheme instanceof \Stringable
        ? trim((string) $resolvedTheme)
        : null;

    $normalizeStylesheetUrl = function($url) {
        if (!is_string($url) && !$url instanceof \Stringable) {
            return null;
        }

        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if (str_starts_with($url, '/')) {
            return $url;
        }

        return preg_match('/^https?:\/\//i', $url) === 1 ? $url : null;
    };

    $fontUrl = $normalizeStylesheetUrl($fontUrl);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if($resolvedTheme !== null && $resolvedTheme !== '') data-theme="{{ $resolvedTheme }}" @endif @if($htmlClass) class="{{ $htmlClass }}" @endif>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' | ' : '' }}{{ config('app.name', 'Laravel') }}</title>
    {{-- Injection conditionnelle des assets du package --}}
    @include('daisy::components.partials.assets')
    {{-- Injection des thèmes personnalisés --}}
    @include('daisy::components.partials.custom-themes')
    @if($loadDefaultFont && $fontUrl)
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="{{ $fontUrl }}" rel="stylesheet" />
    @endif
    <meta name="color-scheme" content="light dark">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')
    {{ $head ?? '' }}
    {{-- Place for per-page extra <head> content via named slot --}}
</head>
<body class="{{ trim('bg-base-100 text-base-content min-h-screen overflow-x-hidden '.$bodyClass) }}">
    <div class="{{ $container ? 'container mx-auto px-4 sm:px-6 py-4 sm:py-6' : '' }}">
        {{ $slot }}
    </div>
    {{ $scripts ?? '' }}
    @stack('scripts')
</body>
</html>
