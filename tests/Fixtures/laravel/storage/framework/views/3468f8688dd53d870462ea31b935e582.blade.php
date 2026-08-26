<x-daisy::ui.advanced.blueprint
    :node-categories="[[
        'value' => 'safe',
        'label' => '</textarea><script>alert(1)</script>',
        'defaults' => ['content' => '</textarea><img src=x onerror=alert(1)>'],
        'fields' => [[
            'key' => 'content',
            'type' => 'textarea',
            'label' => '</textarea><script>alert(2)</script>',
        ]],
    ]]"
/>