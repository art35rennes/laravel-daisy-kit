<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

it('renders the simplified editable Blueprint contract', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.advanced.blueprint
            name="workflow"
            mode="edit"
            direction="TB"
            height="640px"
            layout="radial"
            transition-shape="orthogonal"
            transition-color="accent"
            node-color="neutral"
            inspector-mode="sidebar"
            :node-categories="[[
                'value' => 'approval',
                'label' => 'Approbation',
                'color' => 'success',
                'defaults' => ['required_approvals' => 2],
                'fields' => [[
                    'key' => 'owner_uuid',
                    'type' => 'select',
                    'label' => 'Responsable',
                    'required' => true,
                    'options' => [['value' => 'ada', 'label' => 'Ada']],
                ]],
            ]]"
            :transition-categories="[[
                'value' => 'return',
                'label' => 'Retour',
                'shape' => 's',
                'color' => 'warning',
            ]]"
            :value="[
                'nodes' => [['id' => 'review', 'label' => 'Révision']],
                'transitions' => [],
            ]"
        />
        BLADE);

    expect($html)
        ->toContain('data-module="blueprint"')
        ->toContain('data-blueprint="1"')
        ->toContain('data-mode="edit"')
        ->toContain('data-direction="TB"')
        ->toContain('data-layout="radial"')
        ->toContain('data-transition-shape="orthogonal"')
        ->toContain('data-transition-color="accent"')
        ->toContain('data-node-color="neutral"')
        ->toContain('data-inspector-mode="sidebar"')
        ->toContain('name="workflow"')
        ->toContain('data-blueprint-world')
        ->toContain('data-blueprint-edges')
        ->toContain('data-blueprint-transition-label-layer')
        ->toContain('data-blueprint-nodes')
        ->toContain('data-blueprint-inspector')
        ->toContain('data-blueprint-inspector-backdrop')
        ->toContain('data-blueprint-discard-dialog')
        ->toContain('data-autosave="false"')
        ->toContain('data-blueprint-mobile-list')
        ->toContain('data-blueprint-action="add-node"')
        ->toContain('data-blueprint-action="arrange"')
        ->toContain('data-blueprint-action="fit"')
        ->toContain('"value":"approval"')
        ->toContain('"color":"success"')
        ->toContain('"defaults":{"required_approvals":2}')
        ->toContain('"key":"owner_uuid"')
        ->toContain('"type":"select"')
        ->toContain('"required":true')
        ->toContain('"value":"return"')
        ->toContain('"shape":"s"')
        ->toContain('"color":"warning"');
});

it('safely serializes integrator schema labels and values', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.advanced.blueprint
            :node-categories="[[
                'value' => 'safe',
                'label' => '</textarea><script>alert(1)</script>',
                'defaults' => ['content' => '</textarea><img src=x onerror=alert(1)>'],
                'fields' => [[
                    'key' => 'content',
                    'type' => 'textarea',
                    'label' => '</textarea><script>alert(2)</script>',
                ]],
            ]]"
        />
        BLADE);

    expect($html)
        ->not->toContain('</textarea><script>')
        ->not->toContain('<img src=x')
        ->toContain('\u003C\/textarea\u003E\u003Cscript\u003Ealert(1)\u003C\/script\u003E')
        ->toContain('data-blueprint-integrator-fields');
});

it('renders Blueprint inspector autosave as an explicit opt-in', function () {
    $html = Blade::render('<x-daisy::ui.advanced.blueprint autosave />');

    expect($html)
        ->toContain('data-autosave="true"')
        ->toContain(__('daisy::components.blueprint_editor.autosave'))
        ->not->toContain('data-blueprint-action="save"');
});

it('renders Blueprint view mode without editing controls', function () {
    $html = Blade::render('<x-daisy::ui.advanced.blueprint mode="view" :value="[]" />');

    expect($html)
        ->toContain('data-mode="view"')
        ->not->toContain('data-blueprint-action="add-node"')
        ->not->toContain('data-blueprint-inspector');
});

it('normalizes invalid visual configuration values in the Blade boundary', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.advanced.blueprint
            mode="workflow"
            direction="RL"
            layout="force"
            transition-shape="spiral"
            transition-color="magenta"
            node-color="magenta"
            inspector-mode="drawer"
        />
        BLADE);

    expect($html)
        ->toContain('data-mode="edit"')
        ->toContain('data-direction="LR"')
        ->toContain('data-layout="hierarchical"')
        ->toContain('data-transition-shape="curve"')
        ->toContain('data-transition-color="primary"')
        ->toContain('data-node-color="primary"')
        ->toContain('data-inspector-mode="modal"');
});

it('uses a centered modal inspector by default', function () {
    $html = Blade::render('<x-daisy::ui.advanced.blueprint />');

    expect($html)
        ->toContain('data-inspector-mode="modal"')
        ->toContain('<dialog')
        ->toContain('modal-middle');
});

it('renders the three focused Blueprint examples through the public view alias', function () {
    $html = View::make('daisy::templates.advanced.blueprint')->render();

    expect($html)
        ->toContain('name="blueprint_approval"')
        ->toContain('name="blueprint_cycle"')
        ->toContain('name="blueprint_dense"')
        ->toContain(__('daisy::components.blueprint_template.examples.approval.title'))
        ->toContain(__('daisy::components.blueprint_template.examples.cycle.title'))
        ->toContain(__('daisy::components.blueprint_template.examples.dense.title'))
        ->toContain('"key":"owner"')
        ->toContain('"key":"notify"')
        ->toContain('"priority":"high"')
        ->toContain('daisy:blueprint:select')
        ->not->toContain('sourcePort')
        ->not->toContain('nodeTypes');
});

it('renders the Blueprint examples through the public component alias', function () {
    $html = Blade::render('<x-daisy::templates.advanced.blueprint :show-header="false" name-prefix="demo" />');

    expect($html)
        ->toContain('name="demo_approval"')
        ->toContain('name="demo_cycle"')
        ->toContain('name="demo_dense"')
        ->not->toContain('<h1');
});
