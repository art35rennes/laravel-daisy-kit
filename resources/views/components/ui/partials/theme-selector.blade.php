@props([
    'position' => 'fixed', // fixed | relative
    'placement' => 'top-right', // top-right | top-left | bottom-right | bottom-left
    'themes' => null, // null = utilise tous les thèmes de la config (intégrés + personnalisés)
    'offsetClass' => null, // ex: top-20 pour décaler sous une navbar fixe
])
@php
    use Art35rennes\DaisyKit\Helpers\ThemeHelper;
    $themes = $themes ?? ThemeHelper::getAllThemes();
    $showThemeSelector = (bool) config('daisy-kit.dev.show_theme_selector', false);
@endphp

@if($showThemeSelector)
    @php
        $positionClasses = match($position) {
            'fixed' => 'fixed z-50',
            'relative' => 'relative',
            default => 'fixed z-50',
        };

        $placementClasses = match($placement) {
            'top-right' => 'top-4 right-4',
            'top-left' => 'top-4 left-4',
            'bottom-right' => 'bottom-4 right-4',
            'bottom-left' => 'bottom-4 left-4',
            default => 'top-4 right-4',
        };

        if ($offsetClass) {
            $placementClasses .= ' '.$offsetClass;
        }
    @endphp

    <div class="{{ $positionClasses }} {{ $placementClasses }}">
        <x-daisy::ui.advanced.theme-controller
            variant="dropdown"
            :themes="$themes"
            label="Theme"
            size="sm"
        />
    </div>
@endif
