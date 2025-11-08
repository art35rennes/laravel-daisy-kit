<!-- Transfer -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Transfer</h2>
    <p class="opacity-70">Transférer des éléments entre deux listes.</p>

    <div class="grid md:grid-cols-2 gap-8 items-start">
        <div class="space-y-4 border border-base-300 rounded-box p-6 bg-base-50">
            <h3 class="font-semibold text-primary">Basique</h3>
            <x-daisy::ui.advanced.transfer :source="[
                ['data' => 'Lorem ipsum'],
                ['data' => 'Something special'],
                ['data' => 'John Wick'],
                ['data' => 'Poland'],
                ['data' => 'Germany'],
                ['data' => 'USA'],
                ['data' => 'China'],
                ['data' => 'Madagascar'],
                ['data' => 'Argentina'],
            ]" buttonsSize="sm" />
        </div>

        <div class="space-y-4 border border-base-300 rounded-box p-6 bg-base-50">
            <h3 class="font-semibold text-primary">Disabled items</h3>
            <x-daisy::ui.advanced.transfer :source="[
                ['data' => 'Lorem ipsum'],
                ['data' => 'Something special', 'disabled' => true],
                ['data' => 'John Wick', 'disabled' => true],
                ['data' => 'Poland'],
                ['data' => 'Germany'],
                ['data' => 'USA'],
                ['data' => 'China'],
                ['data' => 'Madagascar', 'disabled' => true],
                ['data' => 'Argentina'],
            ]" :target="[
                ['data' => 'Russia', 'disabled' => true],
                ['data' => 'Australia'],
                ['data' => 'Hungary', 'disabled' => true],
                ['data' => 'France'],
            ]" buttonsSize="sm" />
        </div>

        <div class="space-y-4 border border-base-300 rounded-box p-6 bg-base-50">
            <h3 class="font-semibold text-primary">One way avec pagination</h3>
            <x-daisy::ui.advanced.transfer :source="[
                ['data' => 'Lorem ipsum', 'checked' => true],
                ['data' => 'Something special', 'checked' => true],
                ['data' => 'John Wick', 'checked' => true],
                ['data' => 'Poland'],
                ['data' => 'Germany', 'disabled' => true],
                ['data' => 'USA', 'checked' => true],
                ['data' => 'China'],
                ['data' => 'Madagascar'],
                ['data' => 'Argentina'],
                ['data' => 'Spain'],
                ['data' => 'Italy'],
                ['data' => 'Portugal'],
            ]" :target="[
                ['data' => 'Russia', 'checked' => true],
                ['data' => 'Australia', 'checked' => true],
                ['data' => 'Hungary'],
                ['data' => 'France'],
            ]" :oneWay="true" :pagination="true" :elementsPerPage="7" buttonsSize="sm" />
        </div>

        <div class="space-y-4 border border-base-300 rounded-box p-6 bg-base-50">
            <h3 class="font-semibold text-primary">Recherche avec textes personnalisés</h3>
            <x-daisy::ui.advanced.transfer 
                titleSource="Utilisateurs disponibles"
                titleTarget="Utilisateurs sélectionnés"
                selectAllTextSource="Tous les utilisateurs"
                selectAllTextTarget="Tous sélectionnés"
                searchPlaceholderSource="Rechercher un utilisateur..."
                searchPlaceholderTarget="Filtrer les sélectionnés..."
                toTargetButtonText="Ajouter →"
                toSourceButtonText="← Retirer"
                :source="[
                    ['data' => 'Alice Martin'],
                    ['data' => 'Bob Dupont'],
                    ['data' => 'Claire Bernard'],
                    ['data' => 'David Laurent'],
                    ['data' => 'Emma Rousseau'],
                ]" 
                :target="[
                    ['data' => 'François Moreau'],
                    ['data' => 'Sophie Blanc'],
                ]" 
                :search="true" 
                buttonsSize="sm" />
        </div>
    </div>
</section>
