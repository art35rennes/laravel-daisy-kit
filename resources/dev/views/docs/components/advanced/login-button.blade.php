@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'advanced';
    $name = 'login-button';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Bouton de connexion" 
    category="advanced" 
    name="login-button"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Bouton de connexion" 
            subtitle="Bouton de connexion OAuth avec support de multiples fournisseurs."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="login-button">
        <x-slot:preview>
            <x-daisy::ui.advanced.login-button provider="github" label="Se connecter avec GitHub" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.advanced.login-button provider="github" label="Se connecter avec GitHub" />';
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

    <x-daisy::docs.sections.variants name="login-button">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Fournisseurs OAuth</p>
                    <div class="flex flex-wrap gap-2">
                        <x-daisy::ui.advanced.login-button provider="github" label="GitHub" />
                        <x-daisy::ui.advanced.login-button provider="google" label="Google" />
                        <x-daisy::ui.advanced.login-button provider="facebook" label="Facebook" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Variantes de style</p>
                    <div class="flex flex-wrap gap-2">
                        <x-daisy::ui.advanced.login-button provider="github" variant="outline" label="Outline" />
                        <x-daisy::ui.advanced.login-button provider="github" variant="ghost" label="Ghost" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="flex flex-wrap items-center gap-2">
                        <x-daisy::ui.advanced.login-button provider="github" size="sm" label="Small" />
                        <x-daisy::ui.advanced.login-button provider="github" size="md" label="Medium" />
                        <x-daisy::ui.advanced.login-button provider="github" size="lg" label="Large" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Fournisseurs OAuth --}}
<x-daisy::ui.advanced.login-button provider="github" label="GitHub" />
<x-daisy::ui.advanced.login-button provider="google" label="Google" />

{{-- Variantes de style --}}
<x-daisy::ui.advanced.login-button provider="github" variant="outline" label="Outline" />
<x-daisy::ui.advanced.login-button provider="github" variant="ghost" label="Ghost" />

{{-- Tailles --}}
<x-daisy::ui.advanced.login-button provider="github" size="sm" label="Small" />
<x-daisy::ui.advanced.login-button provider="github" size="lg" label="Large" />';
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
                height="300px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.variants>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
