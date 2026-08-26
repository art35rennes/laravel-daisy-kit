    <x-daisy::forms.viewer
        id="business-viewer"
        autocomplete="off"
        :schema="[
            'version' => '1.0',
            'id' => 'business',
            'fields' => [
                [
                    'id' => 'organization',
                    'type' => 'text',
                    'name' => 'organization',
                    'label' => 'Organization',
                    'attrs' => ['autocomplete' => 'organization'],
                ],
            ],
        ]"
    />