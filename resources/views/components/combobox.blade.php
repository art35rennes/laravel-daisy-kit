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
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'placeholder' => 'Search…',
])

<section {{ $attributes->class(['daisy-kit-combobox', 'form-control']) }} data-daisy-kit-module="combobox">
    <p class="alert alert-error" data-daisy-kit-status hidden role="alert"></p>
    <label class="label" for="{{ $id = 'daisy-kit-combobox-'.\Illuminate\Support\Str::uuid() }}"><span class="label-text">{{ $label }}</span></label>
    <div class="dropdown w-full" data-daisy-kit-combobox-shell>
        <input
            id="{{ $id }}"
            aria-autocomplete="list"
            aria-controls="{{ $id }}-listbox"
            aria-expanded="false"
            aria-haspopup="listbox"
            class="input input-bordered w-full"
            data-daisy-kit-combobox-input
            @disabled($disabled)
            @readonly($readonly)
            placeholder="{{ $placeholder }}"
            role="combobox"
            type="text"
        >
        <ul class="menu dropdown-content z-10 mt-1 max-h-64 w-full overflow-auto rounded-box border border-base-300 bg-base-100 p-1 shadow" data-daisy-kit-combobox-listbox hidden id="{{ $id }}-listbox" role="listbox"></ul>
    </div>
    <div class="flex flex-wrap gap-1" data-daisy-kit-combobox-tokens></div>
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
        'required' => $required === true,
        'disabled' => $disabled === true,
        'readonly' => $readonly === true,
    ]) !!}</script>
</section>
