@props([
    'type' => 'button',
    'variant' => 'solid', // solid | outline | ghost | link | soft | dash
    'color' => 'primary', // primary | secondary | accent | info | success | warning | error | neutral
    'size' => 'md',       // xs | sm | md | lg | xl
    'wide' => false,
    'block' => false,
    'circle' => false,
    'square' => false,
    'loading' => false,
    'active' => false,
    'noAnimation' => false,
    'disabled' => false,
    'tag' => 'button',    // button | a
    'href' => null,       // URL pour les liens
    'target' => null,     // _blank, _self, etc.
    'iconName' => null,
    'iconPosition' => 'left', // left | right
])

@php
    $sizeMap = [
        'xs' => 'btn-xs',
        'sm' => 'btn-sm',
        'md' => 'btn-md',
        'lg' => 'btn-lg',
        'xl' => 'btn-xl',
    ];

    $classes = 'btn';

    if (isset($sizeMap[$size])) {
        $classes .= ' '.$sizeMap[$size];
    }

    if ($variant === 'outline') {
        $classes .= ' btn-outline';
    } elseif ($variant === 'ghost') {
        $classes .= ' btn-ghost';
    } elseif ($variant === 'link') {
        $classes .= ' btn-link';
    } elseif ($variant === 'soft') {
        $classes .= ' btn-soft';
    } elseif ($variant === 'dash') {
        $classes .= ' btn-dash';
    }

    if ($color) {
        $classes .= ' btn-'.$color;
    }

    // Ensure link variant has explicit text color (btn-link can look faded otherwise)
    if ($variant === 'link') {
        $textColorMap = [
            'primary' => 'text-primary',
            'secondary' => 'text-secondary',
            'accent' => 'text-accent',
            'info' => 'text-info',
            'success' => 'text-success',
            'warning' => 'text-warning',
            'error' => 'text-error',
            'neutral' => 'text-neutral',
        ];
        $classes .= ' '.($textColorMap[$color] ?? 'text-primary');
    }

    if ($wide) $classes .= ' btn-wide';
    if ($block) $classes .= ' btn-block';
    if ($circle) $classes .= ' btn-circle';
    if ($square) $classes .= ' btn-square';
    if ($loading) $classes .= ' loading';
    if ($noAnimation) $classes .= ' no-animation';
    if ($active) $classes .= ' btn-active';
    if ($disabled) $classes .= ' btn-disabled';

    $resolvedIconPosition = $iconPosition === 'right' ? 'right' : 'left';
@endphp

@if($tag === 'a')
    <a 
        @if($href) href="{{ $href }}" @endif
        @if($target) target="{{ $target }}" @endif
        @if($loading) aria-busy="true" @endif
        @if($disabled) aria-disabled="true" @endif
        {{ $attributes->merge(['class' => $classes]) }}
    >
        @if($iconName && $resolvedIconPosition === 'left')
            <x-icon :name="$iconName" class="w-4 h-4 shrink-0" />
        @endif

        @if($resolvedIconPosition === 'left')
            @isset($icon)
                <span class="shrink-0">
                    {{ $icon }}
                </span>
            @endisset
        @endif

        @if(trim($slot) !== '')
            <span>{{ $slot }}</span>
        @endif

        @if($iconName && $resolvedIconPosition === 'right')
            <x-icon :name="$iconName" class="w-4 h-4 shrink-0" />
        @endif

        @if($resolvedIconPosition === 'right')
            @isset($icon)
                <span class="shrink-0">
                    {{ $icon }}
                </span>
            @endisset
        @endif

        @isset($iconRight)
            <span class="shrink-0">
                {{ $iconRight }}
            </span>
        @endisset
    </a>
@else
    <button type="{{ $type }}" @disabled($disabled) @if($loading) aria-busy="true" @endif {{ $attributes->merge(['class' => $classes]) }}>
        @if($iconName && $resolvedIconPosition === 'left')
            <x-icon :name="$iconName" class="w-4 h-4 shrink-0" />
        @endif

        @if($resolvedIconPosition === 'left')
            @isset($icon)
                <span class="shrink-0">
                    {{ $icon }}
                </span>
            @endisset
        @endif

        @if(trim($slot) !== '')
            <span>{{ $slot }}</span>
        @endif

        @if($iconName && $resolvedIconPosition === 'right')
            <x-icon :name="$iconName" class="w-4 h-4 shrink-0" />
        @endif

        @if($resolvedIconPosition === 'right')
            @isset($icon)
                <span class="shrink-0">
                    {{ $icon }}
                </span>
            @endisset
        @endif

        @isset($iconRight)
            <span class="shrink-0">
                {{ $iconRight }}
            </span>
        @endisset
    </button>
@endif

