    <x-daisy::ui.data-display.table
        mode="server"
        endpoint="/interventions"
        :columns="[
            ['key' => 'name', 'label' => 'Name'],
        ]"
        :filters="[
            ['key' => 'reference_internal', 'label' => 'Reference', 'type' => 'text'],
        ]"
    >
        <x-slot:filtersSlot>
            <div data-external-filters>External filters</div>
        </x-slot:filtersSlot>
    </x-daisy::ui.data-display.table>