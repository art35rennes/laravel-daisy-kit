@props([
    'state' => null, // success|warning|error|info
    'message' => null,
    'as' => 'div', // wrapper tag
    'full' => true, // apply w-full on wrapper
    'hintHidden' => false, // add hidden on hint when not visible
])

@php
    $hintClass = match($state) {
        'success' => 'text-success',
        'warning' => 'text-warning',
        'error' => 'text-error',
        'info' => 'text-info',
        default => 'text-base-content/70',
    };
    $wrapperClasses = trim(($full ? 'w-full ' : '').'form-control');
@endphp

<{{ $as }} {{ $attributes->merge(['class' => $wrapperClasses]) }}>
    {{ $slot }}
    @if($message)
        <p class="validator-hint {{ $hintHidden ? 'hidden' : '' }} {{ $hintClass }}">{{ $message }}</p>
    @endif
</{{ $as }}>
