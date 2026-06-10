@php
    use Art35rennes\DaisyKit\Helpers\ThemeHelper;
    use Art35rennes\DaisyKit\Support\PackageAsset;

    $customThemesCss = ThemeHelper::generateCustomThemesCss();
    $shouldRenderInlineCustomCss = (bool) config('daisy-kit.themes.inline_custom_css', false);
@endphp

@if($shouldRenderInlineCustomCss && $customThemesCss)
    @pushOnce('styles')
        <style{!! PackageAsset::nonceAttribute() !!}>
            {!! $customThemesCss !!}
        </style>
    @endPushOnce
@endif
