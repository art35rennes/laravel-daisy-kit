    <x-daisy::forms.viewer
        :schema="[
            'version' => '1.0',
            'id' => 'contact',
            'fields' => [
                [
                    'id' => 'name',
                    'type' => 'text',
                    'name' => 'name',
                    'label' => 'Name',
                    'attrs' => [
                        'placeholder' => 'Jane Doe',
                        'autocomplete' => 'name',
                        'mask' => '999-999',
                        'maskCharPlaceholder' => '_',
                        'maskPlaceholder' => true,
                        'inputPlaceholder' => true,
                        'clearIncomplete' => true,
                        'obfuscate' => true,
                        'obfuscateChar' => '*',
                        'obfuscateKeepEnd' => 2,
                    ],
                    'ui' => ['size' => 'sm', 'color' => 'primary', 'width' => '1/2'],
                ],
                [
                    'id' => 'score',
                    'type' => 'range',
                    'name' => 'score',
                    'label' => 'Score',
                    'attrs' => ['min' => 10, 'max' => 90, 'step' => 5],
                    'ui' => ['size' => 'lg', 'color' => 'accent'],
                ],
            ],
        ]"
    />