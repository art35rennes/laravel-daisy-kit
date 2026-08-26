    <x-daisy::ui.data-display.table
        row-key="id"
        sub-rows-key="children"
        :columns="[['key' => 'name', 'label' => 'Name']]"
        :rows="[
            ['id' => 'parent', 'name' => 'Parent', 'children' => [['id' => 'child', 'name' => 'First']]],
            ['id' => 'other', 'name' => 'Other', 'children' => [['id' => 'child', 'name' => 'Duplicate']]],
        ]"
    />