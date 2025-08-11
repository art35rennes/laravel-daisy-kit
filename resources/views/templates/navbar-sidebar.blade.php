<x-daisy::layout.navbar-sidebar-layout title="Navbar + Sidebar Template" brand="DaisyKit" :sections="[
    ['label' => __('daisy::layout.menu'), 'items' => [
        ['label' => 'Dashboard', 'href' => '#', 'icon' => 'home', 'active' => true],
        ['label' => 'Management', 'icon' => 'cog-6-tooth', 'children' => [
            ['label' => 'Users', 'href' => '#', 'icon' => 'user-circle'],
            ['label' => 'Roles', 'href' => '#', 'icon' => 'key'],
        ]],
        ['label' => 'Reports', 'href' => '#', 'icon' => 'chart-bar'],
    ]],
]">
    <x-slot:brand>
        <a class="btn btn-ghost text-xl">App</a>
    </x-slot:brand>
    <x-slot:nav>
        <x-daisy::ui.menu :vertical="false" class="px-1">
            <li><a>Overview</a></li>
            <li><a>Teams</a></li>
            <li><a>Docs</a></li>
        </x-daisy::ui.menu>
    </x-slot:nav>
    <x-slot:actions>
        <button class="btn">{{ __('daisy::layout.profile') }}</button>
    </x-slot:actions>

    

    <div class="grid gap-4 md:grid-cols-2">
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="card-title">Card</div>
                <p>Contenu principal.</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="card-title">Card</div>
                <p>Contenu principal.</p>
            </div>
        </div>
    </div>
</x-daisy::layout.navbar-sidebar-layout>


