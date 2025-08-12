<!-- Color Picker -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Color Picker</h2>
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Native -->
        <div class="space-y-2">
            <div class="text-sm opacity-70">Native</div>
            <x-daisy::ui.color-picker mode="native" value="#563d7c" />
        </div>

        <!-- Advanced (panel inline) -->
        <div class="space-y-2">
            <div class="text-sm opacity-70">Advanced</div>
            <x-daisy::ui.color-picker mode="advanced" value="#457b9d" />
        </div>
    </div>
</section>


