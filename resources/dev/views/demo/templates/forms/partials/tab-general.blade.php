<div class="space-y-4">
    <x-daisy::ui.partials.form-field name="name" label="Nom complet" required>
        <x-daisy::ui.inputs.input name="name" type="text" placeholder="Jean Dupont" :value="old('name', 'Jean Dupont')" />
    </x-daisy::ui.partials.form-field>
    <x-daisy::ui.partials.form-field name="email" label="Email" required>
        <x-daisy::ui.inputs.input name="email" type="email" placeholder="jean.dupont@example.com" :value="old('email', 'jean.dupont@example.com')" />
    </x-daisy::ui.partials.form-field>
    <x-daisy::ui.partials.form-field name="bio" label="Biographie">
        <x-daisy::ui.inputs.textarea name="bio" placeholder="Parlez-nous de vous..." :value="old('bio')" />
    </x-daisy::ui.partials.form-field>
</div>

