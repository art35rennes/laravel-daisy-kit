@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'example', 'label' => 'Exemple'],
        ['id' => 'api', 'label' => 'API'],
    ];
@endphp

<x-daisy::docs.page 
    title="Formulaire simple" 
    category="form" 
    name="form-simple"
    type="template"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Formulaire simple" 
            subtitle="Layout minimal pour pages de formulaire courtes avec bouton d'action configurable."
        />
        <div class="mt-4 text-sm space-y-1">
            <p><strong>Vue :</strong> <code>{{ "view('daisy::templates.form.form-simple')" }}</code></p>
            <p><strong>Alias composant :</strong> <code>&lt;x-daisy::templates.form.form-simple /&gt;</code></p>
        </div>
    </x-slot:intro>

    <x-daisy::docs.sections.custom id="example" title="Exemple">
        <div class="mockup-code mt-4">
            <pre data-prefix=""><code>{{ '<x-daisy::templates.form.form-simple
    title="Contact"
    action="/contact"
    method="POST"
    :elements="view(\'partials.contact-fields\')->render()"
    submitText="Envoyer"
/>
<x-slot:actions>
    <x-daisy::ui.inputs.button type="submit" color="primary">Envoyer</x-daisy::ui.inputs.button>
</x-slot:actions>' }}</code></pre>
        </div>
    </x-daisy::docs.sections.custom>

    <x-daisy::docs.sections.custom id="api" title="Props">
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm">
                <thead><tr><th>Prop</th><th>Type</th><th>Défaut</th><th>Description</th></tr></thead>
                <tbody>
                    <tr><td><code>title</code></td><td>string|null</td><td><code>null</code></td><td>Titre affiché.</td></tr>
                    <tr><td><code>action</code></td><td>string</td><td><code>#</code></td><td>URL du formulaire.</td></tr>
                    <tr><td><code>method</code></td><td>string</td><td><code>POST</code></td><td>Méthode HTTP.</td></tr>
                    <tr><td><code>elements</code></td><td>string|null</td><td><code>null</code></td><td>HTML des champs (pré-rendu).</td></tr>
                    <tr><td><code>submitText</code></td><td>string</td><td><code>__(\'daisy::form.submit\')</code></td><td>Libellé du bouton par défaut.</td></tr>
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            <h3 class="text-base font-semibold">Slots</h3>
            <ul class="list-disc list-inside text-sm space-y-1">
                <li><code>actions</code> : remplace la barre d'actions.</li>
            </ul>
        </div>
    </x-daisy::docs.sections.custom>
</x-daisy::docs.page>

