@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'inputs';
    $name = 'input';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'types', 'label' => 'Types'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Champ de texte" 
    category="inputs" 
    name="input"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Champ de texte" 
            subtitle="Champ de saisie de texte compatible daisyUI. Supporte différents types et styles."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="input">
        <x-slot:preview>
            <x-daisy::ui.inputs.input type="text" name="email" placeholder="votre@email.com" />
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.inputs.input type="text" name="email" placeholder="votre@email.com" />';
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

    <x-daisy::docs.sections.variants name="input">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Couleurs</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.inputs.input color="primary" placeholder="Primary" />
                        <x-daisy::ui.inputs.input color="secondary" placeholder="Secondary" />
                        <x-daisy::ui.inputs.input color="accent" placeholder="Accent" />
                        <x-daisy::ui.inputs.input color="info" placeholder="Info" />
                        <x-daisy::ui.inputs.input color="success" placeholder="Success" />
                        <x-daisy::ui.inputs.input color="warning" placeholder="Warning" />
                        <x-daisy::ui.inputs.input color="error" placeholder="Error" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Styles</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.inputs.input variant="ghost" placeholder="Ghost" />
                        <x-daisy::ui.inputs.input disabled placeholder="Disabled" />
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold mb-2">Tailles</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-daisy::ui.inputs.input size="xs" placeholder="Extra Small" />
                        <x-daisy::ui.inputs.input size="sm" placeholder="Small" />
                        <x-daisy::ui.inputs.input size="md" placeholder="Medium" />
                        <x-daisy::ui.inputs.input size="lg" placeholder="Large" />
                        <x-daisy::ui.inputs.input size="xl" placeholder="Extra Large" />
                    </div>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Couleurs --}}
<x-daisy::ui.inputs.input color="primary" placeholder="Primary" />
<x-daisy::ui.inputs.input color="error" placeholder="Error" />

{{-- Styles --}}
<x-daisy::ui.inputs.input variant="ghost" placeholder="Ghost" />
<x-daisy::ui.inputs.input disabled placeholder="Disabled" />

{{-- Tailles --}}
<x-daisy::ui.inputs.input size="xs" placeholder="Extra Small" />
<x-daisy::ui.inputs.input size="lg" placeholder="Large" />';
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

    <x-daisy::docs.sections.custom id="types" title="Types d'input">
        <p class="text-sm text-base-content/70 mb-4">Le composant supporte tous les types HTML natifs.</p>
        <div class="tabs tabs-box">
            <input type="radio" name="types-example-input" class="tab" aria-label="Preview" checked />
            <div class="tab-content bg-base-100 p-6">
                <div class="space-y-4">
                    <x-daisy::ui.inputs.input type="email" name="email" placeholder="email@example.com" />
                    <x-daisy::ui.inputs.input type="password" name="password" placeholder="••••••••" />
                    <x-daisy::ui.inputs.input type="number" name="age" placeholder="25" />
                    <x-daisy::ui.inputs.input type="date" name="birthday" />
                    <x-daisy::ui.inputs.input type="time" name="appointment" />
                    <x-daisy::ui.inputs.input type="url" name="website" placeholder="https://example.com" />
                    <x-daisy::ui.inputs.input type="tel" name="phone" placeholder="+33 6 12 34 56 78" />
                </div>
            </div>
            <input type="radio" name="types-example-input" class="tab" aria-label="Code" />
            <div class="tab-content bg-base-100 p-6">
                @php
                    $typesCode = '<x-daisy::ui.inputs.input type="email" name="email" placeholder="email@example.com" />
<x-daisy::ui.inputs.input type="password" name="password" placeholder="••••••••" />
<x-daisy::ui.inputs.input type="number" name="age" placeholder="25" />
<x-daisy::ui.inputs.input type="date" name="birthday" />
<x-daisy::ui.inputs.input type="url" name="website" placeholder="https://example.com" />';
                @endphp
                <x-daisy::ui.advanced.code-editor 
                    language="blade" 
                    :value="$typesCode"
                    :readonly="true"
                    :showToolbar="false"
                    :showFoldAll="false"
                    :showUnfoldAll="false"
                    :showFormat="false"
                    :showCopy="true"
                    height="250px"
                />
            </div>
        </div>
    </x-daisy::docs.sections.custom>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
