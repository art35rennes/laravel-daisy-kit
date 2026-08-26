    <x-daisy::ui.data-display.table
        row-key="id"
        table-class="daisy-table-width-content"
        :columns="[
            ['key' => '_action', 'label' => 'Actions', 'type' => 'actions'],
            ['key' => 'name', 'label' => 'Name'],
        ]"
        :rows="[
            ['id' => 'row-1', '_action' => ['action' => 'open', 'label' => 'Open'], 'name' => 'Jane'],
        ]"
    />