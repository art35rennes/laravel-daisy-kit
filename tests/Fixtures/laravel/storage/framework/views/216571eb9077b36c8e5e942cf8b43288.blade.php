    <x-daisy::forms.viewer
        :schema="[
            'version' => '1.0',
            'id' => 'profile',
            'fields' => [
                [
                    'id' => 'profile_tabs',
                    'type' => 'tabs',
                    'label' => 'Profile sections',
                    'fields' => [
                        [
                            'id' => 'contact_tab',
                            'type' => 'section',
                            'label' => 'Contact',
                            'fields' => [
                                ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
                            ],
                        ],
                        [
                            'id' => 'bio_tab',
                            'type' => 'section',
                            'label' => 'Bio',
                            'fields' => [
                                ['id' => 'bio', 'type' => 'textarea', 'name' => 'bio', 'label' => 'Bio'],
                            ],
                        ],
                    ],
                ],
            ],
        ]"
        :value="['email' => 'jane@example.com', 'bio' => 'Builder friendly']"
    />