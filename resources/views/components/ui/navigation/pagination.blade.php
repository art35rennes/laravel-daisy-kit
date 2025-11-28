@props([
    'total' => 1,
    'current' => 1,
    'size' => null, // xs|sm|md|lg|xl
    'edges' => true,
    'maxButtons' => 7,
    'prevLabel' => '«',
    'nextLabel' => '»',
    'equalPrevNext' => false,
    'outlinePrevNext' => false,
    // Responsive
    'responsive' => true,
    'mobileLabel' => 'Page :current',
    // Améliorations de style
    'color' => null, // primary|secondary|accent|neutral|info|success|warning|error
    'outline' => false, // applique btn-outline aux boutons numérotés
])

@php
    // Normalisation des valeurs : total et current doivent être valides (>= 1, current <= total).
    $total = max(1, (int) $total);
    $current = max(1, min($current, $total));

    // Construction des classes CSS pour les boutons (taille, couleur, outline).
    $btnSize = '';
    if (in_array($size, ['xs','sm','md','lg','xl'], true)) {
        $btnSize = ' btn-'.$size;
    }
    $btnColor = '';
    if (in_array($color, ['primary','secondary','accent','neutral','info','success','warning','error'], true)) {
        $btnColor = ' btn-'.$color;
    }
    $btnOutline = $outline ? ' btn-outline' : '';

    // Formatage du label mobile : remplace :current et :total par les valeurs réelles.
    $mobileInfo = str_replace([':current', ':total'], [$current, $total], (string) $mobileLabel);

    // Construction de la liste des pages avec ellipsis (null) pour les grandes listes.
    // Algorithme : affiche toujours la première et dernière page, et une fenêtre autour de current.
    $maxButtons = max(3, (int) $maxButtons);
    $pages = [];
    if ($total <= $maxButtons) {
        // Cas simple : toutes les pages tiennent dans maxButtons.
        for ($i = 1; $i <= $total; $i++) $pages[] = $i;
    } else {
        // Cas complexe : fenêtre glissante avec ellipsis.
        $pages[] = 1; // Première page toujours visible.
        $window = $maxButtons - 2; // Nombre de pages dans la fenêtre (sans première/dernière).
        $half = (int) floor($window / 2);
        // Calcul de la fenêtre centrée sur current.
        $start = max(2, $current - $half);
        $end = min($total - 1, $start + $window - 1);
        $start = max(2, $end - $window + 1);
        // Ellipsis avant la fenêtre si nécessaire.
        if ($start > 2) $pages[] = null;
        // Pages de la fenêtre.
        for ($i = $start; $i <= $end; $i++) $pages[] = $i;
        // Ellipsis après la fenêtre si nécessaire.
        if ($end < $total - 1) $pages[] = null;
        $pages[] = $total; // Dernière page toujours visible.
    }
@endphp

@if($equalPrevNext)
    {{-- Mode simple : uniquement boutons Précédent/Suivant (2 colonnes égales) --}}
    <div class="join grid grid-cols-2">
        <button class="join-item btn{{ $btnSize }}{{ $btnColor }} {{ $outlinePrevNext ? 'btn-outline' : '' }}" @disabled($current === 1)>{{ $prevLabel }}</button>
        <button class="join-item btn{{ $btnSize }}{{ $btnColor }} {{ $outlinePrevNext ? 'btn-outline' : '' }}" @disabled($current === $total)>{{ $nextLabel }}</button>
    </div>
@else
    {{-- Mode complet : boutons Précédent/Suivant + numéros de pages avec ellipsis --}}
    <div class="join">
        {{-- Bouton Précédent : désactivé sur la première page --}}
        @if($edges)
            <button class="btn join-item{{ $btnSize }}{{ $btnColor }}{{ $btnOutline }}" @disabled($current === 1) aria-label="Previous">{{ $prevLabel }}</button>
        @endif
        {{-- Info mobile : affiche "Page X/Y" sur petits écrans (remplace les numéros) --}}
        @if($responsive)
            <span class="btn join-item{{ $btnSize }}{{ $btnColor }}{{ $btnOutline }} sm:hidden" aria-label="Page info">{{ $mobileInfo }}</span>
        @endif
        {{-- Numéros de pages : ellipsis (null) ou numéros réels --}}
        @foreach($pages as $p)
            @if(is_null($p))
                {{-- Ellipsis : indique qu'il y a des pages entre les numéros affichés --}}
                <button class="btn join-item{{ $btnSize }} btn-disabled hidden sm:inline-flex">…</button>
            @else
                {{-- Numéro de page : btn-active si c'est la page courante --}}
                <button class="btn join-item{{ $btnSize }}{{ $btnColor }}{{ $btnOutline }} {{ $p === $current ? 'btn-active' : '' }} hidden sm:inline-flex">{{ $p }}</button>
            @endif
        @endforeach
        {{-- Bouton Suivant : désactivé sur la dernière page --}}
        @if($edges)
            <button class="btn join-item{{ $btnSize }}{{ $btnColor }}{{ $btnOutline }}" @disabled($current === $total) aria-label="Next">{{ $nextLabel }}</button>
        @endif
    </div>
@endif
