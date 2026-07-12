@props([
    'position' => 'fixed',
    'placement' => 'top-right',
    'themes' => null,
    'offsetClass' => null,
])

<x-daisy::ui.partials.theme-selector
    :position="$position"
    :placement="$placement"
    :themes="$themes"
    :offset-class="$offsetClass"
    {{ $attributes }}
/>
