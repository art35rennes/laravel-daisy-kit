@props([
    'name' => null,
    'preset' => 'email', // email|text
    'size' => 'md', // xs|sm|md|lg|xl
    'variant' => null, // null|ghost
    'color' => null, // primary|secondary|accent|info|success|warning|error|neutral
    'placeholder' => null,
    'values' => [],
    'delimiters' => ['Enter', 'Tab', ','],
    'pasteSeparators' => [',', ';', "\n"],
    'allowDuplicates' => false,
    'maxItems' => null,
    'suggestions' => null,
    'endpoint' => null,
    'param' => 'q',
    'debounce' => 300,
    'minChars' => 2,
    'noResultsText' => 'No results found.',
    'invalidText' => 'The value is invalid.',
    'duplicateText' => 'This value is already added.',
    'maxItemsText' => 'You reached the maximum number of items.',
])

@php
    $sizeMap = [
        'xs' => ['input' => 'input-xs', 'badge' => 'badge-xs'],
        'sm' => ['input' => 'input-sm', 'badge' => 'badge-sm'],
        'md' => ['input' => 'input-md', 'badge' => 'badge-md'],
        'lg' => ['input' => 'input-lg', 'badge' => 'badge-lg'],
        'xl' => ['input' => 'input-xl', 'badge' => 'badge-xl'],
    ];

    $resolvedSize = $sizeMap[$size] ?? $sizeMap['md'];
    $submitName = is_string($name) && str_ends_with($name, '[]') ? $name : (($name ? $name.'[]' : ''));

    $normalizeValue = static function ($item) use ($preset) {
        $value = '';
        $label = '';

        if (is_array($item)) {
            $value = (string) ($item['value'] ?? $item['label'] ?? '');
            $label = (string) ($item['label'] ?? $item['value'] ?? '');
        } else {
            $value = (string) $item;
            $label = (string) $item;
        }

        $value = trim($value);
        $label = trim($label);

        if ($preset === 'email') {
            $value = strtolower($value);
            $label = $label !== '' ? strtolower($label) : $value;
        }

        return [
            'value' => $value,
            'label' => $label !== '' ? $label : $value,
        ];
    };

    $tokens = collect(is_array($values) ? $values : [$values])
        ->map($normalizeValue)
        ->filter(fn (array $item) => $item['value'] !== '')
        ->unique('value')
        ->values()
        ->all();

    $rootClasses = 'dropdown w-full';
    $shellClasses = 'input w-full h-auto min-h-12 items-center gap-2 py-2';

    if ($variant === 'ghost') {
        $shellClasses .= ' input-ghost';
    }

    if ($color) {
        $shellClasses .= ' input-'.$color;
    }

    $shellClasses .= ' '.$resolvedSize['input'];

    $tokenClasses = 'badge badge-soft gap-1 max-w-full';
    $tokenClasses .= $color ? ' badge-'.$color : ' badge-neutral';
    $tokenClasses .= ' '.$resolvedSize['badge'];

    $removeButtonClasses = 'btn btn-ghost btn-xs btn-circle';

    $dataAttributes = [
        'data-module' => 'token-input',
        'data-name' => (string) ($name ?? ''),
        'data-submit-name' => $submitName,
        'data-preset' => (string) $preset,
        'data-delimiters' => json_encode(array_values((array) $delimiters), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'data-paste-separators' => json_encode(array_values((array) $pasteSeparators), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'data-allow-duplicates' => $allowDuplicates ? 'true' : 'false',
        'data-debounce' => (string) (is_numeric($debounce) ? $debounce : 300),
        'data-min-chars' => (string) (is_numeric($minChars) ? $minChars : 2),
        'data-no-results-text' => (string) $noResultsText,
        'data-invalid-text' => (string) $invalidText,
        'data-duplicate-text' => (string) $duplicateText,
        'data-max-items-text' => (string) $maxItemsText,
        'data-token-class' => $tokenClasses,
        'data-token-remove-class' => $removeButtonClasses,
    ];

    if (!is_null($maxItems)) {
        $dataAttributes['data-max-items'] = (string) (int) $maxItems;
    }

    if ($endpoint) {
        $dataAttributes['data-endpoint'] = (string) $endpoint;
        $dataAttributes['data-param'] = (string) ($param ?: 'q');
    }

    if (!is_null($suggestions)) {
        try {
            $dataAttributes['data-suggestions'] = json_encode($suggestions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Throwable $e) {
            // Ignore invalid suggestions payload to keep rendering resilient.
        }
    }
@endphp

<div {{ $attributes->merge(['class' => $rootClasses])->merge($dataAttributes) }}>
    <div class="{{ $shellClasses }}" data-role="shell">
        <div class="flex grow flex-wrap items-center gap-2" data-role="tokens">
            @foreach($tokens as $token)
                <span class="{{ $tokenClasses }}" data-token-item data-value="{{ $token['value'] }}" data-label="{{ $token['label'] }}">
                    <span class="truncate">{{ $token['label'] }}</span>
                    <button
                        type="button"
                        class="{{ $removeButtonClasses }}"
                        data-token-remove
                        aria-label="Remove {{ $token['label'] }}"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </span>
            @endforeach

            <input
                type="text"
                data-role="input"
                class="min-w-32 grow border-0 bg-transparent p-0 text-sm outline-none"
                autocomplete="off"
                placeholder="{{ $placeholder ?? ($preset === 'email' ? 'Add recipients' : 'Add item') }}"
                aria-expanded="false"
                aria-autocomplete="list"
            />
        </div>
    </div>

    <div data-role="hidden-inputs">
        @foreach($tokens as $token)
            @if($submitName !== '')
                <input type="hidden" name="{{ $submitName }}" value="{{ $token['value'] }}" data-token-hidden />
            @endif
        @endforeach
    </div>

    <ul class="dropdown-content menu z-10 mt-2 hidden w-full rounded-box bg-base-100 p-2 shadow" role="listbox" data-role="list"></ul>

    <p class="validator-hint mt-1 hidden text-error" data-role="message"></p>
</div>
