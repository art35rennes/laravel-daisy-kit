@props([
    'title' => 'Sections',
    'targetSelector' => 'div.space-y-10 > section',
    'headingSelector' => 'h2',
    'position' => 'bottom-right', // bottom-right | bottom-left | top-right | top-left
    'breakpointClass' => 'hidden md:block',
    'buttonColor' => 'primary',
    'buttonClass' => '',
    'panelWidth' => 'w-72 sm:w-80',
    'searchPlaceholder' => 'Rechercher...',
    'emptyLabel' => 'Aucune section',
])

@php
    $instanceId = 'section-nav-'.\Illuminate\Support\Str::uuid();

    $positionClasses = match($position) {
        'bottom-left' => 'bottom-6 left-6',
        'top-right' => 'top-6 right-6',
        'top-left' => 'top-6 left-6',
        default => 'bottom-6 right-6',
    };

    $panelAnchorClasses = match($position) {
        'bottom-left' => 'absolute bottom-16 left-0',
        'top-right' => 'absolute top-16 right-0',
        'top-left' => 'absolute top-16 left-0',
        default => 'absolute bottom-16 right-0',
    };
@endphp

<div
    id="{{ $instanceId }}"
    class="fixed {{ $positionClasses }} z-50 {{ $breakpointClass }}"
    data-section-nav
    data-module="section-nav"
    data-target-selector="{{ $targetSelector }}"
    data-heading-selector="{{ $headingSelector }}"
>
    <div class="{{ $panelAnchorClasses }} hidden" data-section-nav-panel>
        <div class="daisy-section-nav-box bg-base-200 rounded-box shadow-lg p-3 {{ $panelWidth }} max-w-[calc(100vw-2rem)]" data-section-nav-box>
            <div class="font-semibold mb-2">{{ $title }}</div>
            <div class="mb-2">
                <label class="input flex w-full items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4 opacity-70">
                        <path fill-rule="evenodd" d="M10.5 3.75a6.75 6.75 0 1 0 3.897 12.303l3.775 3.775a.75.75 0 1 0 1.06-1.06l-3.775-3.776A6.75 6.75 0 0 0 10.5 3.75ZM5.25 10.5a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Z" clip-rule="evenodd" />
                    </svg>
                    <input type="text" placeholder="{{ $searchPlaceholder }}" class="grow" autocomplete="off" data-section-nav-search />
                </label>
            </div>
            <ul class="menu" data-section-nav-list></ul>
            <div class="hidden px-2 py-3 text-sm text-base-content/70" data-section-nav-empty>{{ $emptyLabel }}</div>
        </div>
    </div>

    <button
        type="button"
        class="btn btn-{{ $buttonColor }} btn-circle shadow-lg {{ $buttonClass }}"
        aria-label="{{ $title }}"
        data-section-nav-button
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" data-section-nav-icon-open>
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 hidden" data-section-nav-icon-close>
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>
