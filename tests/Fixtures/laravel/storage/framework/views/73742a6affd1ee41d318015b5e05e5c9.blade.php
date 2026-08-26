    <x-daisy::ui.data-display.table
        :link-policy="['allowedSchemes' => ['javascript']]"
        :columns="[
            ['key' => 'mobile', 'label' => 'Mobile', 'type' => 'resource-link'],
            ['key' => 'danger', 'label' => 'Danger', 'type' => 'resource-link', 'cell' => ['allowedSchemes' => ['javascript']]],
        ]"
        :rows="[
            ['mobile' => ['label' => 'Open app', 'href' => 'myapp://ticket/123'], 'danger' => ['label' => '<Bad>', 'href' => 'javascript:alert(1)']],
        ]"
    />