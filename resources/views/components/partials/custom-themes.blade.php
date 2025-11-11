@php
    use Art35rennes\DaisyKit\Helpers\ThemeHelper;
    $customThemesCss = ThemeHelper::generateCustomThemesCss();
@endphp

@if($customThemesCss)
    @pushOnce('styles')
        <style>
            {!! $customThemesCss !!}
        </style>
    @endPushOnce
@endif

