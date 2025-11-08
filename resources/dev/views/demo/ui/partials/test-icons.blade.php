<!-- Icônes (système simplifié) -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Icônes (système simplifié)</h2>
    <div class="space-y-4">
        <p class="text-sm text-base-content/70">Le composant <code>&lt;x-daisy::ui.advanced.icon&gt;</code> peut utiliser des noms simples (ajoute "bi-") ou des noms complets.</p>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div class="text-center">
                <x-daisy::ui.advanced.icon name="house" size="lg" class="mx-auto text-primary" />
                <div class="text-xs mt-2 font-mono">house</div>
            </div>
            <div class="text-center">
                <x-daisy::ui.advanced.icon name="people" size="lg" class="mx-auto text-secondary" />
                <div class="text-xs mt-2 font-mono">people</div>
            </div>
            <div class="text-center">
                <x-daisy::ui.advanced.icon name="gear" size="lg" class="mx-auto text-accent" />
                <div class="text-xs mt-2 font-mono">gear</div>
            </div>
            <div class="text-center">
                <x-daisy::ui.advanced.icon name="search" size="lg" class="mx-auto text-info" />
                <div class="text-xs mt-2 font-mono">search</div>
            </div>
            <div class="text-center">
                <x-daisy::ui.advanced.icon name="bell" size="lg" class="mx-auto text-warning" />
                <div class="text-xs mt-2 font-mono">bell</div>
            </div>
            <div class="text-center">
                <x-daisy::ui.advanced.icon name="bi-heart-fill" size="lg" class="mx-auto text-error" />
                <div class="text-xs mt-2 font-mono">bi-heart-fill</div>
            </div>
        </div>
        
        <div class="bg-base-100 p-3 rounded-lg">
            <h4 class="font-medium mb-2 text-sm">Exemples d'utilisation</h4>
            <div class="text-xs space-y-1">
                <div><code>&lt;x-daisy::ui.advanced.icon name="house" /&gt;</code> → <code>bi-house</code></div>
                <div><code>&lt;x-daisy::ui.advanced.icon name="bi-heart-fill" /&gt;</code> → <code>bi-heart-fill</code></div>
            </div>
        </div>
        
        <div class="flex items-center gap-6 flex-wrap">
            <span class="text-sm font-medium">Tailles :</span>
            <div class="flex items-center gap-2">
                <x-daisy::ui.advanced.icon name="bi-star-fill" size="xs" class="text-yellow-500" />
                <span class="text-xs">xs</span>
            </div>
            <div class="flex items-center gap-2">
                <x-daisy::ui.advanced.icon name="bi-star-fill" size="sm" class="text-yellow-500" />
                <span class="text-xs">sm</span>
            </div>
            <div class="flex items-center gap-2">
                <x-daisy::ui.advanced.icon name="bi-star-fill" size="md" class="text-yellow-500" />
                <span class="text-xs">md</span>
            </div>
            <div class="flex items-center gap-2">
                <x-daisy::ui.advanced.icon name="bi-star-fill" size="lg" class="text-yellow-500" />
                <span class="text-xs">lg</span>
            </div>
            <div class="flex items-center gap-2">
                <x-daisy::ui.advanced.icon name="bi-star-fill" size="xl" class="text-yellow-500" />
                <span class="text-xs">xl</span>
            </div>
        </div>
    </div>
</section>


