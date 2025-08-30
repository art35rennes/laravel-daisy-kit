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
    // Responsive & taille
    'responsive' => true,
    // xs|sm|md|lg|xl|2xl|3xl|4xl|5xl|6xl|7xl (mappe sur max-w-*)
    'size' => null,
    // Active un scroll interne si le contenu dépasse la hauteur de l'écran
    'scrollable' => true,
    // Déplace le <dialog> sous <body> pour éviter les problèmes de positionnement
    // quand un parent a transform/filter/perspective (fixe => relatif au parent)
    'teleport' => true,
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

    // Id auto si manquant (utile pour téléportation et triggers)
    if (empty($id)) {
        $id = 'modal-'.\Illuminate\Support\Str::uuid();
    }

    $dialogAttrs = $attributes->merge(['class' => $modalClasses]);
    if ($open) {
        $dialogAttrs = $dialogAttrs->merge(['open' => true]);
    }

    // Classes responsive et taille pour .modal-box
    $sizeToMax = [
        'xs' => 'max-w-xs',
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        '6xl' => 'max-w-6xl',
        '7xl' => 'max-w-7xl',
    ];
    $maxWidthClass = $sizeToMax[$size] ?? 'max-w-lg';
    $boxResponsiveClasses = $responsive ? ('w-11/12 ' . $maxWidthClass) : $maxWidthClass;
    $scrollClasses = $scrollable ? ' max-h-[calc(100svh-4rem)] overflow-y-auto' : '';
@endphp

<dialog {{ $dialogAttrs }} @if($id) id="{{ $id }}" @endif>
    <div class="modal-box {{ $boxResponsiveClasses }}{{ $scrollClasses }} {{ $boxClass }}">
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
@if($teleport)
    <script>
    (function(){
        try {
            var el = document.getElementById('{{ $id }}');
            if (!el) return;
            if (el.parentElement && el.parentElement.tagName !== 'BODY' && !el.dataset.teleported) {
                document.body.appendChild(el);
                el.dataset.teleported = '1';
            }
        } catch (e) { /* noop */ }
    })();
    </script>
@endif


