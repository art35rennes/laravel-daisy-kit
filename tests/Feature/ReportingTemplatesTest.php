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
        ->toContain('data-module="theme-controller"')
        ->toContain('aria-label="Light"')
        ->toContain('aria-label="Night"')
        ->toContain('Accès rapides')
        ->toContain('data-daisy-chart="1"')
        ->toContain('"preset":"sparkline"')
        ->toContain('"preset":"donut"')
        ->toContain('"preset":"bar"')
        ->toContain('"preset":"line"')
        ->toContain('"orientation":"horizontal"')
        ->toContain('"drilldown":{"url":"\/interventions"')
        ->toContain('"markers"')
        ->toContain('Rechercher une intervention')
        ->not->toContain('No data available')
        ->not->toContain('data-reporting-chart')
        ->not->toContain('style=');
});
