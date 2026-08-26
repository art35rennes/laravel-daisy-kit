





<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
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
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
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
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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
?>

<?php if (isset($component)) { $__componentOriginalbd2165b0cec1ddd91acdd4cdee286435 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbd2165b0cec1ddd91acdd4cdee286435 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.layout.sidebar-layout','data' => ['title' => $title,'theme' => $theme,'variant' => 'slim','forceCollapsed' => true,'collapsible' => false,'brand' => $brand.' '.$product,'brandCollapsed' => mb_substr($product, 0, 1),'sections' => $sidebarSections,'container' => 'bg-base-200 p-0','themes' => $themes,'themeLabel' => $themeLabel]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::layout.sidebar-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title),'theme' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($theme),'variant' => 'slim','force-collapsed' => true,'collapsible' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'brand' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($brand.' '.$product),'brand-collapsed' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(mb_substr($product, 0, 1)),'sections' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarSections),'container' => 'bg-base-200 p-0','themes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($themes),'theme-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($themeLabel)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

     <?php $__env->slot('navbarHeading', null, []); ?> 
        <h1><?php echo e($title); ?></h1>
        <p><?php echo e($subtitle); ?></p>
     <?php $__env->endSlot(); ?>

     <?php $__env->slot('topbarRight', null, []); ?> 
        <span class="hidden rounded-full border border-base-300 bg-base-200 px-3 py-1 text-xs font-medium text-base-content/70 md:inline-flex">
            Mis à jour <?php echo e($updatedAt); ?>

        </span>
        <button type="button" class="btn btn-circle btn-ghost btn-sm" aria-label="Aide">
            <?php if (isset($component)) { $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.icon','data' => ['name' => 'bi-question-circle','class' => 'h-5 w-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'bi-question-circle','class' => 'h-5 w-5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $attributes = $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $component = $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
        </button>
        <div class="flex items-center gap-2">
            <span class="flex h-9 w-9 items-center justify-center rounded-full border border-base-300 bg-base-100">
                <?php if (isset($component)) { $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.icon','data' => ['name' => 'bi-person','class' => 'h-5 w-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'bi-person','class' => 'h-5 w-5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $attributes = $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $component = $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
            </span>
            <div class="hidden text-right sm:block">
                <p class="text-sm font-semibold leading-tight"><?php echo e($userName); ?></p>
                <p class="text-xs text-base-content/60"><?php echo e($userRole); ?></p>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <main id="dashboard" class="mx-auto w-full min-w-0 max-w-screen-2xl space-y-4 px-3 py-4 sm:px-4 lg:px-6">
        <section id="filters" aria-label="Filtres du tableau de bord" class="rounded-box border border-base-300 bg-base-100 p-4">
            <form method="GET" action="<?php echo e($filterAction ?: request()->url()); ?>" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                        <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['label' => 'Périmètre','gap' => 'gap-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Périmètre','gap' => 'gap-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php if (isset($component)) { $__componentOriginale3f19de9d041234399138af8d6d623fc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3f19de9d041234399138af8d6d623fc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.select','data' => ['name' => 'perimeter','size' => 'sm','options' => [$perimeter],'value' => $perimeter,'ariaLabel' => 'Périmètre']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'perimeter','size' => 'sm','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([$perimeter]),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($perimeter),'aria-label' => 'Périmètre']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3f19de9d041234399138af8d6d623fc)): ?>
<?php $attributes = $__attributesOriginale3f19de9d041234399138af8d6d623fc; ?>
<?php unset($__attributesOriginale3f19de9d041234399138af8d6d623fc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3f19de9d041234399138af8d6d623fc)): ?>
<?php $component = $__componentOriginale3f19de9d041234399138af8d6d623fc; ?>
<?php unset($__componentOriginale3f19de9d041234399138af8d6d623fc); ?>
<?php endif; ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $attributes = $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $component = $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['label' => 'Période','gap' => 'gap-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Période','gap' => 'gap-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <label class="input input-sm flex w-full items-center gap-2">
                                <input name="period" value="<?php echo e($period); ?>" class="min-w-0 flex-1 truncate font-semibold" aria-label="Période" />
                                <?php if (isset($component)) { $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.icon','data' => ['name' => 'bi-calendar3','class' => 'h-4 w-4 text-base-content/60']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'bi-calendar3','class' => 'h-4 w-4 text-base-content/60']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $attributes = $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $component = $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
                            </label>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $attributes = $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $component = $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['label' => 'Contrat','gap' => 'gap-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Contrat','gap' => 'gap-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php if (isset($component)) { $__componentOriginale3f19de9d041234399138af8d6d623fc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3f19de9d041234399138af8d6d623fc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.select','data' => ['name' => 'contract','size' => 'sm','options' => [$contract],'value' => $contract,'ariaLabel' => 'Contrat']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'contract','size' => 'sm','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([$contract]),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($contract),'aria-label' => 'Contrat']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3f19de9d041234399138af8d6d623fc)): ?>
<?php $attributes = $__attributesOriginale3f19de9d041234399138af8d6d623fc; ?>
<?php unset($__attributesOriginale3f19de9d041234399138af8d6d623fc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3f19de9d041234399138af8d6d623fc)): ?>
<?php $component = $__componentOriginale3f19de9d041234399138af8d6d623fc; ?>
<?php unset($__componentOriginale3f19de9d041234399138af8d6d623fc); ?>
<?php endif; ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $attributes = $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $component = $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['label' => 'Type d’enquête','gap' => 'gap-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Type d’enquête','gap' => 'gap-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php if (isset($component)) { $__componentOriginale3f19de9d041234399138af8d6d623fc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3f19de9d041234399138af8d6d623fc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.select','data' => ['name' => 'survey_type','size' => 'sm','options' => [$surveyType],'value' => $surveyType,'ariaLabel' => 'Type d’enquête']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'survey_type','size' => 'sm','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([$surveyType]),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($surveyType),'aria-label' => 'Type d’enquête']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3f19de9d041234399138af8d6d623fc)): ?>
<?php $attributes = $__attributesOriginale3f19de9d041234399138af8d6d623fc; ?>
<?php unset($__attributesOriginale3f19de9d041234399138af8d6d623fc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3f19de9d041234399138af8d6d623fc)): ?>
<?php $component = $__componentOriginale3f19de9d041234399138af8d6d623fc; ?>
<?php unset($__componentOriginale3f19de9d041234399138af8d6d623fc); ?>
<?php endif; ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $attributes = $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $component = $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['label' => 'Rechercher une enquête','gap' => 'gap-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Rechercher une enquête','gap' => 'gap-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <label class="input input-sm flex w-full items-center gap-2">
                                <input name="q" type="search" class="min-w-0 flex-1" placeholder="N° intervention, adresse, PDC..." aria-label="Rechercher une enquête">
                                <?php if (isset($component)) { $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.icon','data' => ['name' => 'bi-search','class' => 'h-4 w-4 text-base-content/60']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'bi-search','class' => 'h-4 w-4 text-base-content/60']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $attributes = $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $component = $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
                            </label>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $attributes = $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $component = $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
                        <button type="submit" class="sr-only">Appliquer les filtres</button>
            </form>
        </section>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showSummary): ?>
                <section aria-label="Synthèse opérationnelle" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <article class="rounded-box border <?php echo e($toneClasses[$item['tone']]['border']); ?> bg-base-100 p-4">
                            <p class="text-sm font-semibold text-base-content/70"><?php echo e($item['label']); ?></p>
                            <div class="mt-2 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                                <p class="text-3xl font-bold tracking-tight"><?php echo e($item['value']); ?></p>
                                <span class="w-fit rounded-full <?php echo e($toneClasses[$item['tone']]['soft']); ?> px-2 py-1 text-xs font-semibold <?php echo e($toneClasses[$item['tone']]['text']); ?>">
                                    <?php echo e($item['detail']); ?>

                                </span>
                            </div>
                        </article>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </section>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="flex flex-wrap items-center justify-between gap-3 text-sm text-base-content/65">
                    <p>Les indicateurs sont calculés sur votre périmètre : <span class="font-semibold text-base-content"><?php echo e($perimeter); ?></span></p>
                    <a href="<?php echo e($effectiveDetailedUrl); ?>" class="btn btn-primary btn-sm">
                        Accéder à la liste détaillée
                        <?php if (isset($component)) { $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.icon','data' => ['name' => 'bi-arrow-right','class' => 'h-4 w-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'bi-arrow-right','class' => 'h-4 w-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $attributes = $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $component = $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
                    </a>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <section id="<?php echo e($section['id']); ?>" class="min-w-0 scroll-mt-4 rounded-box border border-base-300 bg-base-100 p-4 shadow-sm">
                        <?php echo $__env->make('daisy::partials.reporting.section-heading', ['section' => $section, 'toneClasses' => $toneClasses], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                        <div class="mt-4 grid min-w-0 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $section['kpis']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php echo $__env->make('daisy::partials.reporting.metric-card', ['kpi' => $kpi, 'toneClasses' => $toneClasses], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>

                        <div class="mt-4 grid min-w-0 gap-3 lg:grid-cols-2 xl:grid-cols-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $section['panels']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $panel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php echo $__env->make('daisy::partials.reporting.panel', ['panel' => $panel, 'section' => $section, 'toneClasses' => $toneClasses, 'detailedUrl' => $effectiveDetailedUrl, 'detailModalId' => $detailModal ? $detailModalId : null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </section>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                <section id="quick-actions" class="scroll-mt-4 rounded-box border border-base-300 bg-base-100 p-4">
                    <h2 class="text-base font-bold">Accès rapides</h2>
                    <div class="mt-3 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $quickActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <a href="<?php echo e($action['url'] ?? '#'); ?>" class="group flex min-h-16 items-center gap-3 rounded-box border border-base-300 bg-base-100 p-3 transition hover:border-primary hover:bg-base-200">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full <?php echo e($toneClasses[$action['tone']]['soft']); ?> <?php echo e($toneClasses[$action['tone']]['text']); ?>">
                                    <?php if (isset($component)) { $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.icon','data' => ['name' => $action['icon'],'class' => 'h-5 w-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($action['icon']),'class' => 'h-5 w-5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $attributes = $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $component = $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
                                </span>
                                <span class="min-w-0">
                                    <span class="block text-sm font-bold leading-snug"><?php echo e($action['label']); ?></span>
                                    <span class="block text-xs text-base-content/60"><?php echo e($action['description']); ?></span>
                                </span>
                            </a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </section>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($detailModal): ?>
                    <?php if (isset($component)) { $__componentOriginal8f2cb44993712bdbd6d9bd2b9a505962 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f2cb44993712bdbd6d9bd2b9a505962 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.overlay.modal','data' => ['id' => $detailModalId,'title' => 'Détail de l’indicateur','size' => 'md','dataChartDetailModal' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.overlay.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($detailModalId),'title' => 'Détail de l’indicateur','size' => 'md','data-chart-detail-modal' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

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
                         <?php $__env->slot('actions', null, []); ?> 
                            <a href="<?php echo e($effectiveDetailedUrl); ?>" class="btn btn-primary" data-chart-detail-link>Ouvrir la liste filtrée</a>
                         <?php $__env->endSlot(); ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8f2cb44993712bdbd6d9bd2b9a505962)): ?>
<?php $attributes = $__attributesOriginal8f2cb44993712bdbd6d9bd2b9a505962; ?>
<?php unset($__attributesOriginal8f2cb44993712bdbd6d9bd2b9a505962); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8f2cb44993712bdbd6d9bd2b9a505962)): ?>
<?php $component = $__componentOriginal8f2cb44993712bdbd6d9bd2b9a505962; ?>
<?php unset($__componentOriginal8f2cb44993712bdbd6d9bd2b9a505962); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </main>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbd2165b0cec1ddd91acdd4cdee286435)): ?>
<?php $attributes = $__attributesOriginalbd2165b0cec1ddd91acdd4cdee286435; ?>
<?php unset($__attributesOriginalbd2165b0cec1ddd91acdd4cdee286435); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbd2165b0cec1ddd91acdd4cdee286435)): ?>
<?php $component = $__componentOriginalbd2165b0cec1ddd91acdd4cdee286435; ?>
<?php unset($__componentOriginalbd2165b0cec1ddd91acdd4cdee286435); ?>
<?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/templates/reporting/operations-dashboard.blade.php ENDPATH**/ ?>