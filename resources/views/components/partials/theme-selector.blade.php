@props([
    'position' => 'fixed', // fixed | relative
    'placement' => 'top-right', // top-right | top-left | bottom-right | bottom-left
    'themes' => ['light', 'dark', 'cupcake', 'bumblebee', 'emerald', 'corporate', 'synthwave', 'retro', 'cyberpunk', 'valentine', 'halloween', 'garden', 'forest', 'aqua', 'lofi', 'pastel', 'fantasy', 'wireframe', 'black', 'luxury', 'dracula', 'cmyk', 'autumn', 'business', 'acid', 'lemonade', 'night', 'coffee', 'winter'],
])

@php
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
