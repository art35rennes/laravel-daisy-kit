<!-- Drawer -->
<section class="space-y-6 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Drawer</h2>
    <div class="space-y-6">
        <!-- Basique -->
        <x-daisy::ui.drawer id="demo-drawer">
            <x-slot:content>
                <label for="demo-drawer" class="btn btn-primary">Open drawer</label>
            </x-slot:content>
            <x-slot:side>
                <li><a>Sidebar item 1</a></li>
                <li><a>Sidebar item 2</a></li>
            </x-slot:side>
        </x-daisy::ui.drawer>

        <!-- Drawer end (droite) -->
        <x-daisy::ui.drawer id="demo-drawer-end" :end="true">
            <x-slot:content>
                <label for="demo-drawer-end" class="btn">Open right drawer</label>
            </x-slot:content>
            <x-slot:side>
                <li><a>Right item 1</a></li>
                <li><a>Right item 2</a></li>
            </x-slot:side>
        </x-daisy::ui.drawer>

        <!-- Drawer open sur breakpoint (sidebar visible en lg) -->
        <x-daisy::ui.drawer id="demo-drawer-lg" responsiveOpen="lg">
            <x-slot:content>
                <div class="bg-base-300 w-full p-3 flex items-center justify-between">
                    <div class="font-semibold">Responsive drawer</div>
                    <label for="demo-drawer-lg" aria-label="open sidebar" class="btn btn-square btn-ghost lg:hidden">
                        <x-heroicon-o-bars-3 class="h-6 w-6" />
                    </label>
                </div>
                <div class="p-4 space-y-2">
                    <p>Content area. En grand écran, la sidebar reste ouverte.</p>
                    <p>Réduisez la fenêtre pour voir le bouton d'ouverture.</p>
                </div>
            </x-slot:content>
            <x-slot:side>
                <li><a>Sidebar Item 1</a></li>
                <li><a>Sidebar Item 2</a></li>
            </x-slot:side>
        </x-daisy::ui.drawer>
    </div>
</section>


