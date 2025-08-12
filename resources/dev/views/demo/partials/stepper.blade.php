<!-- Stepper (Wizard) -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Stepper</h2>
    <div class="grid md:grid-cols-2 gap-6 items-start">
        <div class="space-y-4">
            <div class="text-sm opacity-70">Horizontal non-linéaire (clic autorisé)</div>
            <x-daisy::ui.stepper id="demoStepper1" :items="[
                ['label' => 'Compte'],
                ['label' => 'Profil'],
                ['label' => 'Confirmation'],
            ]" :current="1" :persist="true">
                <x-slot:step_1>
                    <div class="p-4 rounded-box bg-base-100 border">Contenu étape 1</div>
                </x-slot:step_1>
                <x-slot:step_2>
                    <div class="p-4 rounded-box bg-base-100 border">Contenu étape 2</div>
                </x-slot:step_2>
                <x-slot:step_3>
                    <div class="p-4 rounded-box bg-base-100 border">Contenu étape 3</div>
                </x-slot:step_3>
            </x-daisy::ui.stepper>
        </div>

        <div class="space-y-4">
            <div class="text-sm opacity-70">Vertical linéaire (étape 2 disabled)</div>
            <x-daisy::ui.stepper id="demoStepper2" :items="[
                ['label' => 'Adresse'],
                ['label' => 'Paiement (désactivé)', 'disabled' => true],
                ['label' => 'Résumé'],
            ]" :current="1" :linear="true" :persist="false" :horizontalAt="'lg'">
                <x-slot:step_1>
                    <div class="p-4 rounded-box bg-base-100 border">Adresse</div>
                </x-slot:step_1>
                <x-slot:step_2>
                    <div class="p-4 rounded-box bg-base-100 border">Paiement indisponible</div>
                </x-slot:step_2>
                <x-slot:step_3>
                    <div class="p-4 rounded-box bg-base-100 border">Résumé</div>
                </x-slot:step_3>
            </x-daisy::ui.stepper>
        </div>
    </div>
</section>


