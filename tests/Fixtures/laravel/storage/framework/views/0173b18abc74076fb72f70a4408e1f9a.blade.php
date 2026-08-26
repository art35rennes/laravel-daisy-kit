    <x-daisy::ui.data-display.table
        mode="server"
        server-adapter="spatie-query-builder"
        persist-state="url"
        state-key="users-table"
        global-filter-key="global"
        endpoint="/users"
        :columns="[
            ['key' => 'name', 'label' => 'Name', 'sortable' => true, 'filterable' => true, 'sortKey' => 'users.name', 'filterKey' => 'name', 'filter' => ['type' => 'text']],
            ['key' => 'status', 'label' => 'Status', 'sortable' => true, 'filterable' => true, 'sortKey' => 'status', 'filterKey' => 'status', 'filter' => ['type' => 'select', 'options' => [['value' => 'active', 'label' => 'Active']]]],
            ['key' => 'is_published', 'label' => 'Published', 'filterable' => true, 'filter' => ['type' => 'boolean']],
        ]"
    />