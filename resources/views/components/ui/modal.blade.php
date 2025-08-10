@props([
    'open' => false,
    'id' => null,
    'title' => null,
])

@php
    $dialogAttrs = $attributes->merge(['class' => 'modal']);
    if ($open) {
        $dialogAttrs = $dialogAttrs->merge(['open' => true]);
    }
@endphp

<dialog {{ $dialogAttrs }} @if($id) id="{{ $id }}" @endif>
    <div class="modal-box">
        @if($title)
            <h3 class="text-lg font-bold mb-2">{{ $title }}</h3>
        @endif
        <div class="mb-4">{{ $slot }}</div>
        <div class="modal-action">
            {{ $actions ?? '' }}
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>


