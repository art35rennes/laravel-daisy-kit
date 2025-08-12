<!-- Filter -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Filter</h2>
    <div class="space-y-4">
        <!-- Avec form + bouton reset -->
        <x-daisy::ui.filter useForm="true" name="frameworks" :items="[
            ['label' => 'Svelte', 'checked' => true],
            'Vue',
            'React'
        ]" />

        <!-- Sans form + radio reset -->
        <x-daisy::ui.filter :useForm="false" name="metaframeworks" allLabel="All" :items="[
            'Sveltekit', 'Nuxt', 'Next.js'
        ]" />
    </div>
</section>


