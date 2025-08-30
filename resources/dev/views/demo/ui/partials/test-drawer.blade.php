<!-- Drawer -->
<section class="space-y-4 bg-base-200 p-4 rounded-box">
    <h2 class="text-lg font-medium">Drawer</h2>
    <div class="space-y-4">
        <!-- Basique -->
        <x-daisy::ui.fieldset legend="Basique" :bordered="true" padding="p-3">
            <x-daisy::ui.drawer id="demo-drawer" :fullHeight="false">
                <x-slot:content>
                    <label for="demo-drawer" class="btn btn-primary btn-sm">Open drawer</label>
                </x-slot:content>
                <x-slot:side>
                    <li><a>Sidebar item 1</a></li>
                    <li><a>Sidebar item 2</a></li>
                </x-slot:side>
            </x-daisy::ui.drawer>
        </x-daisy::ui.fieldset>

        <!-- Drawer end (droite) -->
        <x-daisy::ui.fieldset legend="Droite (drawer-end)" :bordered="true" padding="p-3">
            <x-daisy::ui.drawer id="demo-drawer-end" :end="true" :fullHeight="false">
                <x-slot:content>
                    <label for="demo-drawer-end" class="btn btn-sm">Open right drawer</label>
                </x-slot:content>
                <x-slot:side>
                    <li><a>Right item 1</a></li>
                    <li><a>Right item 2</a></li>
                </x-slot:side>
            </x-daisy::ui.drawer>
        </x-daisy::ui.fieldset>

        <!-- Drawer open sur breakpoint (sidebar visible en lg) -->
        <x-daisy::ui.fieldset legend="Ouvert à partir de lg" :bordered="true" padding="p-3">
            <x-daisy::ui.drawer id="demo-drawer-lg" responsiveOpen="lg" :fullHeight="false" contentClass="border border-base-300 rounded-box">
                <x-slot:content>
                    <div class="bg-base-300 w-full p-2 flex items-center justify-between rounded-box">
                        <div class="font-semibold text-sm">Responsive drawer</div>
                        <label for="demo-drawer-lg" aria-label="open sidebar" class="btn btn-square btn-ghost btn-xs lg:hidden">
                            <x-bi-list class="h-4 w-4" />
                        </label>
                    </div>
                    <div class="p-3 space-y-1 text-sm">
                        <p>En grand écran, la sidebar reste ouverte.</p>
                        <p>Réduisez la fenêtre pour voir le bouton d'ouverture.</p>
                    </div>
                </x-slot:content>
                <x-slot:side>
                    <li><a>Sidebar Item 1</a></li>
                    <li><a>Sidebar Item 2</a></li>
                </x-slot:side>
            </x-daisy::ui.drawer>
        </x-daisy::ui.fieldset>
    </div>
</section>


