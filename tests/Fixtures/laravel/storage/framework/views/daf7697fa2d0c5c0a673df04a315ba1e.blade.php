    <x-daisy::forms.viewer
        :schema="[
            'version' => '1.0',
            'id' => 'onboarding',
            'layout' => ['type' => 'multi-step'],
            'fields' => [
                [
                    'id' => 'contact',
                    'type' => 'wizardStep',
                    'label' => 'Contact',
                    'fields' => [
                        ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
                    ],
                ],
                [
                    'id' => 'profile',
                    'type' => 'wizardStep',
                    'label' => 'Profile',
                    'fields' => [
                        ['id' => 'bio', 'type' => 'textarea', 'name' => 'bio', 'label' => 'Bio'],
                    ],
                ],
            ],
        ]"
    />