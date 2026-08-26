    <x-daisy::forms.viewer
        id="upload-viewer"
        method="POST"
        :schema="[
            'version' => '1.0',
            'id' => 'upload',
            'fields' => [
                [
                    'id' => 'documents',
                    'type' => 'section',
                    'label' => 'Documents',
                    'fields' => [
                        [
                            'id' => 'attachments',
                            'type' => 'file',
                            'name' => 'attachments',
                            'label' => 'Attachments',
                            'attrs' => [
                                'accept' => '.pdf,image/*',
                                'multiple' => true,
                            ],
                            'ui' => [
                                'size' => 'sm',
                                'color' => 'primary',
                            ],
                        ],
                    ],
                ],
            ],
        ]"
    />