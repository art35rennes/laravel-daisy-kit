@php
    // Données d'exemple pour la démonstration
    // Utiliser les données de la session si disponibles (après soumission), sinon les valeurs par défaut
    $user = (object) [
        'name' => old('name', session('demo_user.name', 'Jean Dupont')),
        'email' => old('email', session('demo_user.email', 'jean@example.com')),
        'phone' => old('phone', session('demo_user.phone', '+33 6 12 34 56 78')),
        'bio' => old('bio', session('demo_user.bio', 'Développeur passionné par Laravel et Vue.js')),
        'street' => old('street', session('demo_user.street', '123 Rue de la République')),
        'city' => old('city', session('demo_user.city', 'Paris')),
        'postal_code' => old('postal_code', session('demo_user.postal_code', '75001')),
        'country' => old('country', session('demo_user.country', 'France')),
        'language' => old('language', session('demo_user.language', 'fr')),
        'timezone' => old('timezone', session('demo_user.timezone', 'Europe/Paris')),
        'notifications' => old('notifications', session('demo_user.notifications', true)),
    ];
@endphp

<x-daisy::form.with-tabs
    title="Modifier le profil"
    action="{{ route('templates.form.with-tabs.store') }}"
    method="POST"
    :tabs="[
        ['id' => 'general', 'label' => 'Général', 'icon' => 'person', 'fields' => ['name', 'email', 'phone', 'bio']],
        ['id' => 'address', 'label' => 'Adresse', 'icon' => 'geo-alt', 'fields' => ['street', 'city', 'postal_code', 'country']],
        ['id' => 'preferences', 'label' => 'Préférences', 'icon' => 'gear', 'fields' => ['language', 'timezone', 'notifications']],
    ]"
    :activeTab="old('_active_tab', $activeTab ?? 'general')"
    tabsStyle="box"
>
    <x-slot:tab_general>
        <div class="space-y-4">
            <x-daisy::ui.partials.form-field name="name" label="Nom complet" required>
                <x-daisy::ui.inputs.input
                    name="name"
                    type="text"
                    :value="old('name', $user->name)"
                    placeholder="Votre nom complet"
                    required
                />
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field name="email" label="Email" required>
                <x-daisy::ui.inputs.input
                    name="email"
                    type="email"
                    :value="old('email', $user->email)"
                    placeholder="email@example.com"
                    required
                />
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field name="phone" label="Téléphone">
                <x-daisy::ui.inputs.input
                    name="phone"
                    type="tel"
                    :value="old('phone', $user->phone)"
                    placeholder="+33 6 12 34 56 78"
                />
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field name="bio" label="Biographie">
                <x-daisy::ui.inputs.textarea
                    name="bio"
                    placeholder="Parlez-nous de vous..."
                    rows="4"
                >
                    {{ old('bio', $user->bio) }}
                </x-daisy::ui.inputs.textarea>
            </x-daisy::ui.partials.form-field>
        </div>
    </x-slot:tab_general>

    <x-slot:tab_address>
        <div class="space-y-4">
            <x-daisy::ui.partials.form-field name="street" label="Rue">
                <x-daisy::ui.inputs.input
                    name="street"
                    type="text"
                    :value="old('street', $user->street)"
                    placeholder="123 Rue de la République"
                />
            </x-daisy::ui.partials.form-field>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-daisy::ui.partials.form-field name="city" label="Ville">
                    <x-daisy::ui.inputs.input
                        name="city"
                        type="text"
                        :value="old('city', $user->city)"
                        placeholder="Paris"
                    />
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="postal_code" label="Code postal">
                    <x-daisy::ui.inputs.input
                        name="postal_code"
                        type="text"
                        :value="old('postal_code', $user->postal_code)"
                        placeholder="75001"
                    />
                </x-daisy::ui.partials.form-field>
            </div>

            <x-daisy::ui.partials.form-field name="country" label="Pays">
                <x-daisy::ui.inputs.select name="country">
                    <option value="">Sélectionner un pays</option>
                    <option value="fr" {{ old('country', $user->country) === 'fr' ? 'selected' : '' }}>France</option>
                    <option value="be" {{ old('country', $user->country) === 'be' ? 'selected' : '' }}>Belgique</option>
                    <option value="ch" {{ old('country', $user->country) === 'ch' ? 'selected' : '' }}>Suisse</option>
                    <option value="ca" {{ old('country', $user->country) === 'ca' ? 'selected' : '' }}>Canada</option>
                </x-daisy::ui.inputs.select>
            </x-daisy::ui.partials.form-field>
        </div>
    </x-slot:tab_address>

    <x-slot:tab_preferences>
        <div class="space-y-4">
            <x-daisy::ui.partials.form-field name="language" label="Langue">
                <x-daisy::ui.inputs.select name="language">
                    <option value="fr" {{ old('language', $user->language) === 'fr' ? 'selected' : '' }}>Français</option>
                    <option value="en" {{ old('language', $user->language) === 'en' ? 'selected' : '' }}>English</option>
                    <option value="es" {{ old('language', $user->language) === 'es' ? 'selected' : '' }}>Español</option>
                    <option value="de" {{ old('language', $user->language) === 'de' ? 'selected' : '' }}>Deutsch</option>
                </x-daisy::ui.inputs.select>
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field name="timezone" label="Fuseau horaire">
                <x-daisy::ui.inputs.select name="timezone">
                    <option value="Europe/Paris" {{ old('timezone', $user->timezone) === 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris (CET)</option>
                    <option value="Europe/London" {{ old('timezone', $user->timezone) === 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT)</option>
                    <option value="America/New_York" {{ old('timezone', $user->timezone) === 'America/New_York' ? 'selected' : '' }}>America/New_York (EST)</option>
                    <option value="Asia/Tokyo" {{ old('timezone', $user->timezone) === 'Asia/Tokyo' ? 'selected' : '' }}>Asia/Tokyo (JST)</option>
                </x-daisy::ui.inputs.select>
            </x-daisy::ui.partials.form-field>

            <x-daisy::ui.partials.form-field name="notifications" label="Notifications">
                <div class="flex items-center gap-3">
                    <x-daisy::ui.inputs.toggle
                        name="notifications"
                        :checked="old('notifications', $user->notifications)"
                    />
                    <span class="text-sm">Recevoir les notifications par email</span>
                </div>
            </x-daisy::ui.partials.form-field>
        </div>
    </x-slot:tab_preferences>

</x-daisy::form.with-tabs>

@if(session('success'))
    <div class="mt-4">
        <x-daisy::ui.feedback.alert color="success">
            {{ session('success') }}
        </x-daisy::ui.feedback.alert>
    </div>
@endif

