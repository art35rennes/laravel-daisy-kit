@props([
    // Données hiérarchiques: tableau récursif
    // Chaque nœud: ['id' => string|int, 'label' => string, 'children' => [...], 'lazy' => bool, 'expanded' => bool, 'selected' => bool]
    'data' => [],
    // Mode de sélection: 'single'|'multiple'|null
    'selection' => 'single',
    // Persister l'état d'expansion dans sessionStorage (clé: treeview:<id>)
    'persist' => false,
    // Double-clic sur le libellé pour toggler
    'selectOnLabel' => true,
    // Clés personnalisables
    'itemKey' => 'id',
    'labelKey' => 'label',
    'childrenKey' => 'children',
    'lazyKey' => 'lazy',
    // Nom du groupe (pour radio)
    'name' => null,
    // Taille des contrôles (checkbox/radio): xs|sm|md|lg|xl
    'controlSize' => 'sm',
    // Désactiver toute sélection sur l'arbre
    'disabled' => false,
    // Stratégie de retour pour getSelected(): 'nodes' (par défaut) ou 'leaves'
    'return' => 'nodes',
    // Lazy-loading intégré (optionnel): si fourni, le JS fera un fetch auto lors de tree:lazy
    // URL de l'endpoint REST (GET) qui renvoie un JSON [{id, label, disabled?, lazy?}]
    'lazyUrl' => null,
    // Nom du paramètre de query pour passer l'id du nœud (par défaut: node)
    'lazyParam' => 'node',
    // Recherche
    'search' => false,
    'searchMin' => 2,
    'searchDebounce' => 300,
    'searchUrl' => null,
    'searchParam' => 'q',
    'searchPlaceholder' => 'Rechercher…',
    'searchButton' => true,
    'searchAuto' => true,
    // Surcharge du nom de module JS (optionnel)
    'module' => null,
])

@php
    // Génération d'un ID unique pour l'arbre si non fourni.
    $treeId = $attributes->get('id') ?? ('tree-'.uniqid());
    $isMulti = $selection === 'multiple';
    // Nom du groupe pour les radio buttons (sélection unique) : doit être unique par arbre.
    $groupName = $name ?? ($treeId.'-group');
    // Classes par défaut (utilise menu de DaisyUI pour un style cohérent).
    $baseClasses = 'menu menu-sm bg-base-100 rounded-box p-2';
@endphp

{{-- Conteneur principal : prépare les attributs data-* pour l'initialisation JavaScript --}}
<div data-module="{{ $module ?? 'treeview' }}" data-treeview="1"
     data-selection="{{ $selection ?? '' }}"
     data-persist="{{ $persist ? 'true' : 'false' }}"
     data-select-label="{{ $selectOnLabel ? 'true' : 'false' }}"
      data-control-size="{{ $controlSize }}"
      @if($lazyUrl) data-lazy-url="{{ $lazyUrl }}" @endif
      @if($lazyParam) data-lazy-param="{{ $lazyParam }}" @endif
      data-search-enabled="{{ $search ? 'true' : 'false' }}"
      @if($search) data-search-min="{{ (int) $searchMin }}" @endif
      @if($search) data-search-debounce="{{ (int) $searchDebounce }}" @endif
      @if($searchUrl) data-search-url="{{ $searchUrl }}" @endif
      @if($searchParam) data-search-param="{{ $searchParam }}" @endif
      @if($search) data-search-auto="{{ $searchAuto ? 'true' : 'false' }}" @endif
     id="{{ $treeId }}"
     class="w-full">

    {{-- Champ de recherche optionnel : filtre les nœuds visibles dans l'arbre --}}
    @if($search)
        <div class="join w-full mb-2" data-tree-search-container="1">
            <input type="text"
                   class="input input-sm input-bordered join-item w-full"
                   placeholder="{{ $searchPlaceholder }}"
                   data-tree-search="1" />
            {{-- Bouton de recherche optionnel (si recherche manuelle, pas automatique) --}}
            @if($searchButton && !$searchAuto)
                <button type="button" class="btn btn-sm join-item" data-tree-search-btn="1">Rechercher</button>
            @endif
        </div>
    @endif

    {{-- Liste racine de l'arbre : structure récursive gérée par le partial tree-node --}}
    <ul role="tree"
        aria-multiselectable="{{ $isMulti ? 'true' : 'false' }}"
        tabindex="0"
        data-return="{{ $return }}"
        data-disabled="{{ $disabled ? 'true' : 'false' }}"
         @if($selection === 'single') data-radio-name="{{ $groupName }}" @endif
        {{ $attributes->merge(['class' => $baseClasses]) }}>
        {{-- Rendu récursif : chaque nœud racine déclenche le rendu de ses enfants via tree-node --}}
        @foreach(($data ?? []) as $node)
            @include('daisy::components.ui.partials.tree-node', [
                'node' => $node,
                'level' => 1,
                'treeId' => $treeId,
                'selection' => $selection,
                'name' => $groupName,
                'itemKey' => $itemKey,
                'labelKey' => $labelKey,
                'childrenKey' => $childrenKey,
                'lazyKey' => $lazyKey,
                'controlSize' => $controlSize,
            ])
        @endforeach
    </ul>
</div>

@include('daisy::components.partials.assets')
