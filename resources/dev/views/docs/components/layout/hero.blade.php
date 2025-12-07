@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'layout';
    $name = 'hero';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Section hero" 
    category="layout" 
    name="hero"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Section hero" 
            subtitle="Section hero pour les pages d'accueil."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="hero">
        <x-slot:preview>
            <x-daisy::ui.layout.hero imageUrl="https://picsum.photos/1920/1080">
                <div class="hero-content">
                    <h1 class="text-5xl font-bold">Bienvenue</h1>
                    <p class="py-6">Découvrez nos services exceptionnels</p>
                    <x-daisy::ui.inputs.button>Commencer</x-daisy::ui.inputs.button>
                </div>
            </x-daisy::ui.layout.hero>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.layout.hero imageUrl="https://picsum.photos/1920/1080">
    <div class="hero-content">
        <h1 class="text-5xl font-bold">Bienvenue</h1>
        <p class="py-6">Découvrez nos services exceptionnels</p>
        <x-daisy::ui.inputs.button>Commencer</x-daisy::ui.inputs.button>
    </div>
</x-daisy::ui.layout.hero>';
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

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
