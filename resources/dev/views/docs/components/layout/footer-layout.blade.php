@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'layout';
    $name = 'footer-layout';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Footer Layout" 
    category="layout" 
    name="footer-layout"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Footer Layout" 
            subtitle="Layout de pied de page avec colonnes, liens sociaux et copyright."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="footer-layout">
        <x-slot:preview>
            @php
                $columns = [
                    [
                        'title' => 'Produits', 
                        'links' => [
                            ['label' => 'Fonctionnalités', 'href' => '/features'], 
                            ['label' => 'Tarifs', 'href' => '/pricing']
                        ]
                    ],
                    [
                        'title' => 'Support', 
                        'links' => [
                            ['label' => 'Documentation', 'href' => '/docs'], 
                            ['label' => 'Contact', 'href' => '/contact']
                        ]
                    ]
                ];
                $socialLinks = [
                    ['icon' => 'bi-twitter', 'href' => '#', 'label' => 'Twitter'], 
                    ['icon' => 'bi-github', 'href' => '#', 'label' => 'GitHub']
                ];
            @endphp
            <x-daisy::ui.layout.footer-layout 
                :columns="$columns" 
                brandText="Mon Entreprise" 
                copyrightText="Tous droits réservés" 
                :socialLinks="$socialLinks" 
            />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = <<<'CODE'
<x-daisy::ui.layout.footer-layout
    :columns="[
        [
            'title' => 'Produits',
            'links' => [
                ['label' => 'Fonctionnalités', 'href' => '/features'],
                ['label' => 'Tarifs', 'href' => '/pricing'],
            ],
        ],
        [
            'title' => 'Support',
            'links' => [
                ['label' => 'Documentation', 'href' => '/docs'],
                ['label' => 'Contact', 'href' => '/contact'],
            ],
        ],
    ]"
    brandText="Mon Entreprise"
    copyrightText="Tous droits réservés"
    :socialLinks="[
        ['icon' => 'bi-twitter', 'href' => '#', 'label' => 'Twitter'],
        ['icon' => 'bi-github', 'href' => '#', 'label' => 'GitHub'],
    ]"
/>
CODE;
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
                height="300px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.example>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
