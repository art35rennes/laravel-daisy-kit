@props([
    'title' => null,
    'description' => null,
    'id' => null,
    // Breakpoint pour passer en 2 colonnes: sm|md|lg|xl|2xl
    'breakpoint' => 'lg',
    // Ratio des colonnes (desktop)
    'categoryWidth' => '1/3', // 1/4|1/3|1/2
    'contentWidth' => '2/3',  // 3/4|2/3|1/2
    // Espacement interne entre colonnes/éléments
    'gap' => 8,
    // Bordure top optionnelle pour séparer visuellement
    'borderTop' => false,
])

{{--
    CRUD Section Component

    Section d'une page CRUD avec layout 2 colonnes responsive :
    - Colonne gauche (category) : titre et description (1/3 ou 1/4 de la largeur)
    - Colonne droite (content) : contenu principal (2/3 ou 3/4 de la largeur)

    Sur mobile : les colonnes s'empilent verticalement (col-span-12).

    Usage:
        <x-daisy::ui.layout.crud-section title="..." description="...">
            Contenu principal
        </x-daisy::ui.layout.crud-section>
--}}

@php
    // Map des breakpoints vers classes grid-cols-12 (évite les classes entièrement dynamiques).
    $bpMap = [
        'sm' => 'sm:grid-cols-12',
        'md' => 'md:grid-cols-12',
        'lg' => 'lg:grid-cols-12',
        'xl' => 'xl:grid-cols-12',
        '2xl' => '2xl:grid-cols-12',
    ];
    $bp = $bpMap[$breakpoint] ?? $bpMap['lg'];

    // Map des spans pour la colonne "catégorie" (titre/description) selon le ratio et le breakpoint.
    $categoryMap = [
        '1/4' => [
            'sm' => 'sm:col-span-12',
            'md' => 'md:col-span-3',
            'lg' => 'lg:col-span-3',
            'xl' => 'xl:col-span-3',
            '2xl' => '2xl:col-span-3',
        ],
        '1/3' => [
            'sm' => 'sm:col-span-12',
            'md' => 'md:col-span-4',
            'lg' => 'lg:col-span-4',
            'xl' => 'xl:col-span-4',
            '2xl' => '2xl:col-span-4',
        ],
        '1/2' => [
            'sm' => 'sm:col-span-12',
            'md' => 'md:col-span-6',
            'lg' => 'lg:col-span-6',
            'xl' => 'xl:col-span-6',
            '2xl' => '2xl:col-span-6',
        ],
    ];

    // Map des spans pour la colonne "contenu" (slot principal) selon le ratio et le breakpoint.
    $contentMap = [
        '3/4' => [
            'sm' => 'sm:col-span-12',
            'md' => 'md:col-span-9',
            'lg' => 'lg:col-span-9',
            'xl' => 'xl:col-span-9',
            '2xl' => '2xl:col-span-9',
        ],
        '2/3' => [
            'sm' => 'sm:col-span-12',
            'md' => 'md:col-span-8',
            'lg' => 'lg:col-span-8',
            'xl' => 'xl:col-span-8',
            '2xl' => '2xl:col-span-8',
        ],
        '1/2' => [
            'sm' => 'sm:col-span-12',
            'md' => 'md:col-span-6',
            'lg' => 'lg:col-span-6',
            'xl' => 'xl:col-span-6',
            '2xl' => '2xl:col-span-6',
        ],
    ];

    $gapValue = is_numeric($gap) ? (int) $gap : 8;
    // Construction de la grille : 1 colonne sur mobile, 12 colonnes à partir du breakpoint.
    $root = 'grid grid-cols-1 '.$bp.' gap-'.$gapValue.($borderTop ? ' pt-8 mt-8 border-t' : '');

    // Sélection des classes selon le breakpoint demandé (fallback : lg).
    $bpKey = in_array($breakpoint, ['sm','md','lg','xl','2xl']) ? $breakpoint : 'lg';
    $categorySpans = $categoryMap[$categoryWidth] ?? $categoryMap['1/3'];
    $contentSpans = $contentMap[$contentWidth] ?? $contentMap['2/3'];

    // Construction des classes pour chaque colonne : espacement vertical + spans responsive.
    $categoryClass = 'space-y-1';
    $contentClass = 'space-y-4';
    $categoryClass .= ' '.($categorySpans[$bpKey] ?? $categoryMap['1/3'][$bpKey]);
    $contentClass .= ' '.($contentSpans[$bpKey] ?? $contentMap['2/3'][$bpKey]);
@endphp

<section {{ $attributes->merge(['id' => $id, 'class' => $root]) }}>
    <div class="{{ $categoryClass }}">
        @if($title)
            <h2 class="text-base font-medium">{{ $title }}</h2>
        @endif
        @if($description)
            <p class="text-sm text-base-content/70">
                {{ $description }}
            </p>
        @endif
    </div>

    <div class="{{ $contentClass }}">
        {{ $slot }}

        @isset($actions)
            <div class="mt-4 flex items-center justify-end gap-3">
                {{ $actions }}
            </div>
        @endisset
    </div>
</section>


