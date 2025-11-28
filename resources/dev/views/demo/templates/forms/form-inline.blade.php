@php
    $filters = view('daisy-dev::demo.templates.forms.partials.inline-filters')->render();
    $advanced = view('daisy-dev::demo.templates.forms.partials.inline-advanced')->render();

    $activeFilters = [];
    if (request()->has('status')) {
        $activeFilters[] = ['label' => 'Statut', 'value' => request('status'), 'param' => 'status'];
    }
    if (request()->has('type')) {
        $activeFilters[] = ['label' => 'Type', 'value' => request('type'), 'param' => 'type'];
    }
    if (request()->has('category')) {
        $activeFilters[] = ['label' => 'Catégorie', 'value' => request('category'), 'param' => 'category'];
    }
@endphp

<x-daisy::layout.navbar-layout title="Formulaire inline / Filtres">
    <div class="max-w-6xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold mb-2">Recherche et filtres</h1>
            <p class="text-base-content/70">Exemple de formulaire de filtres avec tokens actifs et panel avancé</p>
        </div>

        @include('daisy::templates.form-inline', [
            'action' => route('templates.forms.inline'),
            'method' => 'GET',
            'size' => 'sm',
            'showReset' => true,
            'activeFilters' => $activeFilters,
            'showAdvanced' => true,
            'advancedTitle' => 'Filtres avancés',
            'filters' => $filters,
            'advanced' => $advanced,
        ])

        @if(request()->anyFilled(['search', 'status', 'type', 'category', 'date_from', 'date_to', 'min_price', 'max_price']))
            <div class="mt-8 card card-border bg-base-200">
                <div class="card-body">
                    <h3 class="card-title">Résultats de la recherche</h3>
                    <p class="text-sm text-base-content/70">
                        Filtres appliqués : 
                        @if(request('search'))
                            Recherche: "{{ request('search') }}"
                        @endif
                        @if(request('status'))
                            | Statut: {{ request('status') }}
                        @endif
                        @if(request('type'))
                            | Type: {{ request('type') }}
                        @endif
                        @if(request('category'))
                            | Catégorie: {{ request('category') }}
                        @endif
                    </p>
                    <div class="mt-4">
                        <p class="text-sm">Aucun résultat pour le moment. Les filtres fonctionnent correctement !</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-daisy::layout.navbar-layout>
