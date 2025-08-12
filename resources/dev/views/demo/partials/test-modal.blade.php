<!-- Modal -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Modal</h2>
    <div class="space-y-6">
        <!-- Méthode dialog standard -->
        <div class="space-y-2">
            <x-daisy::ui.button onclick="document.getElementById('demo-modal').showModal()">Ouvrir (center)</x-daisy::ui.button>
            <x-daisy::ui.modal id="demo-modal" title="Exemple de modal">
                Contenu de la modal.
                <x-slot:actions>
                    <form method="dialog">
                        <x-daisy::ui.button>Fermer</x-daisy::ui.button>
                    </form>
                </x-slot:actions>
            </x-daisy::ui.modal>
        </div>

        <!-- Positionnements -->
        <div class="flex flex-wrap gap-2">
            <x-daisy::ui.button onclick="document.getElementById('demo-modal-top').showModal()">Top</x-daisy::ui.button>
            <x-daisy::ui.button onclick="document.getElementById('demo-modal-bottom').showModal()">Bottom</x-daisy::ui.button>
            <x-daisy::ui.button onclick="document.getElementById('demo-modal-start').showModal()">Start</x-daisy::ui.button>
            <x-daisy::ui.button onclick="document.getElementById('demo-modal-end').showModal()">End</x-daisy::ui.button>
        </div>
        <x-daisy::ui.modal id="demo-modal-top" title="Modal Top" vertical="top">
            Placée en haut
            <x-slot:actions>
                <form method="dialog">
                    <x-daisy::ui.button>Fermer</x-daisy::ui.button>
                </form>
            </x-slot:actions>
        </x-daisy::ui.modal>
        <x-daisy::ui.modal id="demo-modal-bottom" title="Modal Bottom" vertical="bottom">
            Placée en bas
            <x-slot:actions>
                <form method="dialog">
                    <x-daisy::ui.button>Fermer</x-daisy::ui.button>
                </form>
            </x-slot:actions>
        </x-daisy::ui.modal>
        <x-daisy::ui.modal id="demo-modal-start" title="Modal Start" horizontal="start">
            Alignée à gauche
            <x-slot:actions>
                <form method="dialog">
                    <x-daisy::ui.button>Fermer</x-daisy::ui.button>
                </form>
            </x-slot:actions>
        </x-daisy::ui.modal>
        <x-daisy::ui.modal id="demo-modal-end" title="Modal End" horizontal="end">
            Alignée à droite
            <x-slot:actions>
                <form method="dialog">
                    <x-daisy::ui.button>Fermer</x-daisy::ui.button>
                </form>
            </x-slot:actions>
        </x-daisy::ui.modal>

        <!-- Backdrop option -->
        <div class="space-y-2">
            <x-daisy::ui.button onclick="document.getElementById('demo-modal-nobackdrop').showModal()">Sans backdrop</x-daisy::ui.button>
            <x-daisy::ui.modal id="demo-modal-nobackdrop" title="Sans backdrop" :backdrop="false">
                Cliquez en dehors ne fermera pas (utiliser le bouton).
                <x-slot:actions>
                    <form method="dialog">
                        <x-daisy::ui.button>Fermer</x-daisy::ui.button>
                    </form>
                </x-slot:actions>
            </x-daisy::ui.modal>
        </div>

        <!-- Largeur personnalisée via boxClass -->
        <div class="space-y-2">
            <x-daisy::ui.button onclick="document.getElementById('demo-modal-lg').showModal()">Large</x-daisy::ui.button>
            <x-daisy::ui.modal id="demo-modal-lg" title="Large" boxClass="max-w-3xl">
                Modal plus large via classe utilitaire.
                <x-slot:actions>
                    <form method="dialog">
                        <x-daisy::ui.button>Fermer</x-daisy::ui.button>
                    </form>
                </x-slot:actions>
            </x-daisy::ui.modal>
        </div>
    </div>
</section>


