@props([
    'open' => false,
    'id' => null,
    'title' => null,
    // Positionnement DaisyUI
    // vertical: top | middle | bottom
    'vertical' => 'middle',
    // horizontal: start | end | null
    'horizontal' => null,
    // Afficher le backdrop cliquable pour fermer (méthode dialog)
    'backdrop' => true,
    // Classes supplémentaires sur .modal-box (ex: max-w-xl)
    'boxClass' => '',
])

@php
    $modalClasses = 'modal';
    // Vertical placement
    if (in_array($vertical, ['top','middle','bottom'], true)) {
        $modalClasses .= ' modal-' . $vertical;
    }
    // Horizontal placement
    if (in_array($horizontal, ['start','end'], true)) {
        $modalClasses .= ' modal-' . $horizontal;
    }

    $dialogAttrs = $attributes->merge(['class' => $modalClasses]);
    if ($open) {
        $dialogAttrs = $dialogAttrs->merge(['open' => true]);
    }
@endphp

<dialog {{ $dialogAttrs }} @if($id) id="{{ $id }}" @endif>
    <div class="modal-box {{ $boxClass }}">
        @if($title)
            <h3 class="text-lg font-bold mb-2">{{ $title }}</h3>
        @endif
        <div class="mb-4">{{ $slot }}</div>
        <div class="modal-action">
            {{ $actions ?? '' }}
        </div>
    </div>
    @if($backdrop)
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    @endif
</dialog>


