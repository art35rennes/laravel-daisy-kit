<!-- Card -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Card</h2>
    <div class="grid md:grid-cols-3 gap-6">
        <x-daisy::ui.card title="Titre" color="base-100" size="sm">
            Contenu simple
            <x-slot:actions>
                <x-daisy::ui.button size="sm">Action</x-daisy::ui.button>
            </x-slot:actions>
        </x-daisy::ui.card>
        <x-daisy::ui.card title="Bordered" :bordered="true" :dash="true">
            Carte avec bordure
        </x-daisy::ui.card>
        <x-daisy::ui.card title="Compact" :compact="true" size="lg">
            Moins d'espacement
        </x-daisy::ui.card>
    </div>
    <div class="grid md:grid-cols-2 gap-6">
        <x-daisy::ui.card :side="true" title="Side" imageAlt="Exemple image">
            <x-slot:figure>
                <img src="https://picsum.photos/seed/picsum/200/200" alt="" />
            </x-slot:figure>
            Carte avec image latérale
        </x-daisy::ui.card>
        <x-daisy::ui.card :imageFull="true" title="Image Full" imageUrl="https://picsum.photos/seed/daisy/600/300" imageAlt="Image de démonstration">
            Texte sur image full
        </x-daisy::ui.card>
    </div>
</section>


