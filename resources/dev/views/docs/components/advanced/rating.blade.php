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
    $props = DocsHelper::getComponentProps('advanced', 'rating');
@endphp

<x-daisy::layout.docs title="Rating" :sidebarItems="$navItems" :sections="$sections" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{$prefix}}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{$prefix}}/templates" class="btn btn-sm join-item btn-ghost">Template</a>
        </div>
    </x-slot:navbar>

    <section id="intro">
        <h1>Rating</h1>
        <p>Système de notation.</p>
    </section>

    <section id="base" class="mt-10">
        <h2>Exemple de base</h2>
        <div class="tabs tabs-box">
            <input type="radio" name="base-example-rating" class="tab" aria-label="Preview" checked />
            <div class="tab-content bg-base-100 p-6">
                <div class="not-prose">
                    <x-daisy::ui.advanced.rating name="rating" :count="5" :value="4" />
                </div>
            </div>
            <input type="radio" name="base-example-rating" class="tab" aria-label="Code" />
            <div class="tab-content bg-base-100 p-6">
                @php
                    $baseCode = '<x-daisy::ui.advanced.rating name="rating" :count="5" :value="4" />';
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
            <input type="radio" name="variants-example-rating" class="tab" aria-label="Preview" checked />
            <div class="tab-content bg-base-100 p-6">
                <div class="not-prose flex flex-wrap items-center gap-3">
                    <x-daisy::ui.advanced.rating color="primary">Primary</x-daisy::ui.advanced.rating>
                    <x-daisy::ui.advanced.rating color="secondary">Secondary</x-daisy::ui.advanced.rating>
                    <x-daisy::ui.advanced.rating size="sm">Small</x-daisy::ui.advanced.rating>
                    <x-daisy::ui.advanced.rating size="lg">Large</x-daisy::ui.advanced.rating>
                </div>
            </div>
            <input type="radio" name="variants-example-rating" class="tab" aria-label="Code" />
            <div class="tab-content bg-base-100 p-6">
                @php
                    $variantsCode = '&lt;x-daisy::ui.advanced.rating color=&quot;primary&quot;&gt;Primary&lt;/x-daisy::ui.advanced.rating&gt;
&lt;x-daisy::ui.advanced.rating color=&quot;secondary&quot;&gt;Secondary&lt;/x-daisy::ui.advanced.rating&gt;
&lt;x-daisy::ui.advanced.rating size=&quot;sm&quot;&gt;Small&lt;/x-daisy::ui.advanced.rating&gt;
&lt;x-daisy::ui.advanced.rating size=&quot;lg&quot;&gt;Large&lt;/x-daisy::ui.advanced.rating&gt;';
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
