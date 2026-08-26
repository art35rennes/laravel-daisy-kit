    <x-daisy::ui.data-display.table
        :link-policy="['allowedSchemes' => ['myapp']]"
        :columns="[
            ['key' => 'mobile', 'label' => 'Mobile', 'type' => 'resource-link'],
            ['key' => 'scan', 'label' => 'Scan', 'type' => 'resource-link', 'cell' => ['allowedSchemes' => ['intent']]],
        ]"
        :rows="[
            ['mobile' => ['label' => 'Open app', 'href' => 'myapp://ticket/123', 'target' => '_blank'], 'scan' => ['label' => 'Scan', 'href' => 'intent://scan/#Intent;scheme=zxing;end']],
        ]"
    />