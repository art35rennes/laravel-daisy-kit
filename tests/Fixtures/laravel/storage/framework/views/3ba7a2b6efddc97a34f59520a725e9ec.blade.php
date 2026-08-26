    <x-daisy::forms.viewer
        :schema="[
            'version' => '1.0',
            'id' => 'agreement',
            'fields' => [
                [
                    'id' => 'signature',
                    'type' => 'signature',
                    'name' => 'signature',
                    'label' => 'Signature',
                    'attrs' => [
                        'width' => 620,
                        'height' => 240,
                        'penColor' => '#123456',
                        'minWidth' => 1,
                        'maxWidth' => 4,
                        'velocityFilterWeight' => 0.4,
                        'responsive' => true,
                        'showActions' => false,
                        'downloadFormat' => 'svg',
                        'downloadFilename' => 'agreement-signature',
                    ],
                ],
            ],
        ]"
        :value="['signature' => 'data:image/png;base64,abc']"
    />