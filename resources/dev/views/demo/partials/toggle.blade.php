<!-- Toggle -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Toggle</h2>
    <div class="flex flex-wrap items-center gap-4">
        <label class="flex items-center gap-2">
            <x-daisy::ui.toggle />
            <span>Default</span>
        </label>
        <label class="flex items-center gap-2">
            <x-daisy::ui.toggle color="primary" :checked="true" />
            <span>Primary ON</span>
        </label>
        <label class="flex items-center gap-2 opacity-70">
            <x-daisy::ui.toggle :disabled="true" />
            <span>Disabled</span>
        </label>
        <x-daisy::ui.toggle size="xs" />
        <x-daisy::ui.toggle size="sm" />
        <x-daisy::ui.toggle size="md" />
        <x-daisy::ui.toggle size="lg" />
        <x-daisy::ui.toggle size="xl" />
        <x-daisy::ui.toggle :indeterminate="true" />
    </div>
</section>


