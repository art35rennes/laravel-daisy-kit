@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getNavigationItems($prefix);
    $sections = [
            ['id' => 'intro', 'label' => 'Introduction'],
            ['id' => 'base', 'label' => 'Exemple de base'],
            ['id' => 'responsive', 'label' => 'Responsive'],
            ['id' => 'api', 'label' => 'API'],
        ];
    $props = DocsHelper::getComponentProps('layout', 'crud-layout');
@endphp

<x-daisy::layout.docs title="CRUD Layout" :sidebarItems="$navItems" :sections="$sections" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{$prefix}}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{$prefix}}/templates" class="btn btn-sm join-item btn-ghost">Template</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>CRUD Layout</h1>
        <p>Layout à 2 colonnes (catégorie / inputs) pour les formulaires CRUD, responsive avec breakpoint configurable.</p>
    </section>

    <section id="base" class="mt-10">
        <h2>Exemple de base</h2>
        <div class="tabs tabs-box">
            <input type="radio" name="base-example-crud-layout" class="tab" aria-label="Preview" checked />
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

                            <x-daisy::ui.partials.form-field name="email" label="Primary email">
                                <x-daisy::ui.inputs.select name="email">
                                    <option>Select primary email...</option>
                                </x-daisy::ui.inputs.select>
                            </x-daisy::ui.partials.form-field>

                            <x-slot:actions>
                                <x-daisy::ui.inputs.button class="btn-primary">Save profile</x-daisy::ui.inputs.button>
                            </x-slot:actions>
                        </x-daisy::ui.layout.crud-section>
                    </x-daisy::ui.layout.crud-layout>
                </div>
            </div>
            <input type="radio" name="base-example-crud-layout" class="tab" aria-label="Code" />
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

        <x-daisy::ui.partials.form-field name="email" label="Primary email">
            <x-daisy::ui.inputs.select name="email">
                <option>Select primary email...</option>
            </x-daisy::ui.inputs.select>
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
                    height="300px"
                />
            </div>
        </div>
    </section>

    <section id="responsive" class="mt-10">
        <h2>Responsive</h2>
        <p>Le layout passe automatiquement en 1 colonne sur mobile et en 2 colonnes (1/3 - 2/3) sur desktop. Le breakpoint est configurable via la prop <code>breakpoint</code>.</p>
        <div class="tabs tabs-box">
            <input type="radio" name="responsive-example-crud-layout" class="tab" aria-label="Preview" checked />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                <div class="not-prose">
                    <x-daisy::ui.layout.crud-layout breakpoint="md" container="max-w-6xl mx-auto px-4">
                        <x-daisy::ui.layout.crud-section
                            title="Section 1"
                            description="Breakpoint md (768px)"
                            breakpoint="md"
                        >
                            <x-daisy::ui.partials.form-field name="field1" label="Field 1">
                                <x-daisy::ui.inputs.input name="field1" />
                            </x-daisy::ui.partials.form-field>
                        </x-daisy::ui.layout.crud-section>
                    </x-daisy::ui.layout.crud-layout>
                </div>
            </div>
            <input type="radio" name="responsive-example-crud-layout" class="tab" aria-label="Code" />
            <div class="tab-content border-base-300 bg-base-100 p-6">
                @php
                    $responsiveCode = '<x-daisy::ui.layout.crud-layout breakpoint="md" container="max-w-6xl mx-auto px-4">
    <x-daisy::ui.layout.crud-section
        title="Section 1"
        description="Breakpoint md (768px)"
        breakpoint="md"
    >
        <x-daisy::ui.partials.form-field name="field1" label="Field 1">
            <x-daisy::ui.inputs.input name="field1" />
        </x-daisy::ui.partials.form-field>
    </x-daisy::ui.layout.crud-section>
</x-daisy::ui.layout.crud-layout>';
                @endphp
                <x-daisy::ui.advanced.code-editor 
                    language="blade" 
                    :value="$responsiveCode"
                    :readonly="true"
                    :showToolbar="false"
                    :showFoldAll="false"
                    :showUnfoldAll="false"
                    :showFormat="false"
                    :showCopy="true"
                    height="200px"
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

