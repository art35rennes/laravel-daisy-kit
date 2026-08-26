    <x-daisy::ui.data-display.table
        row-key="id"
        :editable="[
            'enabled' => true,
            'update' => ['strategy' => 'local'],
        ]"
        :columns="[
            ['key' => 'status', 'label' => 'Status', 'editor' => ['type' => 'blade', 'view' => 'table-test::table.editor']],
        ]"
    />