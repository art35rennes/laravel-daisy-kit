@props([
    'color' => 'neutral', // neutral|primary|secondary|accent|info|success|warning|error
    'variant' => 'solid', // solid|soft|outline|dash
    'icon' => null,
    'title' => null,
    // API "callout" friendly
    'heading' => null,     // alias de title
    'text' => null,        // contenu texte (alias du slot)
    'inline' => false,     // actions en ligne
    'iconInHeading' => false, // placer l'icône dans le heading
    // Orientation
    'vertical' => null,       // bool|null
    'horizontal' => null,     // bool|null
    'horizontalAt' => null,   // ex: 'sm' → alert-vertical sm:alert-horizontal
    'role' => null,
    'sessionKey' => null,
    'showErrors' => false,
    'dismissible' => false,
    'closeLabel' => 'Close alert',
])

@php
    $classes = 'alert';
    // Color (supporte alias danger→error pour compat callout)
    $colorKey = $color === 'danger' ? 'error' : $color;
    $classes .= ' alert-'.$colorKey;
    // Variant
    $variantMap = [
        'soft' => 'alert-soft',
        'outline' => 'alert-outline',
        'dash' => 'alert-dash',
    ];
    if (isset($variantMap[$variant])) {
        $classes .= ' '.$variantMap[$variant];
    }

    // Orientation
    if ($horizontalAt) {
        $classes .= ' alert-vertical '.($horizontalAt).':alert-horizontal';
    } elseif (! is_null($vertical) || ! is_null($horizontal)) {
        if ($vertical) {
            $classes .= ' alert-vertical';
        }
        if ($horizontal) {
            $classes .= ' alert-horizontal';
        }
    }

    // Inline actions → aligner verticalement au centre
    if ($inline) {
        $classes .= ' items-center';
    }

    $resolvedRole = $role ?: (in_array($colorKey, ['error', 'warning'], true) ? 'alert' : 'status');

    $headingText = $heading ?? $title;
    $sessionMessage = $sessionKey ? session($sessionKey) : null;
    $laravelErrors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $errorMessages = $showErrors && method_exists($laravelErrors, 'any') && $laravelErrors->any()
        ? $laravelErrors->all()
        : [];
    $bodyText = filled($text) ? $text : (filled($sessionMessage) ? $sessionMessage : null);

    $hasSlotContent = isset($slot)
        && (method_exists($slot, 'isEmpty') ? ! $slot->isEmpty() : filled(trim((string) $slot)));

    $hasContent = filled($headingText)
        || filled($bodyText)
        || $errorMessages !== []
        || $hasSlotContent;

    // Gestion de l'icône pour éviter l'erreur BladeUI\Icons\Svg
    $iconHtml = null;
    if ($icon) {
        if (is_string($icon)) {
            $iconHtml = $icon;
        } elseif (is_object($icon) && method_exists($icon, 'toHtml')) {
            $iconHtml = $icon->toHtml();
        } elseif (is_object($icon) && method_exists($icon, '__toString')) {
            $iconHtml = (string) $icon;
        } else {
            $iconHtml = '';
        }
    }
@endphp

@if ($hasContent)
    <div
        {{ $attributes->merge(['role' => $resolvedRole, 'class' => $classes]) }}
        @if ($dismissible) data-module="alert-dismiss" @endif
    >
        @if ($iconHtml && ! $iconInHeading)
            <span class="shrink-0">{!! $iconHtml !!}</span>
        @endif
        <div class="flex-1">
            @if ($headingText)
                <h3 class="font-medium flex items-center gap-2">
                    @if ($iconHtml && $iconInHeading)
                        <span class="shrink-0">{!! $iconHtml !!}</span>
                    @endif
                    <span>{{ $headingText }}</span>
                </h3>
            @endif
            <div class="text-sm">
                @if ($errorMessages !== [])
                    <ul class="list-disc list-inside">
                        @foreach ($errorMessages as $errorMessage)
                            <li>{{ $errorMessage }}</li>
                        @endforeach
                    </ul>
                @elseif ($bodyText !== null)
                    {{ $bodyText }}
                @else
                    {{ $slot }}
                @endif
            </div>
        </div>
        @isset($actions)
            <div class="flex items-center gap-2 flex-wrap justify-start sm:justify-end">{{ $actions }}</div>
        @endisset
        @isset($controls)
            <div class="ms-2">{{ $controls }}</div>
        @endisset
        @if ($dismissible)
            <button
                type="button"
                class="btn btn-ghost btn-xs btn-square ms-2"
                aria-label="{{ $closeLabel }}"
                data-alert-dismiss
            >
                <x-icon name="bi-x" class="w-4 h-4" />
            </button>
        @endif
    </div>
@endif
