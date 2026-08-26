    <x-daisy::forms.viewer
        method="PATCH"
        :schema="[
            'version' => '1.0',
            'id' => 'contact',
            'fields' => [
                ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
            ],
        ]"
    />