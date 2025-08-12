<!-- Transfer -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Transfer</h2>
    <p class="opacity-70">Transférer des éléments entre deux listes.</p>

    <div class="grid md:grid-cols-2 gap-8 items-start">
        <div class="space-y-4 border border-base-300 rounded-box p-6 bg-base-50">
            <h3 class="font-semibold text-primary">Basique</h3>
            <x-daisy::ui.transfer :source="[
                ['data' => 'Lorem ipsum'],
                ['data' => 'Something special'],
                ['data' => 'John Wick'],
                ['data' => 'Poland'],
                ['data' => 'Germany'],
                ['data' => 'USA'],
                ['data' => 'China'],
                ['data' => 'Madagascar'],
                ['data' => 'Argentina'],
            ]" />
        </div>

        <div class="space-y-4 border border-base-300 rounded-box p-6 bg-base-50">
            <h3 class="font-semibold text-primary">Disabled items</h3>
            <x-daisy::ui.transfer :source="[
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
            ]" />
        </div>

        <div class="space-y-4 border border-base-300 rounded-box p-6 bg-base-50">
            <h3 class="font-semibold text-primary">Checked items</h3>
            <x-daisy::ui.transfer :source="[
                ['data' => 'Lorem ipsum', 'checked' => true],
                ['data' => 'Something special', 'checked' => true],
                ['data' => 'John Wick', 'checked' => true],
                ['data' => 'Poland'],
                ['data' => 'Germany'],
                ['data' => 'USA', 'checked' => true],
                ['data' => 'China'],
                ['data' => 'Madagascar'],
                ['data' => 'Argentina'],
            ]" :target="[
                ['data' => 'Russia', 'checked' => true],
                ['data' => 'Australia', 'checked' => true],
                ['data' => 'Hungary'],
                ['data' => 'France'],
            ]" />
        </div>

        <div class="space-y-4 border border-base-300 rounded-box p-6 bg-base-50">
            <h3 class="font-semibold text-primary">One way</h3>
            <x-daisy::ui.transfer :source="[
                ['data' => 'Lorem ipsum', 'checked' => true],
                ['data' => 'Something special', 'checked' => true],
                ['data' => 'John Wick', 'checked' => true],
                ['data' => 'Poland'],
                ['data' => 'Germany'],
                ['data' => 'USA', 'checked' => true],
                ['data' => 'China'],
                ['data' => 'Madagascar'],
                ['data' => 'Argentina'],
            ]" :target="[
                ['data' => 'Russia', 'checked' => true],
                ['data' => 'Australia', 'checked' => true],
                ['data' => 'Hungary'],
                ['data' => 'France'],
            ]" :oneWay="true" />
        </div>

        <div class="space-y-4 border border-base-300 rounded-box p-6 bg-base-50">
            <h3 class="font-semibold text-primary">Pagination</h3>
            <x-daisy::ui.transfer :source="[
                ['data' => 'Lorem ipsum', 'checked' => true],
                ['data' => 'Something special', 'checked' => true],
                ['data' => 'John Wick', 'checked' => true],
                ['data' => 'Poland'],
                ['data' => 'Germany', 'disabled' => true],
                ['data' => 'USA', 'checked' => true],
                ['data' => 'China'],
                ['data' => 'Madagascar'],
                ['data' => 'Argentina'],
            ]" :target="[
                ['data' => 'Russia', 'checked' => true],
                ['data' => 'Australia', 'checked' => true],
                ['data' => 'Hungary'],
                ['data' => 'France'],
            ]" :pagination="true" />
        </div>

        <div class="space-y-4 border border-base-300 rounded-box p-6 bg-base-50">
            <h3 class="font-semibold text-primary">Pagination custom</h3>
            <x-daisy::ui.transfer :source="[
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
            ]" :pagination="true" :elementsPerPage="7" />
        </div>

        <div class="space-y-4 border border-base-300 rounded-box p-6 bg-base-50">
            <h3 class="font-semibold text-primary">Search</h3>
            <x-daisy::ui.transfer :source="[
                ['data' => 'Lorem ipsum', 'checked' => true],
                ['data' => 'Something special', 'checked' => true],
                ['data' => 'John Wick', 'checked' => true],
                ['data' => 'Poland'],
                ['data' => 'Germany', 'disabled' => true],
                ['data' => 'USA', 'checked' => true],
                ['data' => 'China'],
                ['data' => 'Madagascar'],
                ['data' => 'Argentina'],
            ]" :target="[
                ['data' => 'Russia', 'checked' => true],
                ['data' => 'Australia', 'checked' => true],
                ['data' => 'Hungary'],
                ['data' => 'France'],
            ]" :pagination="true" :search="true" />
        </div>

        <div class="space-y-4 border border-base-300 rounded-box p-6 bg-base-50">
            <h3 class="font-semibold text-primary">Textes personnalisés - Utilisateurs</h3>
            <x-daisy::ui.transfer 
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
                :search="true" />
        </div>

        <div class="space-y-4 border border-base-300 rounded-box p-6 bg-base-50">
            <h3 class="font-semibold text-primary">Textes personnalisés - Permissions</h3>
            <x-daisy::ui.transfer 
                titleSource="Permissions disponibles"
                titleTarget="Permissions accordées"
                selectAllTextSource="Toutes les permissions"
                selectAllTextTarget="Toutes accordées"
                searchPlaceholderSource="Chercher une permission..."
                searchPlaceholderTarget="Filtrer les permissions accordées..."
                :oneWay="true"
                :source="[
                    ['data' => 'Lire les articles'],
                    ['data' => 'Écrire des articles'],
                    ['data' => 'Modifier les articles'],
                    ['data' => 'Supprimer les articles'],
                    ['data' => 'Gérer les utilisateurs'],
                    ['data' => 'Gérer les rôles'],
                ]" 
                :target="[
                    ['data' => 'Lire les articles', 'checked' => true],
                    ['data' => 'Écrire des articles', 'checked' => true],
                ]" 
                :search="true" />
        </div>
    </div>
</section>


