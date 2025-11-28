<div class="space-y-4">
    <x-daisy::ui.partials.form-field name="category" label="Catégorie">
        <x-daisy::ui.inputs.select name="category" :value="request('category')">
            <option value="">Toutes</option>
            <option value="tech">Technologie</option>
            <option value="design">Design</option>
            <option value="marketing">Marketing</option>
            <option value="sales">Ventes</option>
        </x-daisy::ui.inputs.select>
    </x-daisy::ui.partials.form-field>
    <x-daisy::ui.partials.form-field name="date_from" label="Date de début">
        <x-daisy::ui.inputs.input name="date_from" type="date" :value="request('date_from')" />
    </x-daisy::ui.partials.form-field>
    <x-daisy::ui.partials.form-field name="date_to" label="Date de fin">
        <x-daisy::ui.inputs.input name="date_to" type="date" :value="request('date_to')" />
    </x-daisy::ui.partials.form-field>
    <x-daisy::ui.partials.form-field name="min_price" label="Prix minimum">
        <x-daisy::ui.inputs.input name="min_price" type="number" placeholder="0" :value="request('min_price')" />
    </x-daisy::ui.partials.form-field>
    <x-daisy::ui.partials.form-field name="max_price" label="Prix maximum">
        <x-daisy::ui.inputs.input name="max_price" type="number" placeholder="1000" :value="request('max_price')" />
    </x-daisy::ui.partials.form-field>
</div>

