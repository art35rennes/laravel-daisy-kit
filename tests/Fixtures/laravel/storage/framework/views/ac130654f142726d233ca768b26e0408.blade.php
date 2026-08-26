    <x-daisy::ui.data-display.table
        selection="multiple"
        row-key="id"
        sub-rows-key="children"
        sub-row-selection="master-only"
        :columns="[['key' => 'name', 'label' => 'Name']]"
        :rows="[['id' => 'parent', 'name' => 'Parent', 'children' => [['id' => 'child', 'name' => 'Child']]]]"
    />