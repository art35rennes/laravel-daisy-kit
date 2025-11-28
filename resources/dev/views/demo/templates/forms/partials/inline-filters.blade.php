<x-daisy::ui.partials.form-field name="search" label="Recherche">
    <x-daisy::ui.inputs.input name="search" type="text" placeholder="Rechercher..." :value="request('search')" />
</x-daisy::ui.partials.form-field>
<x-daisy::ui.partials.form-field name="status" label="Statut">
    <x-daisy::ui.inputs.select name="status" :value="request('status')">
        <option value="">Tous</option>
        <option value="active">Actif</option>
        <option value="inactive">Inactif</option>
        <option value="pending">En attente</option>
    </x-daisy::ui.inputs.select>
</x-daisy::ui.partials.form-field>
<x-daisy::ui.partials.form-field name="type" label="Type">
    <x-daisy::ui.inputs.select name="type" :value="request('type')">
        <option value="">Tous</option>
        <option value="premium">Premium</option>
        <option value="standard">Standard</option>
        <option value="basic">Basique</option>
    </x-daisy::ui.inputs.select>
</x-daisy::ui.partials.form-field>

