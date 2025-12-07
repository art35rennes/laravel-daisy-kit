@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'overlay';
    $name = 'modal';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'slots', 'label' => 'Slots'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Modale" 
    category="overlay" 
    name="modal"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Modale" 
            subtitle="Fenêtre modale pour afficher du contenu."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="modal">
        <x-slot:preview>
            <button onclick="document.getElementById('confirm-modal').showModal()" class="btn btn-primary">Ouvrir la modale</button>
            <x-daisy::ui.overlay.modal id="confirm-modal" title="Confirmer la suppression">
                <p>Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.</p>
                <x-slot:actions>
                    <x-daisy::ui.inputs.button variant="ghost" onclick="document.getElementById('confirm-modal').close()">Annuler</x-daisy::ui.inputs.button>
                    <x-daisy::ui.inputs.button color="error" onclick="document.getElementById('confirm-modal').close()">Supprimer</x-daisy::ui.inputs.button>
                </x-slot:actions>
            </x-daisy::ui.overlay.modal>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.overlay.modal id="confirm-modal" title="Confirmer la suppression">
    <p>Êtes-vous sûr de vouloir supprimer cet élément ?</p>
    <x-slot:actions>
        <x-daisy::ui.inputs.button variant="ghost">Annuler</x-daisy::ui.inputs.button>
        <x-daisy::ui.inputs.button color="error">Supprimer</x-daisy::ui.inputs.button>
    </x-slot:actions>
</x-daisy::ui.overlay.modal>

{{-- Ouvrir avec JavaScript --}}
<button onclick="document.getElementById(\'confirm-modal\').showModal()">
    Ouvrir
</button>';
            @endphp
            <x-daisy::ui.advanced.code-editor 
                language="blade" 
                :value="$baseCode"
                :readonly="true"
                :showToolbar="false"
                :showFoldAll="false"
                :showUnfoldAll="false"
                :showFormat="false"
                :showCopy="true"
                height="250px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.example>

    <x-daisy::docs.sections.variants name="modal">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="flex flex-wrap gap-3">
                        <button onclick="document.getElementById('modal-sm').showModal()" class="btn btn-sm">Small</button>
                        <button onclick="document.getElementById('modal-md').showModal()" class="btn btn-sm">Medium</button>
                        <button onclick="document.getElementById('modal-lg').showModal()" class="btn btn-sm">Large</button>
                        <button onclick="document.getElementById('modal-xl').showModal()" class="btn btn-sm">Extra Large</button>
                    </div>
                    <x-daisy::ui.overlay.modal id="modal-sm" size="sm" title="Modal Small">
                        <p>Contenu de la modale small</p>
                    </x-daisy::ui.overlay.modal>
                    <x-daisy::ui.overlay.modal id="modal-md" size="md" title="Modal Medium">
                        <p>Contenu de la modale medium</p>
                    </x-daisy::ui.overlay.modal>
                    <x-daisy::ui.overlay.modal id="modal-lg" size="lg" title="Modal Large">
                        <p>Contenu de la modale large</p>
                    </x-daisy::ui.overlay.modal>
                    <x-daisy::ui.overlay.modal id="modal-xl" size="xl" title="Modal Extra Large">
                        <p>Contenu de la modale extra large</p>
                    </x-daisy::ui.overlay.modal>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Positionnement</p>
                    <div class="flex flex-wrap gap-3">
                        <button onclick="document.getElementById('modal-top').showModal()" class="btn btn-sm">Top</button>
                        <button onclick="document.getElementById('modal-bottom').showModal()" class="btn btn-sm">Bottom</button>
                        <button onclick="document.getElementById('modal-start').showModal()" class="btn btn-sm">Start</button>
                        <button onclick="document.getElementById('modal-end').showModal()" class="btn btn-sm">End</button>
                    </div>
                    <x-daisy::ui.overlay.modal id="modal-top" vertical="top" title="Modal Top">
                        <p>Modale positionnée en haut</p>
                    </x-daisy::ui.overlay.modal>
                    <x-daisy::ui.overlay.modal id="modal-bottom" vertical="bottom" title="Modal Bottom">
                        <p>Modale positionnée en bas</p>
                    </x-daisy::ui.overlay.modal>
                    <x-daisy::ui.overlay.modal id="modal-start" horizontal="start" title="Modal Start">
                        <p>Modale positionnée à gauche</p>
                    </x-daisy::ui.overlay.modal>
                    <x-daisy::ui.overlay.modal id="modal-end" horizontal="end" title="Modal End">
                        <p>Modale positionnée à droite</p>
                    </x-daisy::ui.overlay.modal>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Tailles --}}
<x-daisy::ui.overlay.modal id="my-modal" size="sm" title="Small">
    <p>Contenu</p>
</x-daisy::ui.overlay.modal>

<x-daisy::ui.overlay.modal id="my-modal" size="lg" title="Large">
    <p>Contenu</p>
</x-daisy::ui.overlay.modal>

{{-- Positionnement --}}
<x-daisy::ui.overlay.modal id="my-modal" vertical="top" title="Top">
    <p>Contenu</p>
</x-daisy::ui.overlay.modal>

<x-daisy::ui.overlay.modal id="my-modal" horizontal="end" title="End">
    <p>Contenu</p>
</x-daisy::ui.overlay.modal>

{{-- Ouvrir avec JavaScript --}}
<button onclick="document.getElementById(\'my-modal\').showModal()">
    Ouvrir
</button>';
            @endphp
            <x-daisy::ui.advanced.code-editor 
                language="blade" 
                :value="$variantsCode"
                :readonly="true"
                :showToolbar="false"
                :showFoldAll="false"
                :showUnfoldAll="false"
                :showFormat="false"
                :showCopy="true"
                height="350px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.variants>

    <x-daisy::docs.sections.custom id="slots" title="Slots disponibles">
        <p class="text-sm text-base-content/70 mb-4">Le composant modal supporte plusieurs slots pour personnaliser le contenu et les actions.</p>
        <div class="tabs tabs-box">
            <input type="radio" name="slots-example-modal" class="tab" aria-label="Preview" checked />
            <div class="tab-content bg-base-100 p-6">
                <button onclick="document.getElementById('modal-slots').showModal()" class="btn btn-primary">Voir exemple avec slots</button>
                <x-daisy::ui.overlay.modal id="modal-slots" title="Modal avec slots">
                    <p>Contenu principal de la modale.</p>
                    <x-slot:actions>
                        <x-daisy::ui.inputs.button variant="ghost" onclick="document.getElementById('modal-slots').close()">Annuler</x-daisy::ui.inputs.button>
                        <x-daisy::ui.inputs.button color="primary" onclick="document.getElementById('modal-slots').close()">Confirmer</x-daisy::ui.inputs.button>
                    </x-slot:actions>
                </x-daisy::ui.overlay.modal>
            </div>
            <input type="radio" name="slots-example-modal" class="tab" aria-label="Code" />
            <div class="tab-content bg-base-100 p-6">
                @php
                    $slotsCode = '<x-daisy::ui.overlay.modal id="my-modal" title="Titre">
    <p>Contenu principal</p>
    <x-slot:actions>
        <x-daisy::ui.inputs.button variant="ghost">Annuler</x-daisy::ui.inputs.button>
        <x-daisy::ui.inputs.button color="primary">Confirmer</x-daisy::ui.inputs.button>
    </x-slot:actions>
</x-daisy::ui.overlay.modal>

{{-- Ouvrir la modale --}}
<button onclick="document.getElementById(\'my-modal\').showModal()">
    Ouvrir
</button>';
                @endphp
                <x-daisy::ui.advanced.code-editor 
                    language="blade" 
                    :value="$slotsCode"
                    :readonly="true"
                    :showToolbar="false"
                    :showFoldAll="false"
                    :showUnfoldAll="false"
                    :showFormat="false"
                    :showCopy="true"
                    height="300px"
                />
            </div>
        </div>
    </x-daisy::docs.sections.custom>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
