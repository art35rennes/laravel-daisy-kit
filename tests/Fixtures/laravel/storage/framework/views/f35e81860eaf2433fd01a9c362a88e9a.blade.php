    <x-daisy::ui.data-display.table
        :columns="[
            ['key' => 'profile', 'label' => 'Profile', 'type' => 'resource-link'],
        ]"
        :rows="[
            ['profile' => ['label' => '<Open>', 'href' => 'javascript:alert(1)', 'target' => '_blank']],
        ]"
    />