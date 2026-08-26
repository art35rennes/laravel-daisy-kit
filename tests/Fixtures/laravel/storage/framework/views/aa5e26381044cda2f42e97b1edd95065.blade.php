    <x-daisy::templates.form.form-simple id="simple-business-form" autocomplete="off" />

    <x-daisy::templates.form.form-inline id="inline-business-form" autocomplete="off" />

    <x-daisy::templates.form.form-with-tabs
        id="tabs-business-form"
        autocomplete="off"
        :tabs="[['id' => 'general', 'label' => 'General']]"
    />

    <x-daisy::templates.form.form-wizard
        id="wizard-business-form"
        autocomplete="off"
        :steps="[['key' => 'details', 'label' => 'Details']]"
    />