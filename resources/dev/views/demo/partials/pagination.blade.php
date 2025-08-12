<!-- Pagination -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Pagination</h2>
    <div class="space-y-6">
        <!-- Basique avec couleur -->
        <x-daisy::ui.pagination :total="7" :current="3" color="primary" />

        <!-- Petite taille + fenêtre réduite + outline -->
        <x-daisy::ui.pagination :total="12" :current="6" size="sm" :maxButtons="5" color="secondary" :outline="true" />

        <!-- Contrôles Previous/Next égaux + outline -->
        <x-daisy::ui.pagination :equalPrevNext="true" :outlinePrevNext="true" color="accent" prevLabel="Previous" nextLabel="Next" />

        <!-- XL, neutre, avec extrémités masquées -->
        <x-daisy::ui.pagination :total="15" :current="10" size="lg" :edges="false" color="neutral" />
    </div>
</section>


