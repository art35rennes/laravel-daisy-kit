    <x-daisy::ui.data-display.table
        mode="server"
        endpoint="/interventions"
        column-visibility
        :columns="[
            ['key' => 'external_note', 'label' => 'External note', 'filterable' => true, 'filter' => ['type' => 'text']],
            ['key' => 'compile_status', 'label' => 'Compile status', 'filterable' => true, 'filter' => ['type' => 'select']],
            ['key' => 'name', 'label' => 'Name', 'filterable' => true, 'filter' => ['type' => 'text']],
            ['key' => 'company', 'label' => 'Company', 'filterable' => true, 'filter' => ['type' => 'text']],
            ['key' => 'city', 'label' => 'City', 'filterable' => true, 'filter' => ['type' => 'text']],
            ['key' => 'reference_internal', 'label' => 'Reference', 'filterable' => true, 'filter' => ['type' => 'text']],
        ]"
        :filters="[
            ['key' => 'intervention_type_code', 'label' => 'Intervention type', 'type' => 'text'],
        ]"
    />