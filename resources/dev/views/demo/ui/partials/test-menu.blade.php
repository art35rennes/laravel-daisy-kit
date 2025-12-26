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
                        <ul>
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
        
        <div>
            <h3 class="text-sm font-semibold mb-2">Menu avec sous-menus imbriqués</h3>
            <x-daisy::ui.navigation.menu class="bg-base-100 rounded-box w-56">
                <li><a>Item 1</a></li>
                <li>
                    <details open>
                        <summary>Parent item</summary>
                        <ul>
                            <li><a>Submenu 1</a></li>
                            <li><a>Submenu 2</a></li>
                            <li>
                                <details>
                                    <summary>Nested parent</summary>
                                    <ul>
                                        <li><a>Nested item 1</a></li>
                                        <li><a>Nested item 2</a></li>
                                    </ul>
                                </details>
                            </li>
                        </ul>
                    </details>
                </li>
                <li><a>Item 3</a></li>
            </x-daisy::ui.navigation.menu>
        </div>

        <div>
            <h3 class="text-sm font-semibold mb-2">Menu avec icônes</h3>
            <x-daisy::ui.navigation.menu class="bg-base-100 rounded-box w-56">
                <li>
                    <a>
                        <x-daisy::ui.advanced.icon name="house" size="sm" />
                        Accueil
                    </a>
                </li>
                <li>
                    <a>
                        <x-daisy::ui.advanced.icon name="file-text" size="sm" />
                        Documents
                    </a>
                </li>
                <li>
                    <a>
                        <x-daisy::ui.advanced.icon name="gear" size="sm" />
                        Paramètres
                    </a>
                </li>
            </x-daisy::ui.navigation.menu>
        </div>

        <div>
            <h3 class="text-sm font-semibold mb-2">Menu avec filtrage</h3>
            <x-daisy::ui.navigation.menu filterable filterPlaceholder="Rechercher..." class="bg-base-100 rounded-box w-56">
                <li><a>Accueil</a></li>
                <li>
                    <details>
                        <summary>Navigation</summary>
                        <ul>
                            <li><a>Page 1</a></li>
                            <li><a>Page 2</a></li>
                            <li><a>Page 3</a></li>
                        </ul>
                    </details>
                </li>
                <li>
                    <details>
                        <summary>Paramètres</summary>
                        <ul>
                            <li><a>Général</a></li>
                            <li><a>Sécurité</a></li>
                            <li><a>Notifications</a></li>
                        </ul>
                    </details>
                </li>
                <li><a>Contact</a></li>
            </x-daisy::ui.navigation.menu>
        </div>
    </div>
</section>


