{{-- @template-label Operations dashboard --}}
{{-- @template-description Dashboard opérationnel KPI avec filtres, sections terrain/bureau/gestion, graphiques et accès rapides. --}}
{{-- @template-type reusable --}}
{{-- @template-route templates.reporting.operations-dashboard --}}
{{-- @template-tags reporting, dashboard, kpi, charts --}}

@props([
    'title' => 'Accueil',
    'subtitle' => 'Vue d’ensemble de vos enquêtes',
    'theme' => null,
    'brand' => 'SUEZ',
    'product' => 'ECA3',
    'updatedAt' => '16/06/2026 à 08:30',
    'perimeter' => 'Agence Bretagne Sud',
    'period' => '01/06/2026 - 16/06/2026',
    'contract' => 'Tous les contrats',
    'surveyType' => 'Tous (ANC / RAC)',
    'detailedUrl' => '#',
    'terrainUrl' => '#',
    'officeUrl' => '#',
    'managementUrl' => '#',
    'userName' => 'Sophie Martin',
    'userRole' => 'Secrétaire technique',
])

@php
    $navItems = [
        ['label' => 'Accueil', 'icon' => 'bi-house', 'active' => true],
        ['label' => 'Interventions', 'icon' => 'bi-clipboard-check'],
        ['label' => 'Documents', 'icon' => 'bi-file-earmark-text'],
        ['label' => 'Exports', 'icon' => 'bi-download'],
        ['label' => 'Paramètres', 'icon' => 'bi-gear'],
    ];

    $summary = [
        ['label' => 'Interventions', 'value' => '128', 'detail' => '+8% sur la période', 'tone' => 'success'],
        ['label' => 'Conformité', 'value' => '87%', 'detail' => '+5 pts vs période précédente', 'tone' => 'success'],
        ['label' => 'À traiter', 'value' => '40', 'detail' => '24 à valider, 16 rapports', 'tone' => 'primary'],
        ['label' => 'Points à risque', 'value' => '19', 'detail' => '14 retards, 5 non conformes', 'tone' => 'error'],
    ];

    $sections = [
        [
            'id' => 'terrain',
            'marker' => 'A',
            'title' => 'Terrain',
            'subtitle' => 'Prioriser les interventions et anomalies visibles aujourd’hui',
            'tone' => 'success',
            'url' => $terrainUrl,
            'link' => 'Interventions terrain',
            'kpis' => [
                ['label' => 'À réaliser', 'value' => '38', 'unit' => 'Interventions', 'trend' => '+12% vs période précédente', 'tone' => 'success', 'icon' => 'bi-calendar-check', 'sparkline' => '14,18 34,18 54,13 74,17 94,8 114,14 134,6'],
                ['label' => 'En cours', 'value' => '12', 'unit' => 'Interventions', 'trend' => 'Stable', 'tone' => 'neutral', 'icon' => 'bi-activity'],
                ['label' => 'À corriger / refaire', 'value' => '7', 'unit' => 'Interventions', 'trend' => '+2 vs période précédente', 'tone' => 'error', 'icon' => 'bi-exclamation-triangle'],
                ['label' => 'Non conformes', 'value' => '5', 'unit' => 'Interventions', 'trend' => '14% du total terrain', 'tone' => 'error', 'icon' => 'bi-shield-exclamation'],
            ],
            'panels' => [
                [
                    'type' => 'donut',
                    'title' => 'Répartition par statut',
                    'center' => '69',
                    'centerLabel' => 'terrain',
                    'segments' => [
                        ['label' => 'À réaliser', 'value' => '38', 'detail' => '55%', 'dash' => 55, 'offset' => 0, 'class' => 'text-success'],
                        ['label' => 'En cours', 'value' => '12', 'detail' => '17%', 'dash' => 17, 'offset' => 55, 'class' => 'text-primary'],
                        ['label' => 'À corriger', 'value' => '7', 'detail' => '10%', 'dash' => 10, 'offset' => 72, 'class' => 'text-warning'],
                        ['label' => 'Non conforme', 'value' => '5', 'detail' => '7%', 'dash' => 7, 'offset' => 82, 'class' => 'text-error'],
                        ['label' => 'Terminée', 'value' => '7', 'detail' => '11%', 'dash' => 11, 'offset' => 89, 'class' => 'text-base-content/30'],
                    ],
                ],
                [
                    'type' => 'progress',
                    'title' => 'Interventions à réaliser par commune',
                    'tone' => 'success',
                    'items' => [
                        ['label' => 'Rennes', 'value' => '12', 'width' => 'w-11/12'],
                        ['label' => 'Vannes', 'value' => '8', 'width' => 'w-8/12'],
                        ['label' => 'Auray', 'value' => '6', 'width' => 'w-6/12'],
                        ['label' => 'Clessé', 'value' => '5', 'width' => 'w-5/12'],
                        ['label' => 'Questembert', 'value' => '4', 'width' => 'w-4/12'],
                        ['label' => 'Baden', 'value' => '3', 'width' => 'w-3/12'],
                    ],
                ],
                [
                    'type' => 'progress',
                    'title' => 'Charge par agent',
                    'tone' => 'success',
                    'items' => [
                        ['label' => 'Thomas Bernard', 'value' => '15', 'width' => 'w-11/12'],
                        ['label' => 'Julie Dupont', 'value' => '11', 'width' => 'w-8/12'],
                        ['label' => 'Antoine Le Goff', 'value' => '8', 'width' => 'w-6/12'],
                        ['label' => 'Sophie Martin', 'value' => '4', 'width' => 'w-3/12'],
                        ['label' => 'Marc Le Roux', 'value' => '2', 'width' => 'w-2/12'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'bureau',
            'marker' => 'B',
            'title' => 'Bureau',
            'subtitle' => 'Transformer les enquêtes terrain en livrables validés',
            'tone' => 'primary',
            'url' => $officeUrl,
            'link' => 'Interventions bureau',
            'kpis' => [
                ['label' => 'À valider', 'value' => '24', 'unit' => 'Enquêtes', 'trend' => '+20% vs période précédente', 'tone' => 'primary', 'icon' => 'bi-person-check'],
                ['label' => 'Rapport à produire', 'value' => '16', 'unit' => 'Enquêtes', 'trend' => 'Stable', 'tone' => 'neutral', 'icon' => 'bi-file-text'],
                ['label' => 'Rapport à envoyer', 'value' => '9', 'unit' => 'Enquêtes', 'trend' => '-2 vs période précédente', 'tone' => 'success', 'icon' => 'bi-send'],
                ['label' => 'À facturer', 'value' => '11', 'unit' => 'Enquêtes', 'trend' => '+1 vs période précédente', 'tone' => 'warning', 'icon' => 'bi-currency-euro'],
            ],
            'panels' => [
                [
                    'type' => 'bars',
                    'title' => 'File d’attente de validation',
                    'caption' => 'Nombre d’enquêtes à valider',
                    'tone' => 'primary',
                    'items' => [
                        ['label' => 'J-7', 'value' => '18', 'height' => 'h-16'],
                        ['label' => 'J-6', 'value' => '21', 'height' => 'h-20'],
                        ['label' => 'J-5', 'value' => '22', 'height' => 'h-20'],
                        ['label' => 'J-4', 'value' => '20', 'height' => 'h-16'],
                        ['label' => 'J-3', 'value' => '28', 'height' => 'h-28'],
                        ['label' => 'J-2', 'value' => '26', 'height' => 'h-24'],
                        ['label' => 'J-1', 'value' => '24', 'height' => 'h-24'],
                        ['label' => 'Aujourd’hui', 'value' => '24', 'height' => 'h-24'],
                    ],
                ],
                [
                    'type' => 'donut',
                    'title' => 'Répartition par type d’enquête',
                    'center' => '91',
                    'centerLabel' => 'enquêtes',
                    'segments' => [
                        ['label' => 'ANC', 'value' => '62', 'detail' => '68%', 'dash' => 68, 'offset' => 0, 'class' => 'text-primary'],
                        ['label' => 'RAC', 'value' => '29', 'detail' => '32%', 'dash' => 32, 'offset' => 68, 'class' => 'text-info'],
                    ],
                ],
                [
                    'type' => 'progress',
                    'title' => 'Répartition par contrat à valider',
                    'tone' => 'primary',
                    'items' => [
                        ['label' => 'Auray - Pluvigner', 'value' => '10', 'width' => 'w-10/12'],
                        ['label' => 'Vannes Agglo', 'value' => '8', 'width' => 'w-8/12'],
                        ['label' => 'Pays de Redon', 'value' => '4', 'width' => 'w-4/12'],
                        ['label' => 'Questembert Com.', 'value' => '2', 'width' => 'w-2/12'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'gestion',
            'marker' => 'C',
            'title' => 'Gestion',
            'subtitle' => 'Suivre la qualité, les délais et les volumes globaux',
            'tone' => 'secondary',
            'url' => $managementUrl,
            'link' => 'Indicateurs de gestion',
            'kpis' => [
                ['label' => 'Total interventions', 'value' => '128', 'unit' => 'Sur la période', 'trend' => '+8% vs période précédente', 'tone' => 'secondary', 'icon' => 'bi-folder2-open'],
                ['label' => 'Taux de conformité', 'value' => '87%', 'unit' => 'Conformes', 'trend' => '+5 pts vs période précédente', 'tone' => 'success', 'icon' => 'bi-shield-check'],
                ['label' => 'Délai moyen', 'value' => '9,6 j', 'unit' => 'Terrain à envoi', 'trend' => '-1,2 j vs période précédente', 'tone' => 'success', 'icon' => 'bi-clock'],
                ['label' => 'En retard', 'value' => '14', 'unit' => 'Enquêtes', 'trend' => '-3 vs période précédente', 'tone' => 'warning', 'icon' => 'bi-hourglass-split'],
            ],
            'panels' => [
                [
                    'type' => 'progress',
                    'title' => 'Enquêtes par étape du cycle',
                    'tone' => 'secondary',
                    'items' => [
                        ['label' => 'À réaliser', 'value' => '38 (30%)', 'width' => 'w-11/12'],
                        ['label' => 'Réalisation terrain', 'value' => '24 (19%)', 'width' => 'w-8/12'],
                        ['label' => 'À valider', 'value' => '24 (19%)', 'width' => 'w-8/12'],
                        ['label' => 'Rapport à envoyer', 'value' => '16 (12%)', 'width' => 'w-5/12'],
                        ['label' => 'À facturer', 'value' => '11 (9%)', 'width' => 'w-4/12'],
                        ['label' => 'Terminée', 'value' => '15 (11%)', 'width' => 'w-5/12'],
                    ],
                ],
                [
                    'type' => 'line',
                    'title' => 'Évolution du volume d’interventions',
                    'caption' => 'Total interventions',
                    'points' => '8,86 68,78 128,72 188,62 248,36',
                    'labels' => ['19/05', '26/05', '02/06', '09/06', '16/06'],
                    'values' => ['96', '102', '106', '110', '128'],
                ],
                [
                    'type' => 'donut',
                    'title' => 'Conformité par contrat',
                    'center' => '87%',
                    'centerLabel' => 'taux global',
                    'segments' => [
                        ['label' => 'Auray - Pluvigner', 'value' => '92%', 'detail' => '92%', 'dash' => 30, 'offset' => 0, 'class' => 'text-success'],
                        ['label' => 'Vannes Agglo', 'value' => '88%', 'detail' => '88%', 'dash' => 28, 'offset' => 30, 'class' => 'text-lime-500'],
                        ['label' => 'Pays de Redon', 'value' => '84%', 'detail' => '84%', 'dash' => 24, 'offset' => 58, 'class' => 'text-warning'],
                        ['label' => 'Questembert Com.', 'value' => '70%', 'detail' => '70%', 'dash' => 18, 'offset' => 82, 'class' => 'text-error'],
                    ],
                ],
            ],
        ],
    ];

    $toneClasses = [
        'primary' => ['text' => 'text-primary', 'bg' => 'bg-primary', 'soft' => 'bg-primary/10', 'border' => 'border-primary/20'],
        'secondary' => ['text' => 'text-secondary', 'bg' => 'bg-secondary', 'soft' => 'bg-secondary/10', 'border' => 'border-secondary/20'],
        'success' => ['text' => 'text-success', 'bg' => 'bg-success', 'soft' => 'bg-success/10', 'border' => 'border-success/20'],
        'warning' => ['text' => 'text-warning', 'bg' => 'bg-warning', 'soft' => 'bg-warning/10', 'border' => 'border-warning/20'],
        'error' => ['text' => 'text-error', 'bg' => 'bg-error', 'soft' => 'bg-error/10', 'border' => 'border-error/20'],
        'neutral' => ['text' => 'text-base-content/70', 'bg' => 'bg-base-content/40', 'soft' => 'bg-base-200', 'border' => 'border-base-300'],
    ];

    $quickActions = [
        ['label' => 'Rechercher une intervention', 'description' => 'Référence, adresse, PDC', 'icon' => 'bi-search', 'tone' => 'primary'],
        ['label' => 'Mes tâches ouvertes', 'description' => 'Interventions à traiter', 'icon' => 'bi-clipboard-check', 'tone' => 'neutral'],
        ['label' => 'Anomalies à corriger', 'description' => 'Non conformités et reprises', 'icon' => 'bi-exclamation-triangle', 'tone' => 'error'],
        ['label' => 'Documents récents', 'description' => 'Rapports et courriers', 'icon' => 'bi-file-earmark-text', 'tone' => 'neutral'],
        ['label' => 'Exports de données', 'description' => 'Télécharger un export', 'icon' => 'bi-download', 'tone' => 'success'],
    ];
@endphp

<x-daisy::layout.app :title="$title" :theme="$theme" :container="false" body-class="bg-base-200 text-base-content">
    <div class="min-h-screen bg-base-200">
        <div>
            <header class="sticky top-0 z-20 border-b border-base-300 bg-base-100">
                <div class="mx-auto max-w-screen-2xl px-4 py-3 lg:px-6">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex min-w-0 items-center gap-4">
                            <div class="flex items-center gap-2">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-success/10 text-success">
                                    <x-daisy::ui.advanced.icon name="bi-droplet" class="h-4 w-4" />
                                </span>
                                <div class="hidden leading-tight sm:block">
                                    <p class="text-xs font-bold">{{ $brand }}</p>
                                    <p class="text-sm font-bold text-primary">{{ $product }}</p>
                                </div>
                            </div>
                            <div class="min-w-0 border-l border-base-300 pl-4">
                                <h1 class="truncate text-xl font-bold leading-tight">{{ $title }}</h1>
                                <p class="truncate text-sm text-base-content/60">{{ $subtitle }}</p>
                            </div>
                        </div>
                        <div class="flex shrink-0 items-center gap-3">
                            <span class="hidden rounded-full border border-base-300 bg-base-200 px-3 py-1 text-xs font-medium text-base-content/70 md:inline-flex">
                                Mis à jour {{ $updatedAt }}
                            </span>
                            <button type="button" class="btn btn-circle btn-ghost btn-sm" aria-label="Aide">
                                <x-daisy::ui.advanced.icon name="bi-question-circle" class="h-5 w-5" />
                            </button>
                            <div class="flex items-center gap-2">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full border border-base-300 bg-base-100">
                                    <x-daisy::ui.advanced.icon name="bi-person" class="h-5 w-5" />
                                </span>
                                <div class="hidden text-right sm:block">
                                    <p class="text-sm font-semibold leading-tight">{{ $userName }}</p>
                                    <p class="text-xs text-base-content/60">{{ $userRole }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <nav aria-label="Navigation principale" class="mt-3 grid grid-cols-2 gap-2 sm:flex sm:flex-wrap">
                        @foreach($navItems as $item)
                            <a href="#" @class([
                                'btn btn-sm min-w-0 w-full justify-start sm:w-auto',
                                'btn-primary' => $item['active'] ?? false,
                                'btn-ghost' => ! ($item['active'] ?? false),
                            ])>
                                <x-daisy::ui.advanced.icon :name="$item['icon']" class="h-4 w-4 shrink-0" />
                                <span class="truncate">{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </nav>
                </div>
            </header>

            <main class="mx-auto max-w-screen-2xl space-y-5 px-4 py-5 lg:px-6">
                <section aria-label="Filtres du tableau de bord" class="rounded-box border border-base-300 bg-base-100 p-4">
                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                        <x-daisy::ui.partials.form-field label="Périmètre" gap="gap-1">
                            <x-daisy::ui.inputs.select size="sm" :options="[$perimeter]" :value="$perimeter" aria-label="Périmètre" />
                        </x-daisy::ui.partials.form-field>
                        <x-daisy::ui.partials.form-field label="Période" gap="gap-1">
                            <label class="input input-sm flex w-full items-center gap-2">
                                <span class="min-w-0 flex-1 truncate font-semibold">{{ $period }}</span>
                                <x-daisy::ui.advanced.icon name="bi-calendar3" class="h-4 w-4 text-base-content/60" />
                            </label>
                        </x-daisy::ui.partials.form-field>
                        <x-daisy::ui.partials.form-field label="Contrat" gap="gap-1">
                            <x-daisy::ui.inputs.select size="sm" :options="[$contract]" :value="$contract" aria-label="Contrat" />
                        </x-daisy::ui.partials.form-field>
                        <x-daisy::ui.partials.form-field label="Type d’enquête" gap="gap-1">
                            <x-daisy::ui.inputs.select size="sm" :options="[$surveyType]" :value="$surveyType" aria-label="Type d’enquête" />
                        </x-daisy::ui.partials.form-field>
                        <x-daisy::ui.partials.form-field label="Rechercher une enquête" gap="gap-1">
                            <label class="input input-sm flex w-full items-center gap-2">
                                <input type="search" class="min-w-0 flex-1" placeholder="N° intervention, adresse, PDC..." aria-label="Rechercher une enquête">
                                <x-daisy::ui.advanced.icon name="bi-search" class="h-4 w-4 text-base-content/60" />
                            </label>
                        </x-daisy::ui.partials.form-field>
                    </div>
                </section>

                <section aria-label="Synthèse opérationnelle" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach($summary as $item)
                        <article class="rounded-box border {{ $toneClasses[$item['tone']]['border'] }} bg-base-100 p-4">
                            <p class="text-sm font-semibold text-base-content/70">{{ $item['label'] }}</p>
                            <div class="mt-2 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                                <p class="text-3xl font-bold tracking-tight">{{ $item['value'] }}</p>
                                <span class="w-fit rounded-full {{ $toneClasses[$item['tone']]['soft'] }} px-2 py-1 text-xs font-semibold {{ $toneClasses[$item['tone']]['text'] }}">
                                    {{ $item['detail'] }}
                                </span>
                            </div>
                        </article>
                    @endforeach
                </section>

                <div class="flex flex-wrap items-center justify-between gap-3 text-sm text-base-content/65">
                    <p>Les indicateurs sont calculés sur votre périmètre : <span class="font-semibold text-base-content">{{ $perimeter }}</span></p>
                    <a href="{{ $detailedUrl }}" class="btn btn-primary btn-sm">
                        Accéder à la liste détaillée
                        <x-daisy::ui.advanced.icon name="bi-arrow-right" class="h-4 w-4" />
                    </a>
                </div>

                @foreach($sections as $section)
                    <section class="rounded-box border border-base-300 bg-base-100 p-4 shadow-sm">
                        @include('daisy::partials.reporting.section-heading', ['section' => $section, 'toneClasses' => $toneClasses])

                        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            @foreach($section['kpis'] as $kpi)
                                @include('daisy::partials.reporting.metric-card', ['kpi' => $kpi, 'toneClasses' => $toneClasses])
                            @endforeach
                        </div>

                        <div class="mt-4 grid gap-4 xl:grid-cols-3">
                            @foreach($section['panels'] as $panel)
                                @include('daisy::partials.reporting.panel', ['panel' => $panel, 'toneClasses' => $toneClasses])
                            @endforeach
                        </div>
                    </section>
                @endforeach

                <section class="rounded-box border border-base-300 bg-base-100 p-4">
                    <h2 class="text-base font-bold">Accès rapides</h2>
                    <div class="mt-3 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                        @foreach($quickActions as $action)
                            <a href="#" class="group flex min-h-20 items-center gap-3 rounded-box border border-base-300 bg-base-100 p-3 transition hover:border-primary hover:bg-base-200">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $toneClasses[$action['tone']]['soft'] }} {{ $toneClasses[$action['tone']]['text'] }}">
                                    <x-daisy::ui.advanced.icon :name="$action['icon']" class="h-5 w-5" />
                                </span>
                                <span class="min-w-0">
                                    <span class="block text-sm font-bold leading-snug">{{ $action['label'] }}</span>
                                    <span class="block text-xs text-base-content/60">{{ $action['description'] }}</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>
            </main>
        </div>
    </div>
</x-daisy::layout.app>
