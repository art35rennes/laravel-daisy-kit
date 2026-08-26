    <x-daisy::ui.data-display.table
        row-key="id"
        :editable="[
            'enabled' => true,
            'mode' => 'row',
            'update' => ['strategy' => 'local'],
            'create' => [
                'enabled' => true,
                'strategy' => 'remote',
                'endpoint' => ['url' => '/projects', 'method' => 'POST'],
                'defaults' => ['status' => 'draft'],
            ],
        ]"
        :columns="[
            ['key' => 'name', 'label' => 'Name', 'editor' => ['type' => 'text', 'required' => true]],
            ['key' => 'status', 'label' => 'Status', 'editor' => ['type' => 'select', 'options' => [['value' => 'draft', 'label' => 'Draft']]]],
        ]"
    />