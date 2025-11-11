<!-- Steps -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Steps</h2>
    <div class="space-y-6">
        <!-- Horizontal de base (complétées en primary) -->
        <x-daisy::ui.navigation.steps :items="['Préparation','Commande','Livraison','Fini']" :current="2" />

        <!-- Vertical forcé -->
        <x-daisy::ui.navigation.steps :items="['Register','Choose plan','Purchase','Receive Product']" :current="2" :vertical="true" />

        <!-- Responsive: vertical puis horizontal en lg -->
        <x-daisy::ui.navigation.steps :items="['Étape 1','Étape 2','Étape 3','Étape 4']" :current="3" horizontalAt="lg" />

        <!-- Icônes personnalisées + couleurs par étape -->
        <x-daisy::ui.navigation.steps :current="2" :items="[
            ['label' => 'Step 1', 'icon' => 'x-circle', 'color' => 'neutral'],
            ['label' => 'Step 2', 'icon' => 'check-circle', 'color' => 'neutral'],
            ['label' => 'Step 3', 'icon' => 'heart-fill'],
        ]" />
    </div>
</section>


