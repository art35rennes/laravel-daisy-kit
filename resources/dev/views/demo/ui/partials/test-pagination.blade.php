<!-- Pagination -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Pagination</h2>
    <div class="space-y-6">
        <!-- Basique avec couleur -->
        <x-daisy::ui.navigation.pagination :total="7" :current="3" color="primary" />

        <!-- Petite taille + fenêtre réduite + outline -->
        <x-daisy::ui.navigation.pagination :total="12" :current="6" size="sm" :maxButtons="5" color="secondary" :outline="true" />

        <!-- Contrôles Previous/Next égaux + outline -->
        <x-daisy::ui.navigation.pagination :equalPrevNext="true" :outlinePrevNext="true" color="accent" prevLabel="Previous" nextLabel="Next" />

        <!-- XL, neutre, avec extrémités masquées -->
        <x-daisy::ui.navigation.pagination :total="15" :current="10" size="lg" :edges="false" color="neutral" />

        <!-- Responsive: sur mobile affiche "Page X/Y", sur ≥ sm affiche les numéros -->
        <div class="space-y-2">
            <p class="text-sm opacity-70">Responsive</p>
            <x-daisy::ui.navigation.pagination :total="24" :current="9" size="sm" color="primary" :maxButtons="5" :responsive="true" mobileLabel="Page :current/:total" />
        </div>
    </div>
</section>


