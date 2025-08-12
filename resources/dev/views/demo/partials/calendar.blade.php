<!-- Calendar -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Calendar</h2>
    <div class="space-y-6">
        <!-- Cally (via npm) : date simple -->
        <div class="bg-base-100 border border-base-300 shadow-lg rounded-box p-4">
            <x-daisy::ui.calendar provider="cally" mode="date" class="cally" />
        </div>

        <!-- Cally : range + 2 mois -->
        <div class="bg-base-100 border border-base-300 shadow-lg rounded-box p-4">
            <x-daisy::ui.calendar provider="cally" mode="range" :months="2" class="cally" />
        </div>

        <!-- Native input type=date -->
        <x-daisy::ui.calendar provider="native" value="" class="w-56" />
    </div>
</section>


