    <x-daisy::forms.viewer
        id="brand-viewer"
        :schema="[
            'version' => '1.0',
            'id' => 'brand',
            'fields' => [
                [
                    'id' => 'brand_color',
                    'type' => 'color',
                    'name' => 'brand_color',
                    'label' => 'Brand color',
                    'attrs' => [
                        'mode' => 'advanced',
                        'dropdown' => true,
                        'swatches' => [['#123456', '#abcdef']],
                        'swatchesHeight' => 120,
                        'showAlpha' => false,
                        'showFormatToggle' => true,
                    ],
                ],
            ],
        ]"
        :value="['brand_color' => '#123456']"
    />