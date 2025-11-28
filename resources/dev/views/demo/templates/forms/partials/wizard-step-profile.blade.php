<div class="space-y-4">
    <h3 class="text-lg font-semibold">Informations personnelles</h3>
    <x-daisy::ui.partials.form-field name="first_name" label="Prénom" required>
        <x-daisy::ui.inputs.input name="first_name" type="text" placeholder="Jean" :value="old('first_name')" />
    </x-daisy::ui.partials.form-field>
    <x-daisy::ui.partials.form-field name="last_name" label="Nom" required>
        <x-daisy::ui.inputs.input name="last_name" type="text" placeholder="Dupont" :value="old('last_name')" />
    </x-daisy::ui.partials.form-field>
    <x-daisy::ui.partials.form-field name="birthdate" label="Date de naissance">
        <x-daisy::ui.inputs.input name="birthdate" type="date" :value="old('birthdate')" />
    </x-daisy::ui.partials.form-field>
</div>

