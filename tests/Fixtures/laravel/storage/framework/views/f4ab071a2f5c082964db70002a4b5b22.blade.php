    <x-daisy::ui.data-display.table
        mode="server"
        endpoint="/audits"
        :columns="[
            ['key' => 'actions', 'label' => 'Actions', 'view' => 'table-test::missing'],
        ]"
    />