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
    'themes' => ['light', 'dark', 'corporate', 'business', 'winter', 'night'],
    'themeLabel' => 'Thème',
    'navigation' => null,
    'summary' => null,
    'sections' => null,
    'quickActions' => null,
    'filterAction' => null,
    'showSummary' => false,
    'detailModal' => true,
    'detailModalId' => 'operations-chart-detail',
])

@php
    $effectiveDetailedUrl = $detailedUrl === '#' ? '#terrain' : $detailedUrl;
    $effectiveTerrainUrl = $terrainUrl === '#' ? '#terrain' : $terrainUrl;
    $effectiveOfficeUrl = $officeUrl === '#' ? '#bureau' : $officeUrl;
    $effectiveManagementUrl = $managementUrl === '#' ? '#gestion' : $managementUrl;
    $navItems = $navigation ?? [
        ['label' => 'Accueil', 'icon' => 'bi-house', 'href' => '#dashboard', 'active' => true],
        ['label' => 'Interventions', 'icon' => 'bi-clipboard-check', 'href' => '#terrain'],
        ['label' => 'Documents', 'icon' => 'bi-file-earmark-text', 'href' => '#quick-actions'],
        ['label' => 'Exports', 'icon' => 'bi-download', 'href' => '#quick-actions'],
        ['label' => 'Paramètres', 'icon' => 'bi-gear', 'href' => '#filters'],
    ];

    $summary = $summary ?? [
        ['label' => 'Interventions', 'value' => '128', 'detail' => '+8% sur la période', 'tone' => 'success'],
        ['label' => 'Conformité', 'value' => '87%', 'detail' => '+5 pts vs période précédente', 'tone' => 'success'],
        ['label' => 'À traiter', 'value' => '40', 'detail' => '24 à valider, 16 rapports', 'tone' => 'primary'],
        ['label' => 'Points à risque', 'value' => '19', 'detail' => '14 retards, 5 non conformes', 'tone' => 'error'],
    ];

    $sections = $sections ?? [
        [
            'id' => 'terrain',
            'marker' => 'A',
            'title' => 'Terrain',
            'subtitle' => 'Prioriser les interventions et anomalies visibles aujourd’hui',
            'tone' => 'success',
            'url' => $effectiveTerrainUrl,
            'link' => 'Interventions terrain',
            'kpis' => [
                ['label' => 'À réaliser', 'value' => '38', 'unit' => 'Interventions', 'trend' => '+12% vs période précédente', 'tone' => 'success', 'icon' => 'bi-calendar-check', 'sparklineLabels' => ['J-6', 'J-5', 'J-4', 'J-3', 'J-2', 'J-1', 'Aujourd’hui'], 'sparklineData' => [
                    ['value' => 24, 'drilldown' => ['status' => 'a-realiser', 'day' => 'j-6']],
                    ['value' => 24, 'drilldown' => ['status' => 'a-realiser', 'day' => 'j-5']],
                    ['value' => 29, 'drilldown' => ['status' => 'a-realiser', 'day' => 'j-4']],
                    ['value' => 25, 'drilldown' => ['status' => 'a-realiser', 'day' => 'j-3']],
                    ['value' => 34, 'drilldown' => ['status' => 'a-realiser', 'day' => 'j-2']],
                    ['value' => 28, 'drilldown' => ['status' => 'a-realiser', 'day' => 'j-1']],
                    ['value' => 38, 'drilldown' => ['status' => 'a-realiser', 'day' => 'today']],
                ], 'drilldownUrl' => $detailedUrl, 'drilldownParams' => ['section' => 'terrain', 'chart' => 'kpi']],
                ['label' => 'En cours', 'value' => '12', 'unit' => 'Interventions', 'trend' => 'Stable', 'tone' => 'neutral', 'icon' => 'bi-activity'],
                ['label' => 'À corriger / refaire', 'value' => '7', 'unit' => 'Interventions', 'trend' => '+2 vs période précédente', 'tone' => 'error', 'icon' => 'bi-exclamation-triangle'],
                ['label' => 'Non conformes', 'value' => '5', 'unit' => 'Interventions', 'trend' => '14% du total terrain', 'tone' => 'error', 'icon' => 'bi-shield-exclamation'],
            ],
            'panels' => [
                [
                    'type' => 'donut',
                    'chart' => 'status',
                    'filter' => 'status',
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
                    'chart' => 'commune',
                    'filter' => 'commune',
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
                    'chart' => 'agent-load',
                    'filter' => 'agent',
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
            'url' => $effectiveOfficeUrl,
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
                    'chart' => 'validation-queue',
                    'filter' => 'day',
                    'title' => 'File d’attente de validation',
                    'caption' => 'Nombre d’enquêtes à valider',
                    'tone' => 'primary',
                    'markers' => [
                        ['type' => 'line', 'value' => 24, 'name' => 'Capacité cible', 'label' => 'Cible 24'],
                        ['type' => 'point', 'coord' => ['J-3', 28], 'name' => 'Pic'],
                    ],
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
                    'chart' => 'survey-type',
                    'filter' => 'survey_type',
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
                    'chart' => 'contract-validation',
                    'filter' => 'contract',
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
            'url' => $effectiveManagementUrl,
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
                    'chart' => 'cycle-step',
                    'filter' => 'step',
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
                    'chart' => 'volume',
                    'filter' => 'date',
                    'title' => 'Évolution du volume d’interventions',
                    'caption' => 'Total interventions',
                    'labels' => ['19/05', '26/05', '02/06', '09/06', '16/06'],
                    'values' => ['96', '102', '106', '110', '128'],
                    'markers' => [
                        ['type' => 'point', 'coord' => ['16/06', 128], 'name' => 'Dernier total'],
                    ],
                    'zoom' => true,
                ],
                [
                    'type' => 'donut',
                    'chart' => 'contract-compliance',
                    'filter' => 'contract',
                    'title' => 'Conformité par contrat',
                    'center' => '87%',
                    'centerLabel' => 'taux global',
                    'markers' => [
                        ['type' => 'line', 'value' => 85, 'name' => 'Objectif conformité', 'label' => 'Objectif 85%'],
                    ],
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

    $quickActions = $quickActions ?? [
        ['label' => 'Rechercher une intervention', 'description' => 'Référence, adresse, PDC', 'icon' => 'bi-search', 'tone' => 'primary', 'url' => '#filters'],
        ['label' => 'Mes tâches ouvertes', 'description' => 'Interventions à traiter', 'icon' => 'bi-clipboard-check', 'tone' => 'neutral', 'url' => '#bureau'],
        ['label' => 'Anomalies à corriger', 'description' => 'Non conformités et reprises', 'icon' => 'bi-exclamation-triangle', 'tone' => 'error', 'url' => '#terrain'],
        ['label' => 'Documents récents', 'description' => 'Rapports et courriers', 'icon' => 'bi-file-earmark-text', 'tone' => 'neutral', 'url' => '#quick-actions'],
        ['label' => 'Exports de données', 'description' => 'Télécharger un export', 'icon' => 'bi-download', 'tone' => 'success', 'url' => '#quick-actions'],
    ];

    $sidebarSections = [[
        'items' => array_map(fn ($item) => [
            ...$item,
            'href' => $item['href'] ?? '#',
            'icon' => \Illuminate\Support\Str::after($item['icon'], 'bi-'),
        ], $navItems),
    ]];
@endphp

<x-daisy::layout.sidebar-layout
    :title="$title"
    :theme="$theme"
    variant="slim"
    :force-collapsed="true"
    :collapsible="false"
    :brand="$brand.' '.$product"
    :brand-collapsed="mb_substr($product, 0, 1)"
    :sections="$sidebarSections"
    container="bg-base-200 p-0"
    :themes="$themes"
    :theme-label="$themeLabel"
>
    <x-slot:navbarHeading>
        <h1>{{ $title }}</h1>
        <p>{{ $subtitle }}</p>
    </x-slot:navbarHeading>

    <x-slot:topbarRight>
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
    </x-slot:topbarRight>

    <main id="dashboard" class="mx-auto w-full min-w-0 max-w-screen-2xl space-y-4 px-3 py-4 sm:px-4 lg:px-6">
        <section id="filters" aria-label="Filtres du tableau de bord" class="rounded-box border border-base-300 bg-base-100 p-4">
            <form method="GET" action="{{ $filterAction ?: request()->url() }}" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                        <x-daisy::ui.partials.form-field label="Périmètre" gap="gap-1">
                            <x-daisy::ui.inputs.select name="perimeter" size="sm" :options="[$perimeter]" :value="$perimeter" aria-label="Périmètre" />
                        </x-daisy::ui.partials.form-field>
                        <x-daisy::ui.partials.form-field label="Période" gap="gap-1">
                            <label class="input input-sm flex w-full items-center gap-2">
                                <input name="period" value="{{ $period }}" class="min-w-0 flex-1 truncate font-semibold" aria-label="Période" />
                                <x-daisy::ui.advanced.icon name="bi-calendar3" class="h-4 w-4 text-base-content/60" />
                            </label>
                        </x-daisy::ui.partials.form-field>
                        <x-daisy::ui.partials.form-field label="Contrat" gap="gap-1">
                            <x-daisy::ui.inputs.select name="contract" size="sm" :options="[$contract]" :value="$contract" aria-label="Contrat" />
                        </x-daisy::ui.partials.form-field>
                        <x-daisy::ui.partials.form-field label="Type d’enquête" gap="gap-1">
                            <x-daisy::ui.inputs.select name="survey_type" size="sm" :options="[$surveyType]" :value="$surveyType" aria-label="Type d’enquête" />
                        </x-daisy::ui.partials.form-field>
                        <x-daisy::ui.partials.form-field label="Rechercher une enquête" gap="gap-1">
                            <label class="input input-sm flex w-full items-center gap-2">
                                <input name="q" type="search" class="min-w-0 flex-1" placeholder="N° intervention, adresse, PDC..." aria-label="Rechercher une enquête">
                                <x-daisy::ui.advanced.icon name="bi-search" class="h-4 w-4 text-base-content/60" />
                            </label>
                        </x-daisy::ui.partials.form-field>
                        <button type="submit" class="sr-only">Appliquer les filtres</button>
            </form>
        </section>

                @if($showSummary)
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
                @endif

                <div class="flex flex-wrap items-center justify-between gap-3 text-sm text-base-content/65">
                    <p>Les indicateurs sont calculés sur votre périmètre : <span class="font-semibold text-base-content">{{ $perimeter }}</span></p>
                    <a href="{{ $effectiveDetailedUrl }}" class="btn btn-primary btn-sm">
                        Accéder à la liste détaillée
                        <x-daisy::ui.advanced.icon name="bi-arrow-right" class="h-4 w-4" />
                    </a>
                </div>

                @foreach($sections as $section)
                    <section id="{{ $section['id'] }}" class="min-w-0 scroll-mt-4 rounded-box border border-base-300 bg-base-100 p-4 shadow-sm">
                        @include('daisy::partials.reporting.section-heading', ['section' => $section, 'toneClasses' => $toneClasses])

                        <div class="mt-4 grid min-w-0 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            @foreach($section['kpis'] as $kpi)
                                @include('daisy::partials.reporting.metric-card', ['kpi' => $kpi, 'toneClasses' => $toneClasses])
                            @endforeach
                        </div>

                        <div class="mt-4 grid min-w-0 gap-3 lg:grid-cols-2 xl:grid-cols-3">
                            @foreach($section['panels'] as $panel)
                                @include('daisy::partials.reporting.panel', ['panel' => $panel, 'section' => $section, 'toneClasses' => $toneClasses, 'detailedUrl' => $effectiveDetailedUrl, 'detailModalId' => $detailModal ? $detailModalId : null])
                            @endforeach
                        </div>
                    </section>
                @endforeach

                <section id="quick-actions" class="scroll-mt-4 rounded-box border border-base-300 bg-base-100 p-4">
                    <h2 class="text-base font-bold">Accès rapides</h2>
                    <div class="mt-3 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                        @foreach($quickActions as $action)
                            <a href="{{ $action['url'] ?? '#' }}" class="group flex min-h-16 items-center gap-3 rounded-box border border-base-300 bg-base-100 p-3 transition hover:border-primary hover:bg-base-200">
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

                @if($detailModal)
                    <x-daisy::ui.overlay.modal :id="$detailModalId" title="Détail de l’indicateur" size="md" data-chart-detail-modal>
                        <dl class="grid gap-3 text-sm sm:grid-cols-2">
                            <div class="rounded-box bg-base-200 p-3">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-base-content/60">Donnée</dt>
                                <dd class="mt-1 font-bold" data-chart-detail-name>—</dd>
                            </div>
                            <div class="rounded-box bg-base-200 p-3">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-base-content/60">Valeur</dt>
                                <dd class="mt-1 text-xl font-bold" data-chart-detail-value>—</dd>
                            </div>
                            <div class="rounded-box bg-base-200 p-3 sm:col-span-2">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-base-content/60">Série</dt>
                                <dd class="mt-1 font-medium" data-chart-detail-series>—</dd>
                            </div>
                        </dl>
                        <x-slot:actions>
                            <a href="{{ $effectiveDetailedUrl }}" class="btn btn-primary" data-chart-detail-link>Ouvrir la liste filtrée</a>
                        </x-slot:actions>
                    </x-daisy::ui.overlay.modal>
                @endif
    </main>
</x-daisy::layout.sidebar-layout>
