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
])

@php
    $treeId = $attributes->get('id') ?? ('tree-'.uniqid());
    $isMulti = $selection === 'multiple';
    $groupName = $name ?? ($treeId.'-group');
    // Classes par défaut (utilise menu de DaisyUI pour un style cohérent)
    $baseClasses = 'menu menu-sm bg-base-100 rounded-box p-2';
@endphp

<div data-treeview="1"
     data-selection="{{ $selection ?? '' }}"
     data-persist="{{ $persist ? 'true' : 'false' }}"
     data-select-label="{{ $selectOnLabel ? 'true' : 'false' }}"
     id="{{ $treeId }}"
     class="w-full">
    <ul role="tree"
        aria-multiselectable="{{ $isMulti ? 'true' : 'false' }}"
        tabindex="0"
        data-return="{{ $return }}"
        data-disabled="{{ $disabled ? 'true' : 'false' }}"
        {{ $attributes->merge(['class' => $baseClasses]) }}>
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
