<div class="space-y-8">
    <!-- Premier exemple - Avec minuteur -->
    <div class="card bg-base-100 card-border">
        <div class="card-body space-y-6">
            <h3 class="card-title">Onboarding - Exemple avec minuteur</h3>

            <div class="flex flex-col lg:flex-row gap-6 items-start">
                <div class="flex-shrink-0">
                    <x-daisy::ui.inputs.button id="onboarding-start-btn" class="btn-primary">Démarrer l'onboarding</x-daisy::ui.inputs.button>
                </div>

                <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4 min-w-0">
                    <x-daisy::ui.layout.card id="box-a" class="min-w-0">
                        <x-slot:header>Bloc A</x-slot:header>
                        <div class="space-y-3">
                            <x-daisy::ui.inputs.button class="btn-outline w-full">Action A</x-daisy::ui.inputs.button>
                            <p class="text-sm opacity-70 break-words">Exemple de contenu pour la cible A avec un texte plus long pour tester le wrapping</p>
                        </div>
                    </x-daisy::ui.layout.card>

                    <x-daisy::ui.layout.card id="box-b" class="min-w-0">
                        <x-slot:header>Bloc B</x-slot:header>
                        <div class="space-y-3">
                            <x-daisy::ui.inputs.button class="btn-outline w-full">Action B</x-daisy::ui.inputs.button>
                            <p class="text-sm opacity-70 break-words">Autre contenu pour la cible B avec du texte supplémentaire</p>
                        </div>
                    </x-daisy::ui.layout.card>
                </div>
            </div>

            <x-daisy::ui.advanced.onboarding id="demo-onboarding" :start="false" :steps="[
                ['target' => '#onboarding-start-btn', 'title' => 'Bienvenue', 'content' => 'Cliquez sur ce bouton pour démarrer n\'importe quand.', 'placement' => 'bottom'],
                ['target' => '#box-a', 'title' => 'Bloc A', 'content' => 'Zone A mise en avant. Navigation au rythme de l\'utilisateur.', 'placement' => 'right', 'auto' => 0],
                ['target' => '#box-b', 'title' => 'Bloc B', 'content' => 'Étape avec auto-avancement en 2 secondes.', 'placement' => 'left', 'auto' => 2000],
            ]">
                {{-- slot optionnel: on peut rendre un bouton dédié ici --}}
            </x-daisy::ui.advanced.onboarding>

            <div class="text-sm opacity-70 text-center">Astuce: flèches clavier pour naviguer, Échapper pour quitter.</div>
        </div>
    </div>

    <!-- Deuxième exemple - Sans minuteur -->
    <div class="card bg-base-100 card-border">
        <div class="card-body space-y-6">
            <h3 class="card-title">Onboarding - Exemple manuel (sans minuteur)</h3>

            <div class="flex flex-col lg:flex-row gap-6 items-start">
                <div class="flex-shrink-0 space-y-2">
                    <x-daisy::ui.inputs.button id="onboarding-manual-btn" class="btn-secondary w-full">Tour manuel</x-daisy::ui.inputs.button>
                    <div class="text-xs opacity-70 max-w-40">Navigation entièrement manuelle</div>
                </div>

                <div class="flex-1 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 min-w-0">
                        <x-daisy::ui.layout.card id="feature-1" class="min-w-0">
                            <x-slot:header>🎯 Feature 1</x-slot:header>
                            <div class="space-y-2">
                                <x-daisy::ui.inputs.button class="btn-ghost btn-sm w-full">Fonctionnalité</x-daisy::ui.inputs.button>
                                <p class="text-xs opacity-70">Description de la première fonctionnalité importante</p>
                            </div>
                        </x-daisy::ui.layout.card>

                        <x-daisy::ui.layout.card id="feature-2" class="min-w-0">
                            <x-slot:header>⚙️ Feature 2</x-slot:header>
                            <div class="space-y-2">
                                <x-daisy::ui.inputs.button class="btn-ghost btn-sm w-full">Configuration</x-daisy::ui.inputs.button>
                                <p class="text-xs opacity-70">Options de configuration avancées</p>
                            </div>
                        </x-daisy::ui.layout.card>

                        <x-daisy::ui.layout.card id="feature-3" class="min-w-0">
                            <x-slot:header>📊 Feature 3</x-slot:header>
                            <div class="space-y-2">
                                <x-daisy::ui.inputs.button class="btn-ghost btn-sm w-full">Analytiques</x-daisy::ui.inputs.button>
                                <p class="text-xs opacity-70">Tableau de bord et statistiques</p>
                            </div>
                        </x-daisy::ui.layout.card>
                    </div>

                    <div class="bg-base-200 p-4 rounded-box" id="help-section">
                        <h4 class="font-medium mb-2">💬 Centre d'aide</h4>
                        <p class="text-sm opacity-70">Section d'aide et documentation pour accompagner les utilisateurs</p>
                    </div>
                </div>
            </div>

            <x-daisy::ui.advanced.onboarding id="manual-onboarding" :start="false" :steps="[
                ['target' => '#onboarding-manual-btn', 'title' => 'Tour manuel', 'content' => 'Ce tour se fait entièrement à votre rythme. Utilisez les boutons ou les flèches du clavier.', 'placement' => 'bottom'],
                ['target' => '#feature-1', 'title' => 'Première fonctionnalité', 'content' => 'Découvrez les fonctionnalités principales de l\'application. Prenez le temps d\'explorer.', 'placement' => 'top'],
                ['target' => '#feature-2', 'title' => 'Configuration', 'content' => 'Personnalisez votre expérience avec les options de configuration disponibles.', 'placement' => 'right'],
                ['target' => '#feature-3', 'title' => 'Analytiques', 'content' => 'Suivez vos performances avec le tableau de bord intégré.', 'placement' => 'left'],
                ['target' => '#help-section', 'title' => 'Centre d\'aide', 'content' => 'N\'hésitez pas à consulter la documentation si vous avez des questions.', 'placement' => 'bottom'],
            ]">
                {{-- Deuxième onboarding sans minuteur --}}
            </x-daisy::ui.advanced.onboarding>

            <div class="text-sm opacity-70 text-center">Navigation 100% manuelle - Aucun avancement automatique</div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    function waitForOnboarding() {
        return new Promise((resolve) => {
            // Si DaisyOnboarding est déjà disponible, résoudre immédiatement
            if (window.DaisyOnboarding && window.DaisyOnboarding.initAll) {
                resolve();
                return;
            }
            
            // Sinon, attendre qu'il soit disponible (polling)
            const checkInterval = setInterval(() => {
                if (window.DaisyOnboarding && window.DaisyOnboarding.initAll) {
                    clearInterval(checkInterval);
                    resolve();
                }
            }, 50);
            
            // Timeout de sécurité (10 secondes)
            setTimeout(() => {
                clearInterval(checkInterval);
                resolve(); // Résoudre quand même pour éviter de bloquer
            }, 10000);
        });
    }
    
    async function initOnboardingDemo() {
        await waitForOnboarding();
        
        // Forcer l'initialisation si nécessaire
        if (window.DaisyOnboarding && window.DaisyOnboarding.initAll) {
            window.DaisyOnboarding.initAll();
        }
        
        // Petit délai pour laisser l'initialisation se faire
        await new Promise(resolve => setTimeout(resolve, 100));
        
        // Premier onboarding (avec minuteur)
        const btn = document.getElementById('onboarding-start-btn');
        const ob = document.getElementById('demo-onboarding');
        
        if (btn && ob) {
            btn.addEventListener('click', () => {
                ob.__onboarding?.start?.();
            });
        }

        // Deuxième onboarding (manuel)
        const manualBtn = document.getElementById('onboarding-manual-btn');
        const manualOb = document.getElementById('manual-onboarding');
        
        if (manualBtn && manualOb) {
            manualBtn.addEventListener('click', () => {
                manualOb.__onboarding?.start?.();
            });
        }
    }
    
    initOnboardingDemo();
});
</script>


