<!-- Color Picker -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Color Picker</h2>
    <div class="grid md:grid-cols-3 gap-6">
        <!-- Native -->
        <div class="space-y-2">
            <div class="text-sm opacity-70">Natif</div>
            <x-daisy::ui.color-picker mode="native" value="#563d7c" />
        </div>

        <!-- Avancé (inline) -->
        <div class="space-y-2">
            <div class="text-sm opacity-70">Avancé (inline)</div>
            <x-daisy::ui.color-picker 
                mode="advanced" 
                value="#457b9d"
                :dropdown="false"
                :swatches="[
                    ['#000000','#ffffff','#ef4444','#f59e0b','#10b981','#3b82f6','#8b5cf6'],
                    ['#111827','#4b5563','#9ca3af','#d1d5db','#e5e7eb','#f3f4f6','#fafafa']
                ]"
                swatchesHeight="96"
            />
        </div>

        <!-- Avancé (dropdown) -->
        <div class="space-y-2">
            <div class="text-sm opacity-70">Avancé (dropdown)</div>
            <x-daisy::ui.color-picker 
                mode="advanced" 
                value="#22d3ee" 
                :dropdown="true"
                :swatches="[
                    ['#000000','#ffffff','#ef4444','#f59e0b','#10b981','#3b82f6','#8b5cf6'],
                    ['#111827','#4b5563','#9ca3af','#d1d5db','#e5e7eb','#f3f4f6','#fafafa']
                ]"
                swatchesHeight="96"
            />
        </div>
    </div>
</section>
