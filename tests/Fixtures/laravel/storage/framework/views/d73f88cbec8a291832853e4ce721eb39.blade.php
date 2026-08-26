    <x-daisy::forms.viewer
        :schema="[
            'version' => '1.0',
            'id' => 'profile',
            'fields' => [
                ['id' => 'name', 'type' => 'text', 'name' => 'name', 'label' => 'Name'],
            ],
            'submit' => ['mode' => 'event', 'label' => 'Save'],
        ]"
        :value="['name' => 'Ada']"
        :readonly="true"
    />