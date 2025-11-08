<!-- Theme Controller -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Theme Controller</h2>
    <div class="space-y-4">
        <!-- Boutons (join) -->
        <x-daisy::ui.advanced.theme-controller class="flex flex-wrap gap-2" :themes="['light','dark','cupcake','emerald','retro','cyberpunk']" value="light" size="sm" />

        <!-- Dropdown -->
        <x-daisy::ui.advanced.theme-controller variant="dropdown" :themes="['light','dark','cupcake','emerald','retro','cyberpunk']" value="dark" label="Theme" />

        <!-- Toggle unique (synthwave) -->
        <label class="swap swap-rotate">
            <input type="checkbox" class="theme-controller" value="synthwave" />
            <x-bi-sun class="swap-off h-8 w-8" />
            <x-bi-moon class="swap-on h-8 w-8" />
        </label>
    </div>
</section>


