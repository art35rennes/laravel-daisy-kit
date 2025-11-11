@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getNavigationItems($prefix);
    $sections = [
            ['id' => 'intro', 'label' => 'Introduction'],
            ['id' => 'base', 'label' => 'Exemple de base'],
            ['id' => 'variants', 'label' => 'Variantes'],
            ['id' => 'api', 'label' => 'API'],
        ];
    $props = DocsHelper::getComponentProps('layout', 'crud-section');
@endphp

<x-daisy::layout.docs title="CRUD Section" :sidebarItems="$navItems" :sections="$sections" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{$prefix}}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{$prefix}}/templates" class="btn btn-sm join-item btn-ghost">Template</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>CRUD Section</h1>
        <p>Composant enfant pour structurer chaque section d'un formulaire CRUD avec titre, description et slot d'actions.</p>
        <p class="mt-2 text-sm text-base-content/70">À utiliser à l'intérieur de <a href="/{{$prefix}}/layout/crud-layout" class="link">CRUD Layout</a>.</p>
    </section>

    <section id="base" class="mt-10">
        <h2>Exemple de base</h2>
        <div class="tabs tabs-box">
            <input type="radio" name="base-example-crud-section" class="tab" aria-label="Preview" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <div class="not-prose">
                    <x-daisy::ui.layout.crud-layout>
                        <x-daisy::ui.layout.crud-section
                            title="Profile"
                            description="This is how others will see you on the site."
                        >
                            <x-daisy::ui.partials.form-field name="username" label="Username">
                                <x-daisy::ui.inputs.input name="username" value="calebporzio" />
                            </x-daisy::ui.partials.form-field>

                            <x-slot:actions>
                                <x-daisy::ui.inputs.button class="btn-primary">Save profile</x-daisy::ui.inputs.button>
                            </x-slot:actions>
                        </x-daisy::ui.layout.crud-section>
                    </x-daisy::ui.layout.crud-layout>
                </div>
            </div>
            <input type="radio" name="base-example-crud-section" class="tab" aria-label="Code" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    $baseCode = '<x-daisy::ui.layout.crud-layout>
    <x-daisy::ui.layout.crud-section
        title="Profile"
        description="This is how others will see you on the site."
    >
        <x-daisy::ui.partials.form-field name="username" label="Username">
            <x-daisy::ui.inputs.input name="username" value="calebporzio" />
        </x-daisy::ui.partials.form-field>

        <x-slot:actions>
            <x-daisy::ui.inputs.button class="btn-primary">Save profile</x-daisy::ui.inputs.button>
        </x-slot:actions>
    </x-daisy::ui.layout.crud-section>
</x-daisy::ui.layout.crud-layout>';
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
            </div>
        </div>
    </section>

    <section id="variants" class="mt-10">
        <h2>Variantes</h2>
        
        <h3 class="mt-6 text-lg font-medium">Section avec bordure</h3>
        <p class="text-sm text-base-content/70 mb-4">Utilisez la prop <code>borderTop</code> pour séparer visuellement les sections.</p>
        <div class="tabs tabs-box">
            <input type="radio" name="border-example-crud-section" class="tab" aria-label="Preview" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <div class="not-prose">
                    <x-daisy::ui.layout.crud-layout>
                        <x-daisy::ui.layout.crud-section title="Section 1">
                            <x-daisy::ui.partials.form-field name="field1" label="Field 1">
                                <x-daisy::ui.inputs.input name="field1" />
                            </x-daisy::ui.partials.form-field>
                        </x-daisy::ui.layout.crud-section>
                        <x-daisy::ui.layout.crud-section title="Section 2" :borderTop="true">
                            <x-daisy::ui.partials.form-field name="field2" label="Field 2">
                                <x-daisy::ui.inputs.input name="field2" />
                            </x-daisy::ui.partials.form-field>
                        </x-daisy::ui.layout.crud-section>
                    </x-daisy::ui.layout.crud-layout>
                </div>
            </div>
            <input type="radio" name="border-example-crud-section" class="tab" aria-label="Code" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    $borderCode = '<x-daisy::ui.layout.crud-section title="Section 1">
    {{-- Contenu --}}
</x-daisy::ui.layout.crud-section>
<x-daisy::ui.layout.crud-section title="Section 2" :borderTop="true">
    {{-- Contenu --}}
</x-daisy::ui.layout.crud-section>';
                @endphp
                <x-daisy::ui.advanced.code-editor 
                    language="blade" 
                    :value="$borderCode"
                    :readonly="true"
                    :showToolbar="false"
                    :showFoldAll="false"
                    :showUnfoldAll="false"
                    :showFormat="false"
                    :showCopy="true"
                    height="150px"
                />
            </div>
        </div>

        <h3 class="mt-6 text-lg font-medium">Ratio des colonnes personnalisé</h3>
        <p class="text-sm text-base-content/70 mb-4">Personnalisez le ratio des colonnes avec <code>categoryWidth</code> et <code>contentWidth</code>.</p>
        <div class="tabs tabs-box">
            <input type="radio" name="ratio-example-crud-section" class="tab" aria-label="Preview" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <div class="not-prose">
                    <x-daisy::ui.layout.crud-layout>
                        <x-daisy::ui.layout.crud-section 
                            title="Section 1/4 - 3/4"
                            categoryWidth="1/4"
                            contentWidth="3/4"
                        >
                            <x-daisy::ui.partials.form-field name="field1" label="Field 1">
                                <x-daisy::ui.inputs.input name="field1" />
                            </x-daisy::ui.partials.form-field>
                        </x-daisy::ui.layout.crud-section>
                    </x-daisy::ui.layout.crud-layout>
                </div>
            </div>
            <input type="radio" name="ratio-example-crud-section" class="tab" aria-label="Code" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    $ratioCode = '<x-daisy::ui.layout.crud-section 
    title="Section 1/4 - 3/4"
    categoryWidth="1/4"
    contentWidth="3/4"
>
    {{-- Contenu --}}
</x-daisy::ui.layout.crud-section>';
                @endphp
                <x-daisy::ui.advanced.code-editor 
                    language="blade" 
                    :value="$ratioCode"
                    :readonly="true"
                    :showToolbar="false"
                    :showFoldAll="false"
                    :showUnfoldAll="false"
                    :showFormat="false"
                    :showCopy="true"
                    height="150px"
                />
            </div>
        </div>
    </section>

    @if(!empty($props))
    <section id="api" class="mt-10">
        <h2>API</h2>
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Prop</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($props as $prop)
                        <tr>
                            <td><code>{{ $prop }}</code></td>
                            <td class="opacity-70">Voir les commentaires dans le composant Blade pour les valeurs et défauts.</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif
</x-daisy::layout.docs>

