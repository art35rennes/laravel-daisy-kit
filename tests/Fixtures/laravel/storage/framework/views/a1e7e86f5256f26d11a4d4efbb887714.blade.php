    <x-daisy::ui.data-display.table
        row-key="id"
        row-detail="inline"
        row-detail-view="table-test::table.detail"
        :columns="[
            ['key' => 'name', 'label' => 'Name'],
        ]"
        :rows="[
            ['id' => 'user-1', 'name' => 'Jane'],
        ]"
    />