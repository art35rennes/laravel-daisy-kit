    <x-daisy::ui.data-display.table
        mode="client"
        row-key="id"
        search-mode="includes"
        sub-rows-key="children"
        column-resizing
        editable
        edit-endpoint="/users/edit"
        edit-method="PUT"
        edit-mode="row"
        :editable-columns="['name']"
        :edit-policy="['required' => ['name']]"
        :columns="[
            ['key' => 'name', 'label' => 'Name', 'size' => 160, 'minSize' => 80, 'maxSize' => 320],
            ['key' => 'status', 'label' => 'Status'],
        ]"
        :rows="[
            ['id' => 'user-1', 'name' => 'Jane', 'status' => 'draft', 'children' => []],
        ]"
    />