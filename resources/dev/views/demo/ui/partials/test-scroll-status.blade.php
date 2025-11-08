<!-- Scroll Status -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Scroll Status</h2>
    <p class="opacity-70">Barre de progression indiquant le niveau de scroll (conteneur ou page complète).</p>

    <div class="grid md:grid-cols-2 gap-6 items-start">
        <div>
            <h3 class="font-semibold mb-2">Local (conteneur)</h3>
            <div class="border rounded-box" style="height: 180px; overflow-y: auto;">
                <x-daisy::ui.advanced.scroll-status color="#22c55e" height="6px" :offset="0" class="top-0" />
                <div class="p-4 space-y-4">
                    @for($i=0;$i<12;$i++)
                        <p>Some content here ({{ $i+1 }})</p>
                    @endfor
                </div>
            </div>
        </div>

        <div>
            <h3 class="font-semibold mb-2">Global (page)</h3>
            <x-daisy::ui.advanced.scroll-status :global="true" color="#3b82f6" height="6px" :offset="0" class="top-0" />
            <div class="text-sm opacity-70">Faites défiler la page pour voir la barre bleue en haut.</div>
        </div>

        <div>
            <h3 class="font-semibold mb-2">Position / Hauteur</h3>
            <div class="border rounded-box" style="height: 180px; overflow-y: auto;">
                <x-daisy::ui.advanced.scroll-status color="#f59e0b" height="12px" :offset="10" class="top-2" />
                <div class="p-4 space-y-4">
                    @for($i=0;$i<12;$i++)
                        <p>Custom height & offset ({{ $i+1 }})</p>
                    @endfor
                </div>
            </div>
        </div>

        <div>
            <h3 class="font-semibold mb-2">Modal à 50% (once)</h3>
            <div class="border rounded-box" style="height: 180px; overflow-y: auto;">
                <x-daisy::ui.advanced.scroll-status color="#ef4444" height="6px" scroll="50" target="#demoScrollModalOnce" />
                <div class="p-4 space-y-4">
                    @for($i=0;$i<16;$i++)
                        <p>Scroll to open modal once ({{ $i+1 }})</p>
                    @endfor
                </div>
            </div>
            <x-daisy::ui.overlay.modal id="demoScrollModalOnce" title="Scroll Status">
                <x-slot:actions>
                    <form method="dialog">
                        <button class="btn">Close</button>
                    </form>
                </x-slot:actions>
                Showing scroll status on 50% once
            </x-daisy::ui.overlay.modal>
        </div>

        <div>
            <h3 class="font-semibold mb-2">Modal à 50% (multi)</h3>
            <div class="border rounded-box" style="height: 180px; overflow-y: auto;">
                <x-daisy::ui.advanced.scroll-status color="#06b6d4" height="6px" scroll="50" target="#demoScrollModalMulti" :openOnce="false" />
                <div class="p-4 space-y-4">
                    @for($i=0;$i<16;$i++)
                        <p>Scroll to open modal multiple ({{ $i+1 }})</p>
                    @endfor
                </div>
            </div>
            <x-daisy::ui.overlay.modal id="demoScrollModalMulti" title="Scroll Status">
                <x-slot:actions>
                    <form method="dialog">
                        <button class="btn">Close</button>
                    </form>
                </x-slot:actions>
                Showing scroll status on 50% multiple
            </x-daisy::ui.overlay.modal>
        </div>
    </div>
</section>


