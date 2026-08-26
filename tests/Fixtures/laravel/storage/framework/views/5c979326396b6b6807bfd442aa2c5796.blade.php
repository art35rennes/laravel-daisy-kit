    <x-daisy::forms.viewer
        id="contact-viewer"
        :schema="[
            'version' => '1.0',
            'id' => 'contact',
            'fields' => [
                ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
                [
                    'id' => 'civility',
                    'type' => 'radio',
                    'name' => 'civility',
                    'label' => 'Civility',
                    'options' => [
                        ['value' => 'mr', 'label' => 'Mr'],
                        ['value' => 'mrs', 'label' => 'Mrs'],
                    ],
                ],
                ['id' => 'terms', 'type' => 'toggle', 'name' => 'terms', 'label' => 'Terms'],
                ['id' => 'signature', 'type' => 'signature', 'name' => 'signature', 'label' => 'Signature'],
            ],
        ]"
    />