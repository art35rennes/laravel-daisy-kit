    <x-daisy::forms.viewer
        :schema="[
            'version' => '1.0',
            'id' => 'contact',
            'meta' => ['title' => 'Contact'],
            'fields' => [
                ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email', 'rules' => ['required', 'email']],
                ['id' => 'total', 'type' => 'number', 'name' => 'total', 'label' => 'Total', 'computed' => ['type' => 'jsonata', 'expression' => '1 + 1', 'dependsOn' => [], 'mode' => 'readonly']],
            ],
            'submit' => ['mode' => 'event', 'label' => 'Send'],
        ]"
        :value="['email' => 'jane@example.com']"
    />