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
    // Afficher un bouton de fermeture (X) en haut à droite
    'closeButton' => true,
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
    'closeLabel' => 'Close modal',
    'initialFocus' => null,
])

@php
    // Construction des classes CSS pour le positionnement (vertical et horizontal).
    $modalClasses = 'modal';
    // Placement vertical : top (haut), middle (centre, défaut), bottom (bas).
    if (in_array($vertical, ['top','middle','bottom'], true)) {
        $modalClasses .= ' modal-' . $vertical;
    }
    // Placement horizontal : start (gauche), end (droite), null (centré).
    if (in_array($horizontal, ['start','end'], true)) {
        $modalClasses .= ' modal-' . $horizontal;
    }

    // Génération d'un ID unique si manquant (requis pour la téléportation et les triggers).
    if (empty($id)) {
        $id = 'modal-'.\Illuminate\Support\Str::uuid();
    }
    $titleId = $title ? $id.'-title' : null;

    // Préparation des attributs du dialog : classes + état open si spécifié.
    $dialogAttrs = $attributes->merge(['class' => $modalClasses]);
    if ($open) {
        $dialogAttrs = $dialogAttrs->merge(['open' => true]);
    }
    if ($titleId) {
        $dialogAttrs = $dialogAttrs->merge(['aria-labelledby' => $titleId]);
    }

    // Mapping des tailles vers les classes max-width Tailwind.
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
    // Classes responsive : w-11/12 sur mobile + max-width sur desktop (si responsive activé).
    $boxResponsiveClasses = $responsive ? ('w-11/12 ' . $maxWidthClass) : $maxWidthClass;
    // Classes de scroll : limite la hauteur et active le scroll vertical si le contenu dépasse.
    $scrollClasses = $scrollable ? ' max-h-[calc(100svh-4rem)] overflow-y-auto' : '';
    $shouldRenderScript = $teleport || $open || $initialFocus;
@endphp

<dialog {{ $dialogAttrs }} @if($id) id="{{ $id }}" @endif>
    {{-- Conteneur principal de la modal : responsive, scrollable, taille personnalisable --}}
    <div class="modal-box {{ $boxResponsiveClasses }}{{ $scrollClasses }} {{ $boxClass }}">
        {{-- En-tête : titre et bouton de fermeture (si l'un ou l'autre est présent) --}}
        @if(isset($header) || $title || $closeButton)
            <div class="flex items-start justify-between gap-4 mb-4">
                @isset($header)
                    <div class="min-w-0 flex-1">
                        {{ $header }}
                    </div>
                @else
                    @if($title)
                        <h3 id="{{ $titleId }}" class="text-lg font-bold">{{ $title }}</h3>
                    @else
                        <div></div>
                    @endif
                @endisset
                {{-- Bouton de fermeture (X) : ferme la modal via l'API native du dialog --}}
                @if($closeButton)
                    <button 
                        type="button" 
                        class="btn btn-sm btn-circle btn-ghost shrink-0" 
                        onclick="document.getElementById('{{ $id }}').close()"
                        aria-label="{{ $closeLabel }}"
                    >
                        <x-bi-x class="size-5" />
                    </button>
                @endif
            </div>
        @endif
        {{-- Contenu principal : slot par défaut --}}
        <div class="mb-4">{{ $slot }}</div>
        {{-- Zone d'actions : slot actions pour les boutons (submit, cancel, etc.) --}}
        @if(isset($footer) || isset($actions))
            <div class="modal-action">
                {{ $footer ?? $actions ?? '' }}
            </div>
        @endif
    </div>
    {{-- Backdrop cliquable : ferme la modal au clic (pattern daisyUI avec form method="dialog") --}}
    @if($backdrop)
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    @endif
</dialog>
{{-- Téléportation et focus initial léger, sans dépendance JS externe. --}}
@if($shouldRenderScript)
    <script>
    (function(){
        try {
            var el = document.getElementById('{{ $id }}');
            if (!el) return;

            @if($teleport)
                if (el.parentElement && el.parentElement.tagName !== 'BODY' && !el.dataset.teleported) {
                    document.body.appendChild(el);
                    el.dataset.teleported = '1';
                }
            @endif

            var focusSelector = @json($initialFocus);
            var fallbackSelector = '[autofocus], [data-autofocus], button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

            function focusInitialTarget() {
                if (!el.open) return;

                var target = focusSelector ? el.querySelector(focusSelector) : null;
                target = target || el.querySelector(fallbackSelector);

                if (target && typeof target.focus === 'function') {
                    target.focus();
                }
            }

            if (el.open) {
                window.requestAnimationFrame(focusInitialTarget);
            }

            if (!el.dataset.focusListener) {
                el.addEventListener('toggle', focusInitialTarget);
                el.dataset.focusListener = '1';
            }
        } catch (e) { /* noop */ }
    })();
    </script>
@endif
