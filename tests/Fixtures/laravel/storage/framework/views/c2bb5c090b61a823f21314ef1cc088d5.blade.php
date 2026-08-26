    <x-daisy::forms.viewer
        id="content-viewer"
        :schema="[
            'version' => '1.0',
            'id' => 'content',
            'fields' => [
                [
                    'id' => 'intro',
                    'type' => 'staticText',
                    'label' => 'Intro fallback',
                    'text' => 'Read this before continuing.',
                    'ui' => ['width' => '1/2'],
                ],
                ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
            ],
        ]"
    />