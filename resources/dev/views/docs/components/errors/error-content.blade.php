@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'errors';
    $name = 'error-content';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Error Content" 
    category="errors" 
    name="error-content"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Error Content" 
            subtitle="Contenu de page d'erreur avec code de statut, titre et message personnalisables."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="error-content">
        <x-slot:preview>
            <x-daisy::ui.errors.error-content 
                statusCode="404" 
                title="Page non trouvée" 
                message="La page demandée n'existe pas." 
            />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.errors.error-content 
    statusCode="404" 
    title="Page non trouvée" 
    message="La page demandée n\'existe pas." 
/>';
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
                height="200px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.example>

    <x-daisy::docs.sections.variants name="error-content">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Erreur 404</p>
                    <x-daisy::ui.errors.error-content 
                        statusCode="404" 
                        title="Page non trouvée" 
                        message="La page demandée n'existe pas." 
                    />
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Erreur 500</p>
                    <x-daisy::ui.errors.error-content 
                        statusCode="500" 
                        title="Erreur serveur" 
                        message="Une erreur interne s'est produite." 
                    />
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Erreur 403</p>
                    <x-daisy::ui.errors.error-content 
                        statusCode="403" 
                        title="Accès refusé" 
                        message="Vous n'avez pas les permissions nécessaires." 
                    />
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Erreur 404 --}}
<x-daisy::ui.errors.error-content 
    statusCode="404" 
    title="Page non trouvée" 
    message="La page demandée n\'existe pas." 
/>

{{-- Erreur 500 --}}
<x-daisy::ui.errors.error-content 
    statusCode="500" 
    title="Erreur serveur" 
    message="Une erreur interne s\'est produite." 
/>

{{-- Erreur 403 --}}
<x-daisy::ui.errors.error-content 
    statusCode="403" 
    title="Accès refusé" 
    message="Vous n\'avez pas les permissions nécessaires." 
/>';
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

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
