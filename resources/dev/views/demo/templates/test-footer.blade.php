<x-daisy::layout.app title="Footer Template" :container="false">
    <main class="container mx-auto p-6 min-h-screen">
        <div class="py-12">
            <h1 class="text-3xl font-bold mb-4">Footer Template</h1>
            <p class="text-base-content/70 mb-6">
                Template avec footer en bas de page, inclut colonnes de navigation, copyright et réseaux sociaux.
            </p>
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title">Contenu principal</h2>
                    <p>Le footer apparaît en bas de cette page avec toutes les options de personnalisation.</p>
                </div>
            </div>
        </div>
    </main>

    <x-daisy::ui.layout.footer-layout
        bg="base-200"
        text="base-content"
        padding="p-10"
        :center="false"
        :horizontal="false"
        :columns="[
            [
                'title' => 'Navigation',
                'links' => [
                    ['label' => 'Accueil', 'href' => '#'],
                    ['label' => 'À propos', 'href' => '#'],
                    ['label' => 'Contact', 'href' => '#'],
                ],
            ],
            [
                'title' => 'Support',
                'links' => [
                    ['label' => 'Documentation', 'href' => '#'],
                    ['label' => 'FAQ', 'href' => '#'],
                    ['label' => 'Support', 'href' => '#'],
                ],
            ],
        ]"
        brandText="DaisyKit"
        brandDescription="Composants Blade pour Laravel"
        :copyrightYear="date('Y')"
        copyrightText="Tous droits réservés"
        :socialLinks="[
            ['icon' => 'github', 'href' => '#', 'label' => 'GitHub'],
            ['icon' => 'twitter', 'href' => '#', 'label' => 'Twitter'],
            ['icon' => 'linkedin', 'href' => '#', 'label' => 'LinkedIn'],
        ]"
        :showDivider="true"
    />
</x-daisy::layout.app>

