<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

it('renders the blueprint examples template through the public view alias', function () {
    $html = View::make('daisy::templates.advanced.blueprint')->render();

    expect($html)
        ->toContain('data-module="blueprint"')
        ->toContain('data-module="theme-controller"')
        ->toContain('data-blueprint-contract')
        ->toContain('name="blueprint-theme-stress"')
        ->toContain('value="business"')
        ->toContain('value="wireframe"')
        ->toContain('data-blueprint-add-node="trigger"')
        ->toContain('data-blueprint-add-node="metric-source"')
        ->toContain('data-blueprint-add-node="source"')
        ->toContain('data-mode="workflow"')
        ->toContain('data-mode="view"')
        ->toContain('name="blueprint_workflow"')
        ->toContain('name="blueprint_typed"')
        ->toContain('name="blueprint_integration"')
        ->toContain('theme-primary')
        ->toContain('theme-secondary')
        ->toContain('theme-accent')
        ->toContain('theme-neutral')
        ->toContain('theme-info')
        ->toContain('theme-success')
        ->toContain('theme-warning')
        ->toContain('theme-error')
        ->toContain('data-details="false"')
        ->toContain('data-minimap="false"')
        ->toContain('data-history="false"')
        ->toContain('data-reroute="false"')
        ->toContain(__('daisy::components.blueprint_template.contract.title'))
        ->toContain('nodes[].id/type/label')
        ->toContain('edges[].source/sourcePort')
        ->toContain('inputs/outputs')
        ->toContain('controls/fields')
        ->toContain('daisy:blueprint:init')
        ->toContain('daisy:blueprint:change')
        ->toContain('daisy:blueprint:error')
        ->toContain(__('daisy::components.blueprint_template.features.title'))
        ->toContain(__('daisy::components.blueprint_template.examples.workflow.title'))
        ->toContain(__('daisy::components.blueprint_template.examples.theme_tokens.title'))
        ->toContain(__('daisy::components.blueprint_template.examples.typed.title'))
        ->toContain(__('daisy::components.blueprint_template.examples.minimal.title'))
        ->toContain(__('daisy::components.blueprint_template.examples.schema.title'));
});

it('can hide the blueprint contract notes for compact embeds', function () {
    $html = Blade::render('<x-daisy::templates.advanced.blueprint :show-header="false" :show-contract="false" name-prefix="demo" />');

    expect($html)
        ->toContain('data-module="blueprint"')
        ->not->toContain('data-blueprint-contract')
        ->not->toContain(__('daisy::components.blueprint_template.contract.title'));
});

it('renders the blueprint examples template through the public component alias', function () {
    $html = Blade::render('<x-daisy::templates.advanced.blueprint :show-header="false" name-prefix="demo" />');

    expect($html)
        ->toContain('data-module="blueprint"')
        ->toContain('name="demo_workflow"')
        ->toContain('name="demo_typed"')
        ->toContain('name="demo_integration"')
        ->not->toContain('<h1');
});
