@props([
    'name' => null,
    'id' => null,
    'size' => 'md', // xs|sm|md|lg|xl
    'variant' => null, // null|ghost
    'color' => null, // primary|secondary|accent|info|success|warning|error|neutral
    'disabled' => false,
    'readonly' => false,
    'options' => [],
    'value' => null,
    'values' => null,
    'bindOld' => true,
    'error' => null,
    'describedBy' => null,
    'placeholder' => 'Search...',
    'endpoint' => null,
    'param' => 'q',
    'debounce' => 500,
    'minChars' => 3,
    'fetchOnEmpty' => true,
    'default' => null,
    'maxItems' => null,
    'noResultsText' => 'No results found.',
    'loadingText' => 'Loading...',
    'errorText' => 'Unable to load results.',
    'selectedText' => 'selected',
    'module' => null,
])

@php
    $sizeMap = [
        'xs' => ['select' => 'select-xs', 'badge' => 'badge-xs', 'icon' => 'xs', 'minHeight' => 'min-h-6', 'padding' => 'py-0.5'],
        'sm' => ['select' => 'select-sm', 'badge' => 'badge-xs', 'icon' => 'sm', 'minHeight' => 'min-h-8', 'padding' => 'py-1'],
        'md' => ['select' => 'select-md', 'badge' => 'badge-sm', 'icon' => 'sm', 'minHeight' => 'min-h-10', 'padding' => 'py-1.5'],
        'lg' => ['select' => 'select-lg', 'badge' => 'badge-md', 'icon' => 'md', 'minHeight' => 'min-h-12', 'padding' => 'py-2'],
        'xl' => ['select' => 'select-xl', 'badge' => 'badge-lg', 'icon' => 'md', 'minHeight' => 'min-h-14', 'padding' => 'py-2.5'],
    ];

    $resolvedSize = $sizeMap[$size] ?? $sizeMap['md'];
    $selectId = $id ?: ($name ? preg_replace('/[^A-Za-z0-9_-]+/', '-', trim((string) $name, '[]')) : null);
    $submitName = is_string($name) && str_ends_with($name, '[]') ? $name : (($name ? $name.'[]' : ''));

    $sharedErrors = view()->shared('errors');
    $localErrors = $errors ?? null;
    $laravelErrors = $localErrors instanceof \Illuminate\Support\ViewErrorBag && $localErrors->any()
        ? $localErrors
        : ($sharedErrors instanceof \Illuminate\Support\ViewErrorBag ? $sharedErrors : new \Illuminate\Support\ViewErrorBag());
    $errorMessage = $error ?? ($name && method_exists($laravelErrors, 'first') ? $laravelErrors->first(rtrim((string) $name, '[]')) : null);
    $hasError = filled($errorMessage);

    $oldInput = $name ? data_get(session()->get('_old_input', []), rtrim((string) $name, '[]'), old(rtrim((string) $name, '[]'), $values ?? $value)) : ($values ?? $value);
    $selectedInput = $bindOld && $name ? $oldInput : ($values ?? $value);
    $selectedValues = collect(is_iterable($selectedInput) && ! is_string($selectedInput) ? $selectedInput : (is_null($selectedInput) ? [] : [$selectedInput]))
        ->map(fn ($item) => is_array($item) ? (string) ($item['value'] ?? $item['id'] ?? '') : (string) $item)
        ->filter(fn (string $item) => $item !== '')
        ->unique()
        ->values();

    $normalizedOptions = collect(is_iterable($options) ? $options : [])
        ->map(function ($option): array {
            if (is_array($option)) {
                return [
                    'value' => (string) ($option['value'] ?? $option['id'] ?? ''),
                    'label' => (string) ($option['label'] ?? $option['name'] ?? $option['value'] ?? $option['id'] ?? ''),
                    'subtitle' => (string) ($option['subtitle'] ?? ''),
                    'avatar' => (string) ($option['avatar'] ?? ''),
                    'disabled' => (bool) ($option['disabled'] ?? false),
                ];
            }

            return [
                'value' => (string) $option,
                'label' => (string) $option,
                'subtitle' => '',
                'avatar' => '',
                'disabled' => false,
            ];
        })
        ->filter(fn (array $option) => $option['value'] !== '')
        ->values();

    $selectedOptions = $selectedValues
        ->map(function (string $selectedValue) use ($normalizedOptions): array {
            $matched = $normalizedOptions->first(fn (array $option) => (string) $option['value'] === $selectedValue);

            return $matched ?: [
                'value' => $selectedValue,
                'label' => $selectedValue,
                'subtitle' => '',
                'avatar' => '',
                'disabled' => false,
            ];
        });

    $isDisabled = (bool) $disabled;
    $isReadonly = (bool) $readonly;

    $rootClasses = 'dropdown w-full';
    $shellClasses = 'select daisy-multi-select relative flex h-auto '.$resolvedSize['minHeight'].' w-full items-center gap-2 '.$resolvedSize['padding'];
    $shellClasses .= $isReadonly ? ' daisy-multi-select-readonly cursor-default pr-3' : ' cursor-text pr-10';

    if ($variant === 'ghost') {
        $shellClasses .= ' select-ghost';
    }

    if ($color) {
        $shellClasses .= ' select-'.$color;
    }

    if ($hasError) {
        $shellClasses .= ' select-error';
    }

    $shellClasses .= ' '.$resolvedSize['select'];

    $badgeClasses = 'badge badge-soft max-w-full gap-1';
    $badgeClasses .= $color ? ' badge-'.$color : ' badge-neutral';
    $badgeClasses .= ' '.$resolvedSize['badge'];

    $removeButtonClasses = 'btn btn-ghost btn-xs btn-circle';

    $dataAttributes = [
        'data-module' => $module ?: 'multi-select',
        'data-name' => (string) ($name ?? ''),
        'data-submit-name' => $submitName,
        'data-disabled' => $isDisabled ? 'true' : 'false',
        'data-readonly' => $isReadonly ? 'true' : 'false',
        'data-debounce' => (string) (is_numeric($debounce) ? $debounce : 500),
        'data-min-chars' => (string) (is_numeric($minChars) ? $minChars : 3),
        'data-fetch-on-empty' => $fetchOnEmpty ? 'true' : 'false',
        'data-no-results-text' => (string) $noResultsText,
        'data-loading-text' => (string) $loadingText,
        'data-error-text' => (string) $errorText,
        'data-selected-text' => (string) $selectedText,
        'data-placeholder' => (string) $placeholder,
        'data-token-class' => $badgeClasses,
        'data-token-remove-class' => $removeButtonClasses,
    ];

    if ($endpoint) {
        $dataAttributes['data-endpoint'] = (string) $endpoint;
        $dataAttributes['data-param'] = (string) ($param ?: 'q');
    }

    if (! is_null($maxItems)) {
        $dataAttributes['data-max-items'] = (string) (int) $maxItems;
    }

    if (! is_null($default)) {
        try {
            $dataAttributes['data-default'] = json_encode($default, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Throwable $e) {
            // Ignore invalid default payloads to keep rendering resilient.
        }
    }
@endphp

<div
    {{ $attributes->except(['id', 'name'])->merge(['class' => $rootClasses])->merge($dataAttributes) }}
>
    <div class="{{ $shellClasses }}" data-role="shell">
        <div class="flex min-w-0 grow flex-wrap items-center gap-1.5 overflow-hidden" data-role="selected">
            @foreach($selectedOptions as $selectedOption)
                <span class="{{ $badgeClasses }}" data-multi-select-item data-value="{{ $selectedOption['value'] }}" data-label="{{ $selectedOption['label'] }}">
                    <span class="truncate">{{ $selectedOption['label'] }}</span>
                    @unless($isReadonly)
                        <button
                            type="button"
                            class="{{ $removeButtonClasses }}"
                            data-multi-select-remove
                            aria-label="Remove {{ $selectedOption['label'] }}"
                            @disabled($isDisabled)
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    @endunless
                </span>
            @endforeach

            @if($isReadonly && $selectedOptions->isEmpty())
                <span class="text-sm text-base-content/60">{{ $placeholder }}</span>
            @endif

            <input
                type="text"
                data-role="input"
                class="w-10 min-w-8 flex-1 basis-10 border-0 bg-transparent p-0 text-sm outline-none placeholder:text-base-content/60 {{ $isReadonly ? 'hidden' : '' }}"
                @if($selectId) id="{{ $selectId }}" @endif
                autocomplete="off"
                placeholder="{{ $selectedOptions->isEmpty() ? $placeholder : '' }}"
                role="combobox"
                aria-expanded="false"
                aria-autocomplete="list"
                @if($selectId) aria-controls="{{ $selectId }}-listbox" @endif
                @readonly($isReadonly)
                @if($isReadonly) tabindex="-1" @endif
                @if($hasError) aria-invalid="true" @endif
                @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
                @disabled($isDisabled)
            />
        </div>
    </div>

    <select data-role="native" multiple hidden tabindex="-1" aria-hidden="true">
        @foreach($normalizedOptions as $option)
            <option
                value="{{ $option['value'] }}"
                @selected($selectedValues->contains(fn (string $selectedValue) => $selectedValue === (string) $option['value']))
                @disabled($option['disabled'])
                @if($option['subtitle'] !== '') data-subtitle="{{ $option['subtitle'] }}" @endif
                @if($option['avatar'] !== '') data-avatar="{{ $option['avatar'] }}" @endif
            >
                {{ $option['label'] }}
            </option>
        @endforeach
    </select>

    <div data-role="hidden-inputs">
        @foreach($selectedOptions as $selectedOption)
            @if($submitName !== '')
                <input type="hidden" name="{{ $submitName }}" value="{{ $selectedOption['value'] }}" data-multi-select-hidden />
            @endif
        @endforeach
    </div>

    <ul
        class="dropdown-content menu z-10 mt-2 hidden max-h-72 w-full overflow-auto rounded-box bg-base-100 p-2 shadow"
        @if($selectId) id="{{ $selectId }}-listbox" @endif
        role="listbox"
        data-role="list"
        aria-multiselectable="true"
    ></ul>

    @if($hasError)
        <p class="validator-hint mt-1 text-error" data-role="message">{{ $errorMessage }}</p>
    @else
        <p class="validator-hint mt-1 hidden text-error" data-role="message"></p>
    @endif
</div>
