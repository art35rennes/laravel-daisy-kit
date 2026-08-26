    <x-daisy::ui.navigation.sidebar expanded-width="w-72" collapsed-width="w-16" collapsed :sections="[['items' => [['label' => 'Home', 'href' => '/home', 'icon' => 'house']]]]">
        <x-slot:brand>
            <span data-expanded-brand>Expanded brand</span>
        </x-slot:brand>
        <x-slot:brandCollapsed>
            <span data-collapsed-brand>DK</span>
        </x-slot:brandCollapsed>
    </x-daisy::ui.navigation.sidebar>