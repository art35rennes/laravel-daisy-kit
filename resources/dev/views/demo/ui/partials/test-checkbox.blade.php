<!-- Checkbox -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Checkbox</h2>
    <div class="flex flex-wrap items-center gap-4">
        <label class="flex items-center gap-2">
            <x-daisy::ui.inputs.checkbox />
            <span>Default</span>
        </label>
        <label class="flex items-center gap-2">
            <x-daisy::ui.inputs.checkbox color="primary" :checked="true" />
            <span>Primary checked</span>
        </label>
        <label class="flex items-center gap-2 opacity-70">
            <x-daisy::ui.inputs.checkbox :disabled="true" />
            <span>Disabled</span>
        </label>
        <x-daisy::ui.inputs.checkbox size="xs" />
        <x-daisy::ui.inputs.checkbox size="sm" />
        <x-daisy::ui.inputs.checkbox size="md" />
        <x-daisy::ui.inputs.checkbox size="lg" />
        <x-daisy::ui.inputs.checkbox size="xl" />

        <!-- Indeterminate via JS -->
        <div class="flex items-center gap-2">
            <x-daisy::ui.inputs.checkbox id="demo-indeterminate" :indeterminate="true" />
            <span>Indeterminate (JS)</span>
        </div>
    </div>
</section>


