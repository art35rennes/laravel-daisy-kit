    <x-daisy::templates.profile.profile-edit
        :show-phone="true"
        :show-location="true"
        :show-website="true"
        :profile="[
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'phone' => '+33123456789',
            'location' => 'Rennes',
            'website' => 'https://example.com',
        ]"
    />