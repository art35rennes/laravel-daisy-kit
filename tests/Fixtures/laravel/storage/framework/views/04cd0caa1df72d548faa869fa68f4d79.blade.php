    <x-daisy::ui.data-display.table
        :columns="[
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'actions', 'label' => 'Actions', 'type' => 'actions', 'view' => 'table-test::table.actions'],
            ['key' => 'profile', 'label' => 'Profile', 'type' => 'resource-link'],
        ]"
        :rows="[
            ['id' => 1, 'name' => 'Jane', 'actions' => 'open', 'profile' => ['label' => 'Profile', 'href' => 'https://example.test/users/1', 'target' => '_blank']],
        ]"
    />