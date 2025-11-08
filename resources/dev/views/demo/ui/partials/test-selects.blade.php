<!-- Selects -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Selects</h2>
    <div class="space-y-3">
        <div class="grid md:grid-cols-3 gap-3">
            <x-daisy::ui.inputs.select variant="bordered">
                <option value="">Select…</option>
                <option>France</option>
                <option>Belgium</option>
                <option>Canada</option>
            </x-daisy::ui.inputs.select>
            <x-daisy::ui.inputs.select variant="ghost">
                <option>Ghost</option>
            </x-daisy::ui.inputs.select>
            <x-daisy::ui.inputs.select disabled>
                <option>Disabled</option>
            </x-daisy::ui.inputs.select>
        </div>
        <div class="grid grid-cols-3 gap-3">
            <x-daisy::ui.inputs.select size="sm">
                <option>Small</option>
            </x-daisy::ui.inputs.select>
            <x-daisy::ui.inputs.select>
                <option>Medium</option>
            </x-daisy::ui.inputs.select>
            <x-daisy::ui.inputs.select size="lg">
                <option>Large</option>
            </x-daisy::ui.inputs.select>
        </div>
        <div class="grid md:grid-cols-2 gap-3">
            <!-- Mode Search (filtrage local des options) -->
            <div class="space-y-2">
                <div class="label"><span class="label-text text-xs">Search (local)</span></div>
                <x-daisy::ui.inputs.select variant="bordered" data-select-search="1" :search="true">
                    <option value="">Choisir…</option>
                    <optgroup label="Europe">
                        <option value="fr">France</option>
                        <option value="de">Germany</option>
                        <option value="gr">Greece</option>
                        <option value="be">Belgium</option>
                    </optgroup>
                    <optgroup label="Amériques">
                        <option value="ca">Canada</option>
                        <option value="us">United States</option>
                        <option value="mx">Mexico</option>
                    </optgroup>
                    <optgroup label="Asie">
                        <option value="jp">Japan</option>
                        <option value="sg">Singapore</option>
                        <option value="in">India</option>
                    </optgroup>
                </x-daisy::ui.inputs.select>
            </div>
            <!-- Mode Autocomplete (requête distante) -->
            <div class="space-y-2">
                <div class="label"><span class="label-text text-xs">Autocomplete (remote)</span></div>
                <x-daisy::ui.inputs.select
                    name="demo_autocomplete"
                    variant="bordered"
                    data-select-autocomplete="1"
                    :autocomplete="true"
                    endpoint="{{ route('demo.select.options') }}"
                    param="q"
                    :min-chars="2"
                    :debounce="250"
                    :default="[
                        ['value'=>'c_john','label'=>'John Carter','subtitle'=>'john.carter@example.com','avatar'=>'/img/people/people-1.jpg'],
                        ['value'=>'c_jane','label'=>'Jane Doe','subtitle'=>'jane.doe@example.com','avatar'=>'/img/people/people-2.jpg'],
                    ]"
                >
                    <option value="">Choisir…</option>
                </x-daisy::ui.inputs.select>
            </div>
        </div>
    </div>
</section>


