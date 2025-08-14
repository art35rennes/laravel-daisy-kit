<div class="card bg-base-100 border border-base-200">
    <div class="card-body">
        <h3 class="card-title">Onboarding</h3>

        <div class="flex gap-4 items-start">
            <x-daisy::ui.button id="onboarding-start-btn" class="btn-primary">Démarrer l'onboarding</x-daisy::ui.button>

            <div class="flex-1 grid grid-cols-2 gap-4">
                <x-daisy::ui.card id="box-a">
                    <x-slot:header>Bloc A</x-slot:header>
                    <div class="space-y-2">
                        <x-daisy::ui.button class="btn-outline">Action A</x-daisy::ui.button>
                        <p class="text-sm opacity-70">Exemple de contenu pour la cible A</p>
                    </div>
                </x-daisy::ui.card>

                <x-daisy::ui.card id="box-b">
                    <x-slot:header>Bloc B</x-slot:header>
                    <div class="space-y-2">
                        <x-daisy::ui.button class="btn-outline">Action B</x-daisy::ui.button>
                        <p class="text-sm opacity-70">Autre contenu pour la cible B</p>
                    </div>
                </x-daisy::ui.card>
            </div>
        </div>

        <x-daisy::ui.onboarding id="demo-onboarding" :start="false" :steps="[
            ['target' => '#onboarding-start-btn', 'title' => 'Bienvenue', 'content' => 'Cliquez sur ce bouton pour démarrer n\'importe quand.', 'placement' => 'bottom'],
            ['target' => '#box-a', 'title' => 'Bloc A', 'content' => 'Zone A mise en avant. Navigation au rythme de l\'utilisateur.', 'placement' => 'right', 'auto' => 0],
            ['target' => '#box-b', 'title' => 'Bloc B', 'content' => 'Étape avec auto-avancement en 2 secondes.', 'placement' => 'left', 'auto' => 2000],
        ]">
            {{-- slot optionnel: on peut rendre un bouton dédié ici --}}
        </x-daisy::ui.onboarding>

        <div class="text-sm opacity-70">Astuce: flèches clavier pour naviguer, Échapper pour quitter.</div>
    </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('onboarding-start-btn');
    const ob = document.getElementById('demo-onboarding');
    if (btn && ob) {
        btn.addEventListener('click', () => {
            ob.__onboarding?.start?.();
        });
    }
});
</script>


