@props([
    'size' => 'md',        // xs | sm | md | lg | xl
    'variant' => null,     // null | ghost
    'color' => null,       // primary | secondary | accent | info | success | warning | error | neutral
    'disabled' => false,
    'rows' => 4,
    'name' => null,
    'id' => null,
    'value' => null,
    'bindOld' => true,
    'error' => null,
    'describedBy' => null,
])

@php
    $sizeMap = [
        'xs' => 'textarea-xs',
        'sm' => 'textarea-sm',
        'md' => 'textarea-md',
        'lg' => 'textarea-lg',
        'xl' => 'textarea-xl',
    ];

    $classes = 'textarea w-full';

    if ($variant === 'ghost') {
        $classes .= ' textarea-ghost';
    }

    if ($color) {
        $classes .= ' textarea-'.$color;
    }

    $sharedErrors = view()->shared('errors');
    $localErrors = $errors ?? null;
    $laravelErrors = $localErrors instanceof \Illuminate\Support\ViewErrorBag && $localErrors->any()
        ? $localErrors
        : ($sharedErrors instanceof \Illuminate\Support\ViewErrorBag ? $sharedErrors : new \Illuminate\Support\ViewErrorBag());
    $errorMessage = $error ?? ($name && method_exists($laravelErrors, 'first') ? $laravelErrors->first($name) : null);
    $hasError = filled($errorMessage);

    if ($hasError) {
        $classes .= ' textarea-error';
    }

    if (isset($sizeMap[$size])) {
        $classes .= ' '.$sizeMap[$size];
    }

    $textareaId = $id ?: ($name ? preg_replace('/[^A-Za-z0-9_-]+/', '-', trim((string) $name, '[]')) : null);
    $oldInput = $name ? data_get(session()->get('_old_input', []), $name, old($name, $value)) : $value;
    $textareaValue = $bindOld && $name ? $oldInput : $value;
    $resolvedDescribedBy = $describedBy ?: ($hasError && $textareaId ? $textareaId.'-error' : null);
    $textareaContent = $textareaValue ?? $slot;
    $textareaAttributes = $attributes
        ->merge(['class' => $classes])
        ->merge(array_filter([
            'id' => $textareaId,
            'name' => $name,
            'aria-invalid' => $hasError ? 'true' : null,
            'aria-describedby' => $resolvedDescribedBy,
        ], static fn ($attributeValue) => ! is_null($attributeValue)));
@endphp

<textarea rows="{{ $rows }}" @disabled($disabled) {{ $textareaAttributes }}>{{ $textareaContent }}</textarea>

