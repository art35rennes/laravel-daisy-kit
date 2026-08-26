    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
        <x-daisy::ui.partials.form-field
            name="filter[query]"
            id="dashboard-filter-query"
            label="Libelle de recherche volontairement tres long pour verifier la troncature"
        >
            <x-daisy::ui.inputs.input
                id="dashboard-filter-query"
                name="filter[query]"
            />
        </x-daisy::ui.partials.form-field>

        <x-daisy::ui.partials.form-field
            name="filter[intervention_type]"
            id="dashboard-filter-intervention-type"
            label="Type d'enquete tres detaille"
        >
            <x-daisy::ui.inputs.select
                id="dashboard-filter-intervention-type"
                name="filter[intervention_type]"
            >
                <option value="long">Option select avec un libelle tres long qui doit rester dans le champ</option>
            </x-daisy::ui.inputs.select>
        </x-daisy::ui.partials.form-field>

        <x-daisy::ui.partials.form-field
            name="filter[started_on]"
            id="dashboard-filter-started-on"
            label="Date de debut"
        >
            <x-daisy::ui.inputs.input
                type="date"
                id="dashboard-filter-started-on"
                name="filter[started_on]"
            />
        </x-daisy::ui.partials.form-field>
    </div>