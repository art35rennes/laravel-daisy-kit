<!-- Diff -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Diff</h2>
    <div class="space-y-4">
        <!-- Exemple images (comme la doc) -->
        <x-daisy::ui.diff aspect="aspect-16/9" :resizable="true">
            <x-slot:before>
                <img alt="before" src="{{ Vite::asset('resources/dev/img/object/dummy-600x900-Windflower.jpg') }}" />
            </x-slot:before>
            <x-slot:after>
                <img alt="after" src="{{ Vite::asset('resources/dev/img/object/dummy-667x1000-Bottle.jpg') }}" />
            </x-slot:after>
        </x-daisy::ui.diff>

        <!-- Exemple texte (comme la doc) -->
        <x-daisy::ui.diff aspect="aspect-16/9" :resizable="true">
            <x-slot:before>
                <div class="bg-primary text-primary-content grid place-content-center text-6xl md:text-9xl font-black">DAISY</div>
            </x-slot:before>
            <x-slot:after>
                <div class="bg-base-200 grid place-content-center text-6xl md:text-9xl font-black">DAISY</div>
            </x-slot:after>
        </x-daisy::ui.diff>
    </div>
</section>


