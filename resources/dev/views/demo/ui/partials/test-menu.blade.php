<!-- Menu -->
<section class="space-y-6 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Menu</h2>
    <div class="space-y-6">
        <div class="grid md:grid-cols-2 gap-6">
            <x-daisy::ui.navigation.menu class="w-56" title="Menu">
                <li><a class="font-semibold">Item 1</a></li>
                <li>
                    <details>
                        <summary>Parent</summary>
                        <ul class="menu-dropdown">
                            <li><a>Submenu 1</a></li>
                            <li><a>Submenu 2</a></li>
                        </ul>
                    </details>
                </li>
                <li class="menu-disabled"><a>Disabled</a></li>
                <li><a class="menu-active">Active</a></li>
                <li><a>Item 3</a></li>
            </x-daisy::ui.navigation.menu>
            <x-daisy::ui.navigation.menu :vertical="false" class="bg-base-100 rounded-box">
                <li><a>Accueil</a></li>
                <li><a>Docs</a></li>
                <li><a>Contact</a></li>
            </x-daisy::ui.navigation.menu>
        </div>
        <x-daisy::ui.navigation.menu :vertical="true" horizontalAt="lg" class="bg-base-100 rounded-box">
            <li><a>Item 1</a></li>
            <li><a>Item 2</a></li>
            <li><a>Item 3</a></li>
        </x-daisy::ui.navigation.menu>
    </div>
</section>


