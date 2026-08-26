    <x-daisy::ui.data-display.table
        mode="server"
        endpoint="/audits"
        row-key="id"
        row-detail="modal"
        row-detail-view="table-test::table.actions"
        column-resizing
        :columns="[
            ['key' => 'created_at', 'label' => 'Created', 'filterable' => true, 'filter' => ['type' => 'date']],
        ]"
        :filters="[
            ['key' => 'period', 'label' => 'Period', 'type' => 'date-range', 'filterKeyFrom' => 'started_after', 'filterKeyTo' => 'started_before'],
        ]"
    />