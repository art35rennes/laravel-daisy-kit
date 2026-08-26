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
    autosave
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
>
    <x-slot:inspector><div data-host-inspector>Host content</div></x-slot:inspector>
</x-daisy::ui.advanced.blueprint>