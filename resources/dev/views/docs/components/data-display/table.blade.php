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
    $props = DocsHelper::getComponentProps('data-display', 'table');
@endphp

<x-daisy::layout.docs title="Table" :sidebarItems="$navItems" :sections="$sections" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{$prefix}}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{$prefix}}/templates" class="btn btn-sm join-item btn-ghost">Template</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>Table</h1>
        <p>Tableau de données avec fonctionnalités avancées.</p>
        <div class="alert alert-info mt-4">
            <span>Ce composant nécessite le module JavaScript <code>table</code>.</span>
        </div>
    </section>

    <section id="base" class="mt-10">
        <h2>Exemple de base</h2>
        <div class="tabs tabs-box">
            <input type="radio" name="base-example-table" class="tab" aria-label="Preview" checked />
            <div class="tab-content bg-base-100 p-6">
                <div class="not-prose">
                    @php
$headers = ["Nom", "Email", "Rôle"];
$rows = [
    ["Jean Dupont", "jean@example.com", "Admin"],
    ["Marie Martin", "marie@example.com", "Utilisateur"]
];
@endphp
<x-daisy::ui.data-display.table :headers="$headers" :rows="$rows" />
                </div>
            </div>
            <input type="radio" name="base-example-table" class="tab" aria-label="Code" />
            <div class="tab-content bg-base-100 p-6">
                @php
                    $baseCode = '@php
$headers = ["Nom", "Email", "Rôle"];
$rows = [
    ["Jean Dupont", "jean@example.com", "Admin"],
    ["Marie Martin", "marie@example.com", "Utilisateur"]
];
@endphp
<x-daisy::ui.data-display.table :headers="$headers" :rows="$rows" />';
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
            </div>
        </div>
    </section>

    <section id="variants" class="mt-10">
        <h2>Variantes</h2>
        <div class="tabs tabs-box">
            <input type="radio" name="variants-example-table" class="tab" aria-label="Preview" checked />
            <div class="tab-content bg-base-100 p-6">
                <div class="not-prose flex flex-wrap items-center gap-3">
                    <x-daisy::ui.data-display.table size="sm">Small</x-daisy::ui.data-display.table>
                    <x-daisy::ui.data-display.table size="lg">Large</x-daisy::ui.data-display.table>
                </div>
            </div>
            <input type="radio" name="variants-example-table" class="tab" aria-label="Code" />
            <div class="tab-content bg-base-100 p-6">
                @php
                    $variantsCode = '&lt;x-daisy::ui.data-display.table size=&quot;sm&quot;&gt;Small&lt;/x-daisy::ui.data-display.table&gt;
&lt;x-daisy::ui.data-display.table size=&quot;lg&quot;&gt;Large&lt;/x-daisy::ui.data-display.table&gt;';
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
