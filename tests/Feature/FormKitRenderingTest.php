<?php

use Illuminate\Support\Facades\Blade;

it('renders the form viewer public alias', function () {
    $html = Blade::render(<<<'BLADE'
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
    BLADE);

    expect($html)
        ->toContain('data-module="form-viewer"')
        ->toContain('data-form-id="')
        ->toContain('data-form-input="email"')
        ->toContain('jane@example.com')
        ->toContain('data-form-input="total"')
        ->toContain('data-form-schema')
        ->toContain('Send');

    expect((bool) preg_match('/<input(?=[^>]*data-form-input="total")(?=[^>]*readonly)(?![^>]*disabled)[^>]*>/', $html))->toBeTrue();
});

it('renders a multi step form viewer with Blade-owned step markup', function () {
    $html = Blade::render(<<<'BLADE'
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
    BLADE);

    expect($html)
        ->toContain('data-form-step-indicator="0"')
        ->toContain('data-form-step-indicator="1"')
        ->toContain('data-form-step="contact"')
        ->toContain('data-form-step="profile"')
        ->toContain('data-form-previous')
        ->toContain('data-form-next')
        ->toContain('data-form-submit');
});

it('renders static text as layout content without submitting a field value', function () {
    $html = Blade::render(<<<'BLADE'
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
    BLADE);

    expect($html)
        ->toContain('data-form-field="intro"')
        ->toContain('Read this before continuing.')
        ->toContain('md:col-span-6')
        ->toContain('data-form-input="email"')
        ->not->toContain('data-form-input="intro"')
        ->not->toContain('name="intro"');
});

it('links viewer labels to stable field control ids', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::forms.viewer
            id="contact-viewer"
            :schema="[
                'version' => '1.0',
                'id' => 'contact',
                'fields' => [
                    ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
                    [
                        'id' => 'civility',
                        'type' => 'radio',
                        'name' => 'civility',
                        'label' => 'Civility',
                        'options' => [
                            ['value' => 'mr', 'label' => 'Mr'],
                            ['value' => 'mrs', 'label' => 'Mrs'],
                        ],
                    ],
                    ['id' => 'terms', 'type' => 'toggle', 'name' => 'terms', 'label' => 'Terms'],
                    ['id' => 'signature', 'type' => 'signature', 'name' => 'signature', 'label' => 'Signature'],
                ],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('for="contact-viewer-email-control"')
        ->toContain('id="contact-viewer-email-control"')
        ->toContain('for="contact-viewer-civility-control-mr"')
        ->toContain('id="contact-viewer-civility-control-mr"')
        ->toContain('for="contact-viewer-civility-control-mrs"')
        ->toContain('id="contact-viewer-civility-control-mrs"')
        ->toContain('for="contact-viewer-terms-control"')
        ->toContain('id="contact-viewer-terms-control"')
        ->toContain('data-form-input="signature"');

    expect($html)->not->toContain('for="signature"');
});

it('renders tabs containers with recursive viewer fields', function () {
    $html = Blade::render(<<<'BLADE'
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
    BLADE);

    expect($html)
        ->toContain('data-form-field="profile_tabs"')
        ->toContain('class="tab"')
        ->toContain('aria-label="Contact"')
        ->toContain('aria-label="Bio"')
        ->toContain('tab-content')
        ->toContain('data-form-input="email"')
        ->toContain('jane@example.com')
        ->toContain('data-form-input="bio"')
        ->toContain('Builder friendly');
});

it('renders the form builder public alias', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::forms.builder
            name="schema"
            :functionCatalog="[
                ['name' => '$uuid', 'signature' => '<s:s>', 'description' => 'UUID'],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('data-form-builder-livewire')
        ->toContain('data-builder-palette')
        ->toContain('data-builder-json')
        ->toContain('data-module="form-viewer"')
        ->toContain('name="schema"')
        ->toContain('$uuid');
});

it('renders the form builder template with builder and viewer surfaces', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::templates.form.builder
            :schema="[
                'version' => '1.0',
                'id' => 'contact',
                'meta' => ['title' => 'Contact'],
                'fields' => [
                    ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
                ],
            ]"
            :value="['email' => 'ada@example.com']"
            schema-name="form_schema"
        />
    BLADE);

    expect($html)
        ->toContain('data-form-builder-livewire')
        ->toContain('data-module="form-viewer"')
        ->toContain('name="form_schema"')
        ->toContain('ada@example.com');
});

it('uses schema submit mode by default and explicit submit mode as override', function () {
    $schema = [
        'version' => '1.0',
        'id' => 'contact',
        'fields' => [
            ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
        ],
        'submit' => ['mode' => 'fetch', 'label' => 'Send'],
    ];

    $schemaDriven = Blade::render('<x-daisy::forms.viewer :schema="$schema" />', compact('schema'));
    $overridden = Blade::render('<x-daisy::forms.viewer :schema="$schema" submit-mode="none" />', compact('schema'));

    expect($schemaDriven)
        ->toContain('data-submit-mode="fetch"')
        ->toContain('Send')
        ->and($overridden)
        ->toContain('data-submit-mode="none"')
        ->not->toContain('data-form-submit');
});

it('falls back to event submit mode when schema and prop do not define one', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::forms.viewer
            :schema="[
                'version' => '1.0',
                'id' => 'contact',
                'fields' => [
                    ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
                ],
            ]"
        />
    BLADE);

    expect($html)->toContain('data-submit-mode="event"');
});

it('falls back to event submit mode when schema submit mode is invalid', function () {
    $html = Blade::render(<<<'BLADE'
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
    BLADE);

    expect($html)
        ->toContain('data-submit-mode="event"')
        ->toContain('Send');
});

it('spoofs non-post viewer methods through Laravel form method spoofing', function () {
    $html = Blade::render(<<<'BLADE'
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
    BLADE);

    expect($html)
        ->toContain('method="POST"')
        ->toContain('data-form-method="PATCH"')
        ->toContain('name="_method"')
        ->toContain('value="PATCH"')
        ->toContain('name="_token"');
});

it('renders readonly viewers as identifiable display-only forms', function () {
    $html = Blade::render(<<<'BLADE'
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
    BLADE);

    expect($html)
        ->toContain('data-readonly="true"')
        ->toContain('data-submit-mode="event"')
        ->toContain('data-form-input="name"')
        ->toContain('disabled')
        ->not->toContain('data-form-submit');
});

it('applies field attrs and ui hints through the viewer package inputs', function () {
    $html = Blade::render(<<<'BLADE'
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
    BLADE);

    expect($html)
        ->toContain('placeholder="Jane Doe"')
        ->toContain('autocomplete="name"')
        ->toContain('data-inputmask="1"')
        ->toContain('data-mask="999-999"')
        ->toContain('data-char-placeholder="_"')
        ->toContain('data-mask-placeholder="true"')
        ->toContain('data-input-placeholder="true"')
        ->toContain('data-clear-incomplete="true"')
        ->toContain('data-obfuscate="1"')
        ->toContain('data-obfuscate-char="*"')
        ->toContain('data-obfuscate-keep-end="2"')
        ->toContain('input-sm')
        ->toContain('input-primary')
        ->toContain('md:col-span-6')
        ->toContain('min="10"')
        ->toContain('max="90"')
        ->toContain('step="5"')
        ->toContain('range-lg')
        ->toContain('range-accent');
});

it('does not initialize input mask for plain viewer inputs without a mask', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::forms.viewer
            :schema="[
                'version' => '1.0',
                'id' => 'contact',
                'fields' => [
                    [
                        'id' => 'email',
                        'type' => 'email',
                        'name' => 'email',
                        'label' => 'Email',
                    ],
                ],
            ]"
            :value="['email' => 'ada@example.com']"
        />
    BLADE);

    expect($html)
        ->toContain('value="ada@example.com"')
        ->not->toContain('data-inputmask="1"');
});

it('passes signature values and package props through the viewer', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::forms.viewer
            :schema="[
                'version' => '1.0',
                'id' => 'agreement',
                'fields' => [
                    [
                        'id' => 'signature',
                        'type' => 'signature',
                        'name' => 'signature',
                        'label' => 'Signature',
                        'attrs' => [
                            'width' => 620,
                            'height' => 240,
                            'penColor' => '#123456',
                            'minWidth' => 1,
                            'maxWidth' => 4,
                            'velocityFilterWeight' => 0.4,
                            'responsive' => true,
                            'showActions' => false,
                            'downloadFormat' => 'svg',
                            'downloadFilename' => 'agreement-signature',
                        ],
                    ],
                ],
            ]"
            :value="['signature' => 'data:image/png;base64,abc']"
        />
    BLADE);

    expect($html)
        ->toContain('data-form-input="signature"')
        ->toContain('data-width="620"')
        ->toContain('data-height="240"')
        ->toContain('data-pen-color="#123456"')
        ->toContain('data-min-width="1"')
        ->toContain('data-max-width="4"')
        ->toContain('data-velocity-filter-weight="0.4"')
        ->toContain('data-responsive="true"')
        ->toContain('data-show-actions="false"')
        ->toContain('data-download-format="svg"')
        ->toContain('data-download-filename="agreement-signature"')
        ->toContain('value="data:image/png;base64,abc"')
        ->not->toContain('data-sign-clear')
        ->not->toContain('data-sign-download');
});

it('renders color fields with the package color picker component', function () {
    $html = Blade::render(<<<'BLADE'
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
    BLADE);

    expect($html)
        ->toContain('data-form-input="brand_color"')
        ->toContain('data-colorpicker="1"')
        ->toContain('data-value="#123456"')
        ->toContain('name="brand_color"')
        ->toContain('data-colorpicker-input')
        ->toContain('data-dropdown="true"')
        ->toContain('data-swatches-height="120"')
        ->toContain('data-show-alpha="false"')
        ->toContain('#abcdef');
});

it('renders file fields as multipart forms with package file input props', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::forms.viewer
            id="upload-viewer"
            method="POST"
            :schema="[
                'version' => '1.0',
                'id' => 'upload',
                'fields' => [
                    [
                        'id' => 'documents',
                        'type' => 'section',
                        'label' => 'Documents',
                        'fields' => [
                            [
                                'id' => 'attachments',
                                'type' => 'file',
                                'name' => 'attachments',
                                'label' => 'Attachments',
                                'attrs' => [
                                    'accept' => '.pdf,image/*',
                                    'multiple' => true,
                                ],
                                'ui' => [
                                    'size' => 'sm',
                                    'color' => 'primary',
                                ],
                            ],
                        ],
                    ],
                ],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('enctype="multipart/form-data"')
        ->toContain('data-form-input="attachments"')
        ->toContain('name="attachments"')
        ->toContain('accept=".pdf,image/*"')
        ->toContain('multiple')
        ->toContain('file-input-sm')
        ->toContain('file-input-primary');
});
