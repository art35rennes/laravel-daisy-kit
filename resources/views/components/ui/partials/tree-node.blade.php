@php
    // Extraction des propriétés du nœud avec support des clés personnalisables (itemKey, labelKey, etc.).
    $id = is_array($node) ? ($node[$itemKey] ?? null) : null;
    $label = is_array($node) ? ($node[$labelKey] ?? '') : '';
    $children = is_array($node) ? ($node[$childrenKey] ?? []) : [];
    // Détection du lazy-loading : le nœud chargera ses enfants à la demande.
    $isLazy = is_array($node) ? (bool)($node[$lazyKey] ?? false) : false;
    // Un nœud a des enfants s'il est lazy OU s'il a un tableau d'enfants non vide.
    $hasChildren = $isLazy || (is_array($children) && count($children) > 0);
    // Les nœuds lazy sont toujours repliés par défaut pour ne charger qu'à l'ouverture.
    $expanded = $isLazy ? false : (bool)($node['expanded'] ?? false);
    $selected = (bool)($node['selected'] ?? false);
    // Un nœud est désactivé s'il est explicitement désactivé OU si son parent est désactivé (cascade).
    $nodeDisabled = (bool)($node['disabled'] ?? false) || (bool)($disabledParent ?? false);
    $isMulti = $selection === 'multiple';
    // Génération d'un ID unique pour le nœud (utilisé pour l'accessibilité et le ciblage JS).
    $liId = $treeId.'-item-'.($id ?? uniqid());
    // Calcul de l'indentation visuelle : 16px par niveau (niveau 1 = 0px, niveau 2 = 16px, etc.).
    $levelPad = max(0, ($level - 1)) * 16;
@endphp

<li id="{{ $liId }}"
    role="treeitem"
    aria-level="{{ $level }}"
    aria-expanded="{{ $hasChildren ? ($expanded ? 'true' : 'false') : 'false' }}"
    aria-selected="{{ $selected ? 'true' : 'false' }}"
    data-id="{{ $id }}"
    @if($isLazy) data-lazy-node="1" @endif
    class="outline-none">

    {{-- En-tête du nœud : bouton toggle, contrôle de sélection, label --}}
    <div class="flex items-center gap-2 px-2 py-1 rounded hover:bg-base-200 focus:bg-base-200"
         style="padding-left: {{ $levelPad }}px"
         data-node-header="1">
        {{-- Bouton toggle pour expand/collapse (uniquement si le nœud a des enfants) --}}
        @if($hasChildren)
            <button type="button"
                    class="btn btn-ghost btn-xs btn-square"
                    aria-label="Toggle"
                    data-toggle="1"
                    tabindex="-1">
                {{-- Icône collapsed (chevron-right) : visible quand le nœud est replié --}}
                <span data-icon-collapsed class="@if($expanded) hidden @endif">
                    <x-bi-chevron-right class="size-4" />
                </span>
                {{-- Icône expanded (chevron-down) : visible quand le nœud est déplié --}}
                <span data-icon-expanded class="@if(!$expanded) hidden @endif">
                    <x-bi-chevron-down class="size-4" />
                </span>
            </button>
        @else
            {{-- Espaceur pour l'alignement visuel (nœuds sans enfants n'ont pas de toggle) --}}
            <span class="inline-block w-6"></span>
        @endif

        {{-- Contrôle de sélection : radio pour sélection unique, checkbox pour multiple --}}
        @if($selection === 'single')
            <x-daisy::ui.inputs.radio name="{{ $name }}" :checked="$selected" :disabled="$nodeDisabled" :size="$controlSize" class="shrink-0" tabindex="-1" />
        @elseif($selection === 'multiple')
            <x-daisy::ui.inputs.checkbox :checked="$selected" :disabled="$nodeDisabled" :size="$controlSize" class="shrink-0" tabindex="-1" />
        @endif

        {{-- Label du nœud : texte principal, désactivé visuellement si le nœud est disabled --}}
        <span class="flex-1 cursor-default select-none @if($nodeDisabled) opacity-50 @endif" data-label="1">{{ $label }}</span>
    </div>

    {{-- Liste des enfants : rendue uniquement si le nœud a des enfants et est expanded --}}
    @if($hasChildren)
        <ul role="group" class="pl-2 ml-4 border-l @if(!$expanded) hidden @endif" data-children="1">
            @if(!$isLazy)
                {{-- Rendu récursif : chaque enfant est rendu via le même partial (incrément du niveau) --}}
                @foreach($children as $child)
                    @include('daisy::components.ui.partials.tree-node', [
                        'node' => $child,
                        'level' => $level + 1,
                        'treeId' => $treeId,
                        'selection' => $selection,
                        'name' => $name,
                        'itemKey' => $itemKey,
                        'labelKey' => $labelKey,
                        'childrenKey' => $childrenKey,
                        'lazyKey' => $lazyKey,
                        'controlSize' => $controlSize,
                        'disabledParent' => $nodeDisabled, // Propagation de l'état disabled aux enfants.
                    ])
                @endforeach
            @else
                {{-- Placeholder pour le lazy-loading : sera remplacé par le JS lors du chargement --}}
                <li class="px-2 py-1 text-sm opacity-60 hidden" data-lazy-placeholder="1">Loading…</li>
            @endif
        </ul>
    @endif
</li>


