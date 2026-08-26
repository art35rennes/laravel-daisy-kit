    <x-daisy::ui.data-display.table
        selection="multiple"
        row-key="uuid"
        :select-filtered="false"
        :columns="[
            ['key' => 'name', 'label' => 'Name'],
        ]"
        :rows="[
            ['uuid' => 'user-1', 'name' => 'Jane'],
        ]"
    />