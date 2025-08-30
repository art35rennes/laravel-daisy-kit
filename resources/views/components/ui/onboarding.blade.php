@props([
    // Démarrage automatique à l'affichage
    'start' => false,
    // Autoriser la sortie (Esc, bouton « Quitter »)
    'allowSkip' => true,
    // Activer les raccourcis clavier (←/→/Esc)
    'keyboard' => true,
    // Masque: none | dim | dim-blur
    'mask' => 'dim',
    // Mettre en avant l'élément (ring + scrollIntoView)
    'highlight' => true,
    // Autoriser l'interaction avec la cible (clics passent à travers)
    'interactive' => false,
    // Décalage entre le popover et la cible (px)
    'offset' => 12,
    // Rayon du surlignage (px)
    'radius' => 12,
    // Textes
    'prevText' => 'Précédent',
    'nextText' => 'Suivant',
    'finishText' => 'Terminer',
    'skipText' => 'Quitter',
    // Confirmation à la sortie
    'confirmSkip' => false,
    'confirmText' => "Voulez-vous quitter la visite ?",
    'confirmOkText' => 'Oui',
    'confirmCancelText' => 'Non',
    // Étapes: [ ['target' => '#selector', 'title' => '...', 'content' => '...', 'placement' => 'auto', 'auto' => 0, 'interactive' => null], ... ]
    'steps' => [],
])

@php
    $rootId = $attributes->get('id') ?: ('onboarding-'.uniqid());
    $containerAttrs = $attributes->merge([
        'id' => $rootId,
        'data-onboarding' => '1',
        'data-start' => $start ? '1' : '0',
    ])->class('inline-block');

    $config = [
        'allowSkip' => (bool) $allowSkip,
        'keyboard' => (bool) $keyboard,
        'mask' => in_array($mask, ['none','dim','dim-blur'], true) ? $mask : 'dim',
        'highlight' => (bool) $highlight,
        'interactive' => (bool) $interactive,
        'offset' => (int) $offset,
        'radius' => (int) $radius,
        'labels' => [
            'prev' => (string) $prevText,
            'next' => (string) $nextText,
            'finish' => (string) $finishText,
            'skip' => (string) $skipText,
        ],
        'confirm' => [
            'enabled' => (bool) $confirmSkip,
            'text' => (string) $confirmText,
            'ok' => (string) $confirmOkText,
            'cancel' => (string) $confirmCancelText,
        ],
        'steps' => array_values(array_map(function($step){
            $defaults = [
                'target' => null,
                'title' => null,
                'content' => null,
                'placement' => 'auto',
                'auto' => 0,
                'interactive' => null,
            ];
            $s = array_merge($defaults, is_array($step) ? $step : []);
            $s['placement'] = in_array($s['placement'], ['auto','top','right','bottom','left'], true) ? $s['placement'] : 'auto';
            $s['auto'] = (int) $s['auto'];
            return $s;
        }, $steps)),
    ];
@endphp

<span {{ $containerAttrs }}>
    {{-- Un bouton de déclenchement peut être passé comme slot (optionnel) --}}
    {{ $slot ?? '' }}
    <script type="application/json" data-onboarding-config>
        {!! json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
    @include('daisy::components.partials.assets')
    {{-- Rien d'autre n'est rendu; le JS construit l'UI overlay/popover au runtime --}}
    {{-- Événements dispatchés sur l'élément racine: onboarding:start, onboarding:step, onboarding:finish, onboarding:skip --}}
</span>


