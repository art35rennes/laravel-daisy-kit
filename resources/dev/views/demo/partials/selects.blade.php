<!-- Selects -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Selects</h2>
    <div class="space-y-3">
        <div class="grid md:grid-cols-3 gap-3">
            <x-daisy::ui.select variant="bordered">
                <option value="">Select…</option>
                <option>France</option>
                <option>Belgium</option>
                <option>Canada</option>
            </x-daisy::ui.select>
            <x-daisy::ui.select variant="ghost">
                <option>Ghost</option>
            </x-daisy::ui.select>
            <x-daisy::ui.select disabled>
                <option>Disabled</option>
            </x-daisy::ui.select>
        </div>
        <div class="grid grid-cols-3 gap-3">
            <x-daisy::ui.select size="sm">
                <option>Small</option>
            </x-daisy::ui.select>
            <x-daisy::ui.select>
                <option>Medium</option>
            </x-daisy::ui.select>
            <x-daisy::ui.select size="lg">
                <option>Large</option>
            </x-daisy::ui.select>
        </div>
    </div>
</section>


