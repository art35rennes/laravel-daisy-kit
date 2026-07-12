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
        ->toContain('<main id="dashboard" class="mx-auto w-full min-w-0 max-w-screen-2xl')
        ->toContain('<section id="terrain" class="min-w-0 scroll-mt-4 rounded-box border border-base-300')
        ->toContain('<article class="min-w-0 rounded-box border border-base-300')
        ->toContain('xl:grid-cols-3')
        ->toContain('id="operations-chart-detail"')
        ->toContain('data-chart-detail-name')
        ->toContain('data-chart-legend-index="0"')
        ->toContain('class="daisy-chart-legend-item')
        ->toContain('"target":"#operations-chart-detail"')
        ->toContain('href="#filters"')
        ->toContain('href="#terrain"')
        ->toContain('aria-label="Light"')
        ->toContain('aria-label="Night"')
        ->toContain('Accès rapides')
        ->toContain('data-daisy-chart="1"')
        ->toContain('"preset":"sparkline"')
        ->toContain('"preset":"donut"')
        ->toContain('"preset":"bar"')
        ->toContain('"preset":"line"')
        ->toContain('"orientation":"horizontal"')
        ->toContain('"renderer":"svg"')
        ->toContain('"centerValue":"87%"')
        ->toContain('"showValues":true')
        ->toContain('"action":{"type":"event","intent":"detail"')
        ->toContain('"drilldown":{"url":"\/interventions"')
        ->toContain('"markers"')
        ->toContain('Rechercher une intervention')
        ->not->toContain('No data available')
        ->not->toContain('aria-label="Synthèse opérationnelle"')
        ->not->toContain('data-reporting-chart')
        ->not->toContain('style=');
});

it('can opt into a summary and disable the demonstration detail modal', function () {
    config([
        'daisy-kit.auto_assets' => false,
        'daisy-kit.themes.custom' => [],
    ]);

    $html = Blade::render('<x-daisy::templates.reporting.operations-dashboard :show-summary="true" :detail-modal="false" />');

    expect($html)
        ->toContain('aria-label="Synthèse opérationnelle"')
        ->not->toContain('data-chart-detail-modal');
});
