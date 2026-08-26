    <x-daisy::forms.viewer
        :schema="[
            'version' => '1.0',
            'id' => 'contact',
            'fields' => [
                ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
            ],
            'submit' => ['mode' => 'bogus', 'label' => 'Send'],
        ]"
    />