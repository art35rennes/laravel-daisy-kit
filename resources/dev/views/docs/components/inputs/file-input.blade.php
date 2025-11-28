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
    $props = DocsHelper::getComponentProps('inputs', 'file-input');
@endphp

<x-daisy::layout.docs title="File Input" :sidebarItems="$navItems" :sections="$sections" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{$prefix}}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{$prefix}}/templates" class="btn btn-sm join-item btn-ghost">Template</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>File Input</h1>
        <p>Champ de téléchargement de fichier compatible daisyUI.</p>
        <div class="alert alert-info mt-4">
            <span>Ce composant nécessite le module JavaScript <code>file-input</code>.</span>
        </div>
    </section>

    <section id="base" class="mt-10">
        <h2>Exemple de base</h2>
        <div class="tabs tabs-box">
            <input type="radio" name="base-example-file-input" class="tab" aria-label="Preview" checked />
            <div class="tab-content bg-base-100 p-6">
                <div class="not-prose">
                    <x-daisy::ui.inputs.file-input name="avatar" accept="image/*" />
                </div>
            </div>
            <input type="radio" name="base-example-file-input" class="tab" aria-label="Code" />
            <div class="tab-content bg-base-100 p-6">
                @php
                    $baseCode = '<x-daisy::ui.inputs.file-input name="avatar" accept="image/*" />';
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
            <input type="radio" name="variants-example-file-input" class="tab" aria-label="Preview" checked />
            <div class="tab-content bg-base-100 p-6">
                <div class="not-prose flex flex-wrap items-center gap-3">
                    <x-daisy::ui.inputs.file-input color="primary" placeholder="Primary" />
                    <x-daisy::ui.inputs.file-input color="secondary" placeholder="Secondary" />
                    <x-daisy::ui.inputs.file-input variant="outline" placeholder="Outline" />
                    <x-daisy::ui.inputs.file-input variant="ghost" placeholder="Ghost" />
                    <x-daisy::ui.inputs.file-input size="sm" placeholder="Small" />
                    <x-daisy::ui.inputs.file-input size="lg" placeholder="Large" />
                </div>
            </div>
            <input type="radio" name="variants-example-file-input" class="tab" aria-label="Code" />
            <div class="tab-content bg-base-100 p-6">
                @php
                    $variantsCode = '&lt;x-daisy::ui.inputs.file-input color=&quot;primary&quot; placeholder=&quot;Primary&quot; /&gt;
&lt;x-daisy::ui.inputs.file-input color=&quot;secondary&quot; placeholder=&quot;Secondary&quot; /&gt;
&lt;x-daisy::ui.inputs.file-input variant=&quot;outline&quot; placeholder=&quot;Outline&quot; /&gt;
&lt;x-daisy::ui.inputs.file-input variant=&quot;ghost&quot; placeholder=&quot;Ghost&quot; /&gt;
&lt;x-daisy::ui.inputs.file-input size=&quot;sm&quot; placeholder=&quot;Small&quot; /&gt;
&lt;x-daisy::ui.inputs.file-input size=&quot;lg&quot; placeholder=&quot;Large&quot; /&gt;';
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
