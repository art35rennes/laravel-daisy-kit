@props([
    'showSearch' => true,
    'showFilters' => true,
    'searchPlaceholder' => __('changelog.search_placeholder'),
    'filterName' => 'changelog-filter',
    'filterItems' => [], // ['added', 'changed', 'fixed', 'removed', 'security']
])

@php
    // Si filterItems est vide, utiliser les types par défaut
    if (empty($filterItems)) {
        $filterItems = ['added', 'changed', 'fixed', 'removed', 'security'];
    }

    // Formater les items pour le composant filter
    $formattedFilterItems = array_map(function($type) {
        return [
            'label' => __('changelog.'.$type),
            'checked' => false,
        ];
    }, $filterItems);

    // Ajouter "Tous les types" en premier
    array_unshift($formattedFilterItems, [
        'label' => __('changelog.all_types'),
        'checked' => true,
    ]);
@endphp

<div class="changelog-toolbar flex flex-col gap-4 rounded-3xl border border-base-300 bg-base-100 p-6 shadow-sm">
    @if($showSearch)
        <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-base-content/40">
                <x-daisy::ui.advanced.icon name="search" size="sm" />
            </span>
            <x-daisy::ui.inputs.input
                type="text"
                :placeholder="$searchPlaceholder"
                class="w-full pl-10"
                data-changelog-search
            />
        </div>
    @endif

    @if($showFilters)
        <div class="flex flex-wrap gap-2">
            <x-daisy::ui.advanced.filter
                :name="$filterName"
                :items="$formattedFilterItems"
                :useForm="false"
                :resetLabel="__('changelog.all_types')"
                class="filter rounded-full bg-base-200/60 p-1"
            />
        </div>
    @endif
</div>

