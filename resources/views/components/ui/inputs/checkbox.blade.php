@props([
    'color' => null, // primary | secondary | accent | info | success | warning | error | neutral
    'size' => null,  // xs | sm | md | lg | xl
    'checked' => false,
    'disabled' => false,
    'indeterminate' => false,
    'name' => null,
    'id' => null,
    'value' => '1',
    'uncheckedValue' => null,
    'bindOld' => true,
    'error' => null,
    'describedBy' => null,
])

@php
    $sizeMap = [
        'xs' => 'checkbox-xs',
        'sm' => 'checkbox-sm',
        'md' => 'checkbox-md',
        'lg' => 'checkbox-lg',
        'xl' => 'checkbox-xl',
    ];

    $classes = 'checkbox';

    if ($color) {
        $classes .= ' checkbox-'.$color;
    }

    if ($size && isset($sizeMap[$size])) {
        $classes .= ' '.$sizeMap[$size];
    }

    $sharedErrors = view()->shared('errors');
    $localErrors = $errors ?? null;
    $laravelErrors = $localErrors instanceof \Illuminate\Support\ViewErrorBag && $localErrors->any()
        ? $localErrors
        : ($sharedErrors instanceof \Illuminate\Support\ViewErrorBag ? $sharedErrors : new \Illuminate\Support\ViewErrorBag());
    $errorMessage = $error ?? ($name && method_exists($laravelErrors, 'first') ? $laravelErrors->first($name) : null);
    $hasError = filled($errorMessage);

    if ($hasError) {
        $classes .= ' checkbox-error';
    }

    $checkboxId = $id ?: ($name ? preg_replace('/[^A-Za-z0-9_-]+/', '-', trim((string) $name, '[]')) : null);
    $oldInputExists = $name && data_get(session()->get('_old_input', []), $name, '__missing__') !== '__missing__';
    $oldInputValue = $name ? data_get(session()->get('_old_input', []), $name) : null;
    $resolvedChecked = $bindOld && $oldInputExists
        ? in_array((string) $oldInputValue, ['1', 'true', 'on', (string) $value], true)
        : (bool) $checked;
    $resolvedDescribedBy = $describedBy ?: ($hasError && $checkboxId ? $checkboxId.'-error' : null);
    $checkboxAttributes = $attributes
        ->merge(['class' => $classes])
        ->merge(array_filter([
            'id' => $checkboxId,
            'name' => $name,
            'value' => $value,
            'aria-invalid' => $hasError ? 'true' : null,
            'aria-describedby' => $resolvedDescribedBy,
        ], static fn ($attributeValue) => ! is_null($attributeValue)));
@endphp

@if($name && ! is_null($uncheckedValue))
    <input type="hidden" name="{{ $name }}" value="{{ $uncheckedValue }}">
@endif

<input type="checkbox" @checked($resolvedChecked && !$indeterminate) @disabled($disabled) @if($indeterminate) aria-checked="mixed" data-indeterminate="true" @endif {{ $checkboxAttributes }} />

@if($indeterminate)
	@include('daisy::components.partials.assets')
@endif

