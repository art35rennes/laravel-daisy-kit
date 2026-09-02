@props([
    'name' => null,
    'label' => 'Select an option',
    'options' => [],
    'value' => null,
    'multiple' => false,
    'allowCustom' => false,
    'tokenSeparators' => [','],
    'maxItems' => null,
    'source' => null,
    'queryParam' => 'query',
    'debounce' => 200,
    'minChars' => 0,
    'maxSuggestions' => 50,
    'searchFields' => ['label', 'description', 'meta', 'value'],
    'size' => 'md',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'placeholder' => 'Search…',
])

@php
    $sizeClass = match ($size) {
        'sm' => 'input-sm',
        'lg' => 'input-lg',
        default => null,
    };
    $id = 'daisy-kit-combobox-'.\Illuminate\Support\Str::uuid();
@endphp

<section {{ $attributes->class(['daisy-kit-combobox', 'form-control']) }} data-daisy-kit-module="combobox">
    <p class="alert alert-error" data-daisy-kit-status hidden role="alert"></p>
    <label class="label" for="{{ $id }}"><span class="label-text">{{ $label }}</span></label>
    <div class="dropdown w-full" data-daisy-kit-combobox-shell>
        <div @class(['daisy-kit-combobox__control', 'input', 'input-bordered', 'w-full', $sizeClass]) data-daisy-kit-combobox-control>
            <div class="daisy-kit-combobox__tokens" data-daisy-kit-combobox-tokens></div>
            <input
                id="{{ $id }}"
                aria-autocomplete="list"
                aria-controls="{{ $id }}-listbox"
                aria-expanded="false"
                aria-haspopup="listbox"
                class="daisy-kit-combobox__input"
                data-daisy-kit-combobox-input
                @disabled($disabled)
                @readonly($readonly)
                placeholder="{{ $placeholder }}"
                role="combobox"
                type="text"
            >
            <button
                aria-label="{{ __('daisy-kit::combobox.open') }}"
                class="btn btn-ghost btn-xs btn-square daisy-kit-combobox__toggle"
                data-daisy-kit-combobox-toggle
                tabindex="-1"
                type="button"
                @disabled($disabled || $readonly)
            >
                <svg aria-hidden="true" class="size-4 fill-current" viewBox="0 0 20 20">
                    <path clip-rule="evenodd" d="M5.22 7.22a.75.75 0 0 1 1.06 0L10 10.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.28a.75.75 0 0 1 0-1.06Z" fill-rule="evenodd" />
                </svg>
            </button>
        </div>
        <div class="daisy-kit-combobox__popup" data-daisy-kit-combobox-popup hidden>
            <p class="daisy-kit-combobox__popup-status" data-daisy-kit-combobox-popup-status hidden role="status"></p>
            <ul class="menu max-h-72 overflow-auto p-1" data-daisy-kit-combobox-listbox id="{{ $id }}-listbox" role="listbox"></ul>
        </div>
    </div>
    <div data-daisy-kit-combobox-values></div>
    @if ($required)
        <input class="sr-only" data-daisy-kit-combobox-required type="text" tabindex="-1" aria-label="{{ $label }} selection" required @disabled($disabled)>
    @endif
    <script data-daisy-kit-config type="application/json">{!! \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'name' => $name,
        'options' => $options,
        'value' => $value,
        'multiple' => $multiple === true,
        'allowCustom' => $allowCustom === true,
        'tokenSeparators' => $tokenSeparators,
        'maxItems' => $maxItems,
        'source' => $source,
        'queryParam' => $queryParam,
        'debounce' => $debounce,
        'minChars' => $minChars,
        'maxSuggestions' => $maxSuggestions,
        'searchFields' => $searchFields,
        'required' => $required === true,
        'disabled' => $disabled === true,
        'readonly' => $readonly === true,
        'labels' => [
            'loading' => __('daisy-kit::combobox.loading'),
            'noResults' => __('daisy-kit::combobox.no_results'),
            'remove' => __('daisy-kit::combobox.remove'),
        ],
    ]) !!}</script>
</section>
