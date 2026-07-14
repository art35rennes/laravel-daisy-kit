@php
    $label = __($item['label'] ?? '');
    $children = array_values(array_filter(
        (array) data_get($item, 'children', []),
        fn ($child) => data_get($child, 'visible', true) !== false,
    ));
    $hasChildren = $children !== [];
    $itemIsActive = $isItemActive($item);
    $descendantIsActive = $hasActiveDescendant($children);
    $isOpen = (bool) data_get($item, 'open', false) || $itemIsActive || $descendantIsActive;
    $external = (bool) data_get($item, 'external', false);
    $href = $normalizeHref($item['href'] ?? '#');
    $icon = data_get($item, 'icon') ?: $fallbackIcon;
@endphp

<li
    data-sidebar-item
    data-sidebar-label="{{ $label }}"
    data-sidebar-depth="{{ $depth }}"
    data-sidebar-section-id="{{ $sectionId }}"
>
    @if($hasChildren)
        <details @if($isOpen) open @endif data-sidebar-details data-sidebar-default-open="{{ $isOpen ? '1' : '0' }}">
            <summary
                class="flex items-center gap-2 {{ $itemIsActive ? 'menu-active' : '' }}"
                title="{{ $label }}"
                aria-label="{{ $label }}"
                data-sidebar-row
            >
                <x-daisy::ui.advanced.icon :name="$icon" :prefix="$iconPrefix" size="md" class="shrink-0" />
                <span class="min-w-0 flex-1 truncate sidebar-label">{{ $label }}</span>
            </summary>
            <ul data-sidebar-submenu>
                @foreach($children as $child)
                    @include('daisy::components.ui.navigation.partials.sidebar-item', [
                        'item' => $child,
                        'depth' => $depth + 1,
                        'sectionId' => $sectionId,
                        'iconPrefix' => $iconPrefix,
                        'fallbackIcon' => $fallbackIcon,
                        'normalizeHref' => $normalizeHref,
                        'isItemActive' => $isItemActive,
                        'hasActiveDescendant' => $hasActiveDescendant,
                    ])
                @endforeach
            </ul>
        </details>
    @else
        <a
            href="{{ $href }}"
            @if($external) target="_blank" rel="noopener noreferrer" @endif
            class="flex items-center gap-2 {{ $itemIsActive ? 'menu-active' : '' }}"
            title="{{ $label }}"
            aria-label="{{ $label }}"
            data-sidebar-row
        >
            <x-daisy::ui.advanced.icon :name="$icon" :prefix="$iconPrefix" size="md" class="shrink-0" />
            <span class="min-w-0 flex-1 truncate sidebar-label">{{ $label }}</span>
        </a>
    @endif
</li>
