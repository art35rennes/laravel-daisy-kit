<!-- Popconfirm -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Popconfirm</h2>
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Inline basique -->
        <div class="space-y-3">
            <x-daisy::ui.overlay.popconfirm message="Cette action est irréversible. Continuer ?">
                <x-slot:trigger>
                    <x-daisy::ui.inputs.button color="warning">Supprimer (inline)</x-daisy::ui.inputs.button>
                </x-slot:trigger>
            </x-daisy::ui.overlay.popconfirm>
        </div>

        <!-- Inline avec position & icône -->
        <div class="flex flex-wrap items-center gap-3">
            <x-daisy::ui.overlay.popconfirm position="top" message="Confirmer ?">
                <x-slot:icon>
                    <x-bi-exclamation-triangle class="w-5 h-5 text-warning" />
                </x-slot:icon>
                <x-slot:trigger>
                    <x-daisy::ui.inputs.button>Top</x-daisy::ui.inputs.button>
                </x-slot:trigger>
            </x-daisy::ui.overlay.popconfirm>

            <x-daisy::ui.overlay.popconfirm position="right" message="Confirmer ?" okText="Oui" cancelText="Non">
                <x-slot:trigger>
                    <x-daisy::ui.inputs.button>Right</x-daisy::ui.inputs.button>
                </x-slot:trigger>
            </x-daisy::ui.overlay.popconfirm>

            <x-daisy::ui.overlay.popconfirm position="left" message="Valider cette opération ?" :okClass="'btn-success'" :cancelClass="'btn-ghost'">
                <x-slot:trigger>
                    <x-daisy::ui.inputs.button>Left</x-daisy::ui.inputs.button>
                </x-slot:trigger>
            </x-daisy::ui.overlay.popconfirm>
        </div>

        <!-- Modal mode -->
        <div class="space-y-3">
            <x-daisy::ui.overlay.popconfirm mode="modal" id="demo-popconfirm-modal" title="Confirmation" message="Voulez-vous enregistrer ces modifications ?" okText="Enregistrer" cancelText="Annuler">
                <x-slot:trigger>
                    <x-daisy::ui.inputs.button color="primary">Ouvrir (modal)</x-daisy::ui.inputs.button>
                </x-slot:trigger>
            </x-daisy::ui.overlay.popconfirm>
            <div class="text-sm opacity-70">Écoute des événements:</div>
            <div class="text-xs opacity-70">Dans votre JS, écoutez <code>popconfirm:confirm</code> et <code>popconfirm:cancel</code> sur l'élément.</div>
        </div>

        <!-- Variantes de boutons -->
        <div class="space-y-3">
            <x-daisy::ui.overlay.popconfirm message="Appliquer les changements ?" okText="Appliquer" cancelText="Annuler" :okClass="'btn-success'" :cancelClass="'btn-outline'">
                <x-slot:trigger>
                    <x-daisy::ui.inputs.button variant="outline" color="success">Appliquer</x-daisy::ui.inputs.button>
                </x-slot:trigger>
            </x-daisy::ui.overlay.popconfirm>
        </div>
    </div>
</section>


