    <x-daisy::ui.data-display.table
        mode="client"
        size="sm"
        zebra
        hover
        pin-rows
        pin-cols
        caption="Users"
        :columns="[
            ['key' => 'name', 'label' => 'Name', 'sortable' => true, 'width' => '180px'],
            ['key' => 'role', 'label' => 'Role', 'cellClass' => 'text-right'],
        ]"
        :rows="[
            ['name' => 'Jane', 'role' => 'Admin'],
        ]"
        :page-size-options="[10, 25]"
        column-visibility
    />