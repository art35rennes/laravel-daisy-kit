@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getNavigationItems($prefix);
@endphp

<x-daisy::layout.docs title="Template Empty State" :sidebarItems="$navItems" :currentRoute="request()->path()">
    <x-slot:navbar>
        <div class="join">
            <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
            <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost">Démo</a>
            <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost btn-active">Template</a>
        </div>
    </x-slot:navbar>

    <section>
        <h1>Template Empty State</h1>
        <p class="text-base-content/70">
            Template pour afficher un état vide (aucune donnée, aucun résultat) avec message et action optionnelle.
        </p>
    </section>

    <section class="mt-8">
        <h2>Utilisation</h2>
        
        <div class="mockup-code mt-4">
            <pre data-prefix=""><code>&lt;x-daisy::templates.empty-state</code></pre>
            <pre data-prefix="  "><code>icon="bi-inbox"</code></pre>
            <pre data-prefix="  "><code>title="Aucune donnée"</code></pre>
            <pre data-prefix="  "><code>message="Commencez par créer votre premier élément."</code></pre>
            <pre data-prefix="  "><code>actionLabel="Créer"</code></pre>
            <pre data-prefix="  "><code>actionUrl="{{ route('items.create') }}"</code></pre>
            <pre data-prefix="/&gt;"></code></pre>
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
                        <td><code>icon</code></td>
                        <td>string</td>
                        <td><code>bi-inbox</code></td>
                        <td>Nom de l'icône Blade Icons</td>
                    </tr>
                    <tr>
                        <td><code>title</code></td>
                        <td>string</td>
                        <td><code>__('common.empty')</code></td>
                        <td>Titre de l'état vide</td>
                    </tr>
                    <tr>
                        <td><code>message</code></td>
                        <td>string|null</td>
                        <td><code>null</code></td>
                        <td>Message descriptif optionnel</td>
                    </tr>
                    <tr>
                        <td><code>actionLabel</code></td>
                        <td>string|null</td>
                        <td><code>null</code></td>
                        <td>Label du bouton d'action</td>
                    </tr>
                    <tr>
                        <td><code>actionUrl</code></td>
                        <td>string|null</td>
                        <td><code>null</code></td>
                        <td>URL du bouton d'action</td>
                    </tr>
                    <tr>
                        <td><code>actionVariant</code></td>
                        <td>string</td>
                        <td><code>primary</code></td>
                        <td>Variante du bouton (primary, secondary, etc.)</td>
                    </tr>
                    <tr>
                        <td><code>size</code></td>
                        <td>string</td>
                        <td><code>md</code></td>
                        <td>Taille (xs, sm, md, lg)</td>
                    </tr>
                    <tr>
                        <td><code>illustration</code></td>
                        <td>string|null</td>
                        <td><code>null</code></td>
                        <td>URL d'une illustration personnalisée</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</x-daisy::layout.docs>

