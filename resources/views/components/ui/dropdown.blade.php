@props([
    'label' => null,
    'end' => false,
    'hover' => false,
])

@php
    $root = 'dropdown';
    if ($end) $root .= ' dropdown-end';
    if ($hover) $root .= ' dropdown-hover';
@endphp

<div {{ $attributes->merge(['class' => $root]) }}>
    <div tabindex="0" role="button" class="btn m-1">
        {{ $label ?? 'Open' }}
    </div>
    <ul tabindex="0" class="menu dropdown-content bg-base-100 rounded-box z-[1] w-52 p-2 shadow">
        {{ $slot }}
    </ul>
</div>


