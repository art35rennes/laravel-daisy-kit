<div class="space-y-4">
    <h3 class="text-lg font-semibold">Coordonnées</h3>
    <x-daisy::ui.partials.form-field name="email" label="Email" required>
        <x-daisy::ui.inputs.input name="email" type="email" placeholder="jean.dupont@example.com" :value="old('email')" />
    </x-daisy::ui.partials.form-field>
    <x-daisy::ui.partials.form-field name="phone" label="Téléphone">
        <x-daisy::ui.inputs.input name="phone" type="tel" placeholder="+33 6 12 34 56 78" :value="old('phone')" />
    </x-daisy::ui.partials.form-field>
    <x-daisy::ui.partials.form-field name="address" label="Adresse">
        <x-daisy::ui.inputs.textarea name="address" placeholder="123 rue de la République, 75001 Paris" :value="old('address')" />
    </x-daisy::ui.partials.form-field>
</div>

