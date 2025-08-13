<x-daisy::layout.sidebar-layout title="Sidebar Template" variant="wide" brand="DaisyKit" brandHref="#" :sections="[
    ['label' => __('daisy::layout.menu'), 'items' => [
        ['label' => 'Dashboard', 'href' => '#', 'icon' => 'house', 'active' => true],
        ['label' => 'Projects', 'icon' => 'folder', 'children' => [
            ['label' => 'Alpha', 'href' => '#', 'icon' => 'alphabet'],
            ['label' => 'Beta', 'href' => '#', 'icon' => 'beaker'],
        ]],
        ['label' => 'Billing', 'href' => '#', 'icon' => 'credit-card'],
    ]],
]">
    <div class="prose">
        <h2>Contenu</h2>
        <p>La sidebar devient un burger sur mobile via le bouton en haut à gauche.</p>
    </div>
</x-daisy::layout.sidebar-layout>


