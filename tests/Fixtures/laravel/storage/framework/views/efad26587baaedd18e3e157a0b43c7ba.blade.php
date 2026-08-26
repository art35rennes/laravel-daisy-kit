    <x-daisy::ui.data-display.table
        row-key="id"
        table-layout="auto"
        min-width="128rem"
        scroll-x="always"
        external-filters
        livewire-mode="ignore"
        :columns="[
            ['key' => '_action', 'label' => 'Actions', 'type' => 'actions'],
            ['key' => 'status_badge', 'label' => 'Status', 'align' => 'center', 'width' => '140px', 'cell' => ['renderer' => 'trusted-html']],
            ['key' => 'postal_address', 'label' => 'Address', 'truncate' => 2, 'width' => '260px', 'minWidth' => 'max-content', 'nowrap' => true],
        ]"
        :rows="[
            ['id' => 'row-1', '_action' => ['action' => 'open', 'label' => 'Open'], 'status_badge' => '<span class=&quot;badge&quot;>Open</span>', 'postal_address' => '12 rue longue'],
        ]"
        :filters="[
            ['key' => 'status', 'label' => 'Status', 'type' => 'text'],
        ]"
    />