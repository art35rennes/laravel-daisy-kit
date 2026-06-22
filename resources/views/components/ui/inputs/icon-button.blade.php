@props([
    'icon' => null,
    'label' => null,
    'tooltip' => null,
    'href' => null,
    'type' => 'button',
    'variant' => 'ghost',
    'size' => 'xs',
    'color' => null,
    'disabled' => false,
    'target' => null,
])

@php
    $normalizeHref = function($url) {
        if (!is_string($url) && !$url instanceof \Stringable) {
            return null;
        }

        $url = trim((string) $url);

        if ($url === '' || $url === '#' || str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return $url === '' ? null : $url;
        }

        return preg_match('/^(https?:|mailto:|tel:)/i', $url) === 1 ? $url : null;
    };

    $href = $normalizeHref($href);
    $text = $tooltip ?: $label;
    $ariaLabel = $label ?: $tooltip ?: 'Action';
    $sizeClass = match ($size) {
        'sm' => 'btn-sm h-9 min-h-9 w-9',
        'md' => 'btn-md h-10 min-h-10 w-10',
        'lg' => 'btn-lg h-12 min-h-12 w-12',
        default => 'btn-xs h-8 min-h-8 w-8',
    };
    $variantClass = match ($variant) {
        'outline' => 'btn-outline',
        'link' => 'btn-link',
        'soft' => 'btn-soft',
        'dash' => 'btn-dash',
        default => 'btn-ghost',
    };
    $buttonClass = trim('btn btn-circle '.$sizeClass.' '.$variantClass.' '.($color ? 'btn-'.$color : ''));
    $rel = $target === '_blank' ? 'noopener noreferrer' : null;
@endphp

<span @if($text) data-tip="{{ $text }}" @endif class="{{ $text ? 'tooltip tooltip-top inline-flex' : 'inline-flex' }}">
    @if($href)
        <a
            href="{{ $href }}"
            @if($target) target="{{ $target }}" @endif
            @if($rel) rel="{{ $rel }}" @endif
            aria-label="{{ $ariaLabel }}"
            title="{{ $text ?? $ariaLabel }}"
            @if($disabled) aria-disabled="true" @endif
            {{ $attributes->merge(['class' => $buttonClass]) }}
        >
            @if($icon)
                <x-icon :name="$icon" class="size-4" />
            @else
                {{ $slot }}
            @endif
        </a>
    @else
        <button
            type="{{ $type }}"
            @disabled($disabled)
            aria-label="{{ $ariaLabel }}"
            title="{{ $text ?? $ariaLabel }}"
            {{ $attributes->merge(['class' => $buttonClass]) }}
        >
            @if($icon)
                <x-icon :name="$icon" class="size-4" />
            @else
                {{ $slot }}
            @endif
        </button>
    @endif
</span>
