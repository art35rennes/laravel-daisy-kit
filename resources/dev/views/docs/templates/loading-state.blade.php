@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Template Loading State" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost btn-active">Template</a>
        </div>
    </x-slot:navbar>

    <section>
        <h1>Template Loading State</h1>
        <p class="text-base-content/70">
            Template pour afficher un état de chargement avec différents types d'indicateurs (spinner, skeleton, progress).
        </p>
    </section>

    <section class="mt-8">
        <h2>Utilisation</h2>
        
        <div class="mt-4">
            <h3>Type spinner</h3>
            <div class="mockup-code">
                <pre data-prefix=""><code>&lt;x-daisy::templates.loading-state</code></pre>
                <pre data-prefix="  "><code>type="spinner"</code></pre>
                <pre data-prefix="  "><code>message="Chargement en cours..."</code></pre>
                <pre data-prefix="/&gt;"></code></pre>
            </div>
        </div>
        
        <div class="mt-4">
            <h3>Type skeleton</h3>
            <div class="mockup-code">
                <pre data-prefix=""><code>&lt;x-daisy::templates.loading-state</code></pre>
                <pre data-prefix="  "><code>type="skeleton"</code></pre>
                <pre data-prefix="  "><code>:skeletonCount="5"</code></pre>
                <pre data-prefix="/&gt;"></code></pre>
            </div>
        </div>
        
        <div class="mt-4">
            <h3>Type progress</h3>
            <div class="mockup-code">
                <pre data-prefix=""><code>&lt;x-daisy::templates.loading-state</code></pre>
                <pre data-prefix="  "><code>type="progress"</code></pre>
                <pre data-prefix="/&gt;"></code></pre>
            </div>
        </div>
    </section>

    <section class="mt-8">
        <h2>Props disponibles</h2>
        
        <div class="overflow-x-auto mt-4">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Prop</th>
                        <th>Type</th>
                        <th>Défaut</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>type</code></td>
                        <td>string</td>
                        <td><code>spinner</code></td>
                        <td>Type d'indicateur : <code>spinner</code>, <code>skeleton</code>, <code>progress</code></td>
                    </tr>
                    <tr>
                        <td><code>message</code></td>
                        <td>string</td>
                        <td><code>__('common.loading')</code></td>
                        <td>Message de chargement</td>
                    </tr>
                    <tr>
                        <td><code>size</code></td>
                        <td>string</td>
                        <td><code>lg</code></td>
                        <td>Taille de l'indicateur (xs, sm, md, lg, xl)</td>
                    </tr>
                    <tr>
                        <td><code>fullScreen</code></td>
                        <td>bool</td>
                        <td><code>false</code></td>
                        <td>Afficher en plein écran</td>
                    </tr>
                    <tr>
                        <td><code>skeletonCount</code></td>
                        <td>int</td>
                        <td><code>3</code></td>
                        <td>Nombre de skeletons à afficher (type skeleton uniquement)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</x-daisy::layout.docs>

