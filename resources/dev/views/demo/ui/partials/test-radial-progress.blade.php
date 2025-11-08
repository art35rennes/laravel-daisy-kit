<!-- Radial Progress -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Radial Progress</h2>
    <div class="flex flex-wrap items-center gap-6">
        <!-- Valeurs -->
        <x-daisy::ui.data-display.radial-progress value="0">0%</x-daisy::ui.data-display.radial-progress>
        <x-daisy::ui.data-display.radial-progress value="20">20%</x-daisy::ui.data-display.radial-progress>
        <x-daisy::ui.data-display.radial-progress value="60">60%</x-daisy::ui.data-display.radial-progress>
        <x-daisy::ui.data-display.radial-progress value="80">80%</x-daisy::ui.data-display.radial-progress>
        <x-daisy::ui.data-display.radial-progress value="100">100%</x-daisy::ui.data-display.radial-progress>

        <!-- Couleur -->
        <x-daisy::ui.data-display.radial-progress value="70" color="primary">70%</x-daisy::ui.data-display.radial-progress>

        <!-- Fond + bordure -->
        <div class="radial-progress bg-primary text-primary-content border-primary border-4" style="--value:70; --size:5rem;" role="progressbar" aria-valuenow="70">70%</div>

        <!-- Taille / épaisseur -->
        <x-daisy::ui.data-display.radial-progress value="70" size="12rem" thickness="2px">70%</x-daisy::ui.data-display.radial-progress>
        <x-daisy::ui.data-display.radial-progress value="70" size="12rem" thickness="2rem">70%</x-daisy::ui.data-display.radial-progress>
    </div>
</section>


