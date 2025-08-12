<!-- Popconfirm -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Popconfirm</h2>
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Inline basique -->
        <div class="space-y-3">
            <x-daisy::ui.popconfirm message="Cette action est irréversible. Continuer ?">
                <x-slot:trigger>
                    <x-daisy::ui.button color="warning">Supprimer (inline)</x-daisy::ui.button>
                </x-slot:trigger>
            </x-daisy::ui.popconfirm>
        </div>

        <!-- Inline avec position & icône -->
        <div class="flex flex-wrap items-center gap-3">
            <x-daisy::ui.popconfirm position="top" message="Confirmer ?">
                <x-slot:icon>
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-warning" />
                </x-slot:icon>
                <x-slot:trigger>
                    <x-daisy::ui.button>Top</x-daisy::ui.button>
                </x-slot:trigger>
            </x-daisy::ui.popconfirm>

            <x-daisy::ui.popconfirm position="right" message="Confirmer ?" okText="Oui" cancelText="Non">
                <x-slot:trigger>
                    <x-daisy::ui.button>Right</x-daisy::ui.button>
                </x-slot:trigger>
            </x-daisy::ui.popconfirm>

            <x-daisy::ui.popconfirm position="left" message="Valider cette opération ?" :okClass="'btn-success'" :cancelClass="'btn-ghost'">
                <x-slot:trigger>
                    <x-daisy::ui.button>Left</x-daisy::ui.button>
                </x-slot:trigger>
            </x-daisy::ui.popconfirm>
        </div>

        <!-- Modal mode -->
        <div class="space-y-3">
            <x-daisy::ui.popconfirm mode="modal" id="demo-popconfirm-modal" title="Confirmation" message="Voulez-vous enregistrer ces modifications ?" okText="Enregistrer" cancelText="Annuler">
                <x-slot:trigger>
                    <x-daisy::ui.button color="primary">Ouvrir (modal)</x-daisy::ui.button>
                </x-slot:trigger>
            </x-daisy::ui.popconfirm>
            <div class="text-sm opacity-70">Écoute des événements:</div>
            <div class="text-xs opacity-70">Dans votre JS, écoutez <code>popconfirm:confirm</code> et <code>popconfirm:cancel</code> sur l'élément.</div>
        </div>

        <!-- Variantes de boutons -->
        <div class="space-y-3">
            <x-daisy::ui.popconfirm message="Appliquer les changements ?" okText="Appliquer" cancelText="Annuler" :okClass="'btn-success'" :cancelClass="'btn-outline'">
                <x-slot:trigger>
                    <x-daisy::ui.button variant="outline" color="success">Appliquer</x-daisy::ui.button>
                </x-slot:trigger>
            </x-daisy::ui.popconfirm>
        </div>
    </div>
</section>


