<!-- Checkbox -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Checkbox</h2>
    <div class="flex flex-wrap items-center gap-4">
        <label class="flex items-center gap-2">
            <x-daisy::ui.checkbox />
            <span>Default</span>
        </label>
        <label class="flex items-center gap-2">
            <x-daisy::ui.checkbox color="primary" :checked="true" />
            <span>Primary checked</span>
        </label>
        <label class="flex items-center gap-2 opacity-70">
            <x-daisy::ui.checkbox :disabled="true" />
            <span>Disabled</span>
        </label>
        <x-daisy::ui.checkbox size="xs" />
        <x-daisy::ui.checkbox size="sm" />
        <x-daisy::ui.checkbox size="md" />
        <x-daisy::ui.checkbox size="lg" />
        <x-daisy::ui.checkbox size="xl" />

        <!-- Indeterminate via JS -->
        <div class="flex items-center gap-2">
            <x-daisy::ui.checkbox id="demo-indeterminate" :indeterminate="true" />
            <span>Indeterminate (JS)</span>
        </div>
    </div>
</section>


