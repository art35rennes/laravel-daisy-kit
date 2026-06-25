<?php

use Illuminate\Support\Facades\Blade;

it('renders the operations dashboard reporting template', function () {
    config([
        'daisy-kit.auto_assets' => false,
        'daisy-kit.themes.custom' => [],
    ]);

    $html = Blade::render('<x-daisy::templates.reporting.operations-dashboard detailed-url="/interventions" />');

    expect($html)
        ->toContain('Accueil')
        ->toContain('Vue d’ensemble de vos enquêtes')
        ->toContain('Terrain')
        ->toContain('Bureau')
        ->toContain('Gestion')
        ->toContain('À réaliser')
        ->toContain('Taux de conformité')
        ->toContain('Accéder à la liste détaillée')
        ->toContain('href="/interventions"')
        ->toContain('Accès rapides')
        ->toContain('data-reporting-chart="sparkline"')
        ->toContain('data-reporting-chart="donut"')
        ->toContain('data-reporting-chart="bars"')
        ->toContain('data-reporting-chart="line"')
        ->toContain('Rechercher une intervention')
        ->not->toContain('No data available')
        ->not->toContain('data-daisy-chart')
        ->not->toContain('style=');
});
