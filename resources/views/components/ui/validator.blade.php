@props([
    'state' => null, // success|warning|error|info
    'message' => null,
])

@php
    $hintClass = match($state) {
        'success' => 'text-success',
        'warning' => 'text-warning',
        'error' => 'text-error',
        'info' => 'text-info',
        default => 'text-base-content/70',
    };
@endphp

<div class="form-control w-full">
    {{ $slot }}
    @if($message)
        <label class="label">
            <span class="label-text-alt {{ $hintClass }}">{{ $message }}</span>
        </label>
    @endif
</div>
