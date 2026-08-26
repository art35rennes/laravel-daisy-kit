    <x-daisy::ui.data-display.table
        :columns="[['key' => 'name', 'label' => 'Name']]"
        :rows="[['uuid' => 'user-1', 'name' => 'Jane']]"
        row-key="uuid"
        selection="single"
        selection-read-only
    />