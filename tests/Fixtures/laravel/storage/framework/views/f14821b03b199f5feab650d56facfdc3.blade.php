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