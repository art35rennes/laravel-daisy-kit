    <x-daisy::ui.data-display.table
        :columns="[
            ['key' => 'status', 'label' => 'Status', 'cell' => ['renderer' => 'trusted-html']],
        ]"
        :rows="[
            ['status' => '<span class=&quot;badge badge-success&quot;>Active</span>'],
        ]"
    />