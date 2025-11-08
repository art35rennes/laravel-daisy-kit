<!-- Calendar -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Calendar</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Cally (via npm) : date simple -->
        <div class="bg-base-100 border border-base-300 shadow-lg rounded-box p-4">
            <div class="text-sm font-medium opacity-70 mb-2">Cally - Date (1 mois)</div>
            <x-daisy::ui.advanced.calendar provider="cally" mode="date" class="cally" />
        </div>

        <!-- Cally : range + 2 mois -->
        <div class="bg-base-100 border border-base-300 shadow-lg rounded-box p-4">
            <div class="text-sm font-medium opacity-70 mb-2">Cally - Range (2 mois)</div>
            <x-daisy::ui.advanced.calendar provider="cally" mode="range" :months="2" class="cally" />
        </div>

        <!-- Cally : multi + 2 mois + locale FR -->
        <div class="bg-base-100 border border-base-300 shadow-lg rounded-box p-4">
            <div class="text-sm font-medium opacity-70 mb-2">Cally - Multi (2 mois, fr-FR)</div>
            <x-daisy::ui.advanced.calendar provider="cally" mode="multi" :months="2" locale="fr-FR" class="cally" />
        </div>

        <!-- Native input type=date -->
        <div class="bg-base-100 border border-base-300 shadow-lg rounded-box p-4">
            <div class="text-sm font-medium opacity-70 mb-2">Input natif - type="date"</div>
            <x-daisy::ui.advanced.calendar provider="native" value="" class="w-56" />
        </div>
    </div>
</section>


