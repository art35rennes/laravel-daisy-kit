<x-daisy::layout.sidebar-layout title="Sidebar Template" variant="fit" brand="DaisyKit" brandHref="#" :showBrand="true" :sections="[
    ['label' => __('daisy::layout.menu'), 'items' => [
        ['label' => 'Dashboard', 'href' => '#', 'icon' => 'speedometer2', 'active' => true],
        ['label' => 'Projects', 'icon' => 'folder', 'children' => [
            ['label' => 'Alpha', 'href' => '#', 'icon' => 'alphabet'],
            ['label' => 'Beta', 'href' => '#', 'icon' => 'flask'],
        ]],
        ['label' => 'Billing', 'href' => '#', 'icon' => 'credit-card'],
    ]],
]">
    <div class="prose">
        <h2>Contenu</h2>
        <p>La sidebar devient un burger sur mobile via le bouton en haut à gauche.</p>
    </div>
</x-daisy::layout.sidebar-layout>


