    <x-daisy::ui.data-display.table
        selection="multiple"
        row-key="uuid"
        :columns="[
            ['key' => 'name', 'label' => 'Name'],
        ]"
        :rows="[
            ['uuid' => 'user-1', 'name' => 'Jane'],
            ['uuid' => 'user-2', 'name' => 'John'],
        ]"
    >
        <x-slot:bulkActions>
            <button type="button" data-table-bulk-action="archive">Archive</button>
        </x-slot:bulkActions>
    </x-daisy::ui.data-display.table>