@props([
    'items' => [],
    'size' => 'sm',
    'as' => 'nav',
    'label' => __('daisy::components.breadcrumbs'),
    'truncate' => false,
    'ellipsisLabel' => '...',
    'schema' => false,
])

@php
    $sizeMap = [
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-lg',
    ];

    $wrapperClasses = 'breadcrumbs '.($sizeMap[$size] ?? 'text-sm');
    $tag = in_array($as, ['div', 'nav'], true) ? $as : 'nav';
    $wrapperAttributes = $attributes->merge(['class' => $wrapperClasses]);

    if ($tag === 'nav') {
        $wrapperAttributes = $wrapperAttributes->merge(['aria-label' => $label]);
    }

    $items = collect($items)->values();
    $slotContent = isset($slot) ? trim((string) $slot) : '';
    $usesSlot = $slotContent !== '';
    $shouldTruncate = filter_var($truncate, FILTER_VALIDATE_BOOLEAN) && $items->count() > 2;

    $renderIcon = static function (mixed $icon): ?string {
        if ($icon instanceof \Illuminate\Contracts\Support\Htmlable) {
            return $icon->toHtml();
        }

        if (is_string($icon) && $icon !== '') {
            return e($icon);
        }

        return null;
    };

    $renderTrustedIconHtml = static function (mixed $icon): ?string {
        if ($icon instanceof \Illuminate\Contracts\Support\Htmlable) {
            return $icon->toHtml();
        }

        return is_string($icon) && $icon !== '' ? $icon : null;
    };

    $breadcrumbSchema = null;

    if (! $usesSlot && filter_var($schema, FILTER_VALIDATE_BOOLEAN)) {
        $schemaItems = $items
            ->reject(fn (mixed $item): bool => (bool) data_get($item, 'separator', false))
            ->values()
            ->map(function (mixed $item, int $index): array {
                $entry = [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => (string) data_get($item, 'label', ''),
                ];

                $href = data_get($item, 'href');
                $href = is_string($href) || $href instanceof \Stringable ? trim((string) $href) : '';

                if ($href !== '') {
                    $entry['item'] = url($href);
                }

                return $entry;
            })
            ->all();

        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $schemaItems,
        ];
    }
@endphp

{{--
    Usage:
    <x-daisy::ui.navigation.breadcrumbs :items="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'Users', 'href' => route('users.index')],
        ['label' => $user->name, 'current' => true],
    ]" />

    Supported item keys: label, href, current, disabled, separator, iconName, icon, iconHtml.
    Use iconName for Blade Icons and HtmlString or iconHtml for trusted icon markup.
    Plain string icon values are escaped.
--}}
<{{ $tag }} {{ $wrapperAttributes }}>
    <ul>
        @if($usesSlot)
            {{ $slot }}
        @else
            @foreach($items as $index => $item)
                @php
                    $labelText = (string) data_get($item, 'label', '');
                    $href = data_get($item, 'href');
                    $href = is_string($href) || $href instanceof \Stringable ? (string) $href : null;
                    $hasHref = is_string($href) && trim($href) !== '';
                    $isDisabled = (bool) data_get($item, 'disabled', false);
                    $isSeparator = (bool) data_get($item, 'separator', false);
                    $isCurrent = (bool) data_get($item, 'current', false) || (! $hasHref && ! $isDisabled && ! $isSeparator && $index === $items->count() - 1);
                    $iconName = data_get($item, 'iconName');
                    $iconName = is_string($iconName) || $iconName instanceof \Stringable ? (string) $iconName : null;
                    $iconHtml = data_get($item, 'iconHtml');
                    $icon = $iconHtml !== null ? $renderTrustedIconHtml($iconHtml) : $renderIcon(data_get($item, 'icon'));
                    $hasVisualIcon = $iconName || $icon;
                    $itemClasses = $hasVisualIcon ? 'inline-flex items-center gap-2' : '';
                    $spanClasses = trim($itemClasses.' '.($isCurrent ? 'font-medium' : '').' '.($isDisabled ? 'opacity-60 cursor-not-allowed' : ''));
                    $shouldCollapseMiddle = $shouldTruncate && $index > 0 && $index < $items->count() - 1;
                @endphp

                @if($shouldTruncate && $index === 1)
                    <li class="sm:hidden" aria-hidden="true"><span>{{ $ellipsisLabel }}</span></li>
                @endif

                <li @if($shouldCollapseMiddle) class="hidden sm:list-item" @endif @if($isSeparator) aria-hidden="true" @endif>
                    @if($hasHref && ! $isCurrent && ! $isDisabled && ! $isSeparator)
                        <a href="{{ $href }}" @if($itemClasses !== '') class="{{ $itemClasses }}" @endif>
                            @if($iconName)
                                <x-icon :name="$iconName" class="w-4 h-4 shrink-0" />
                            @elseif($icon)
                                {!! $icon !!}
                            @endif
                            <span>{{ $labelText }}</span>
                        </a>
                    @else
                        <span @if($spanClasses !== '') class="{{ $spanClasses }}" @endif @if($isCurrent) aria-current="page" @endif @if($isDisabled) aria-disabled="true" @endif>
                            @if($iconName)
                                <x-icon :name="$iconName" class="w-4 h-4 shrink-0" />
                            @elseif($icon)
                                {!! $icon !!}
                            @endif
                            <span>{{ $labelText }}</span>
                        </span>
                    @endif
                </li>
            @endforeach
        @endif
    </ul>
 </{{ $tag }}>

@if($breadcrumbSchema)
    <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
