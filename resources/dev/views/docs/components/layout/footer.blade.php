@php
    use App\Helpers\DocsHelper;
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $category = 'layout';
    $name = 'footer';
    $sections = [
        ['id' => 'intro', 'label' => 'Introduction'],
        ['id' => 'base', 'label' => 'Exemple de base'],
        ['id' => 'variants', 'label' => 'Variantes'],
        ['id' => 'api', 'label' => 'API'],
    ];
    $props = DocsHelper::getComponentProps($category, $name);
@endphp

<x-daisy::docs.page 
    title="Pied de page" 
    category="layout" 
    name="footer"
    type="component"
    :sections="$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="Pied de page" 
            subtitle="Pied de page du site."
        />
    </x-slot:intro>

    <x-daisy::docs.sections.example name="footer">
        <x-slot:preview>
            <x-daisy::ui.layout.footer class="bg-base-200">
                <nav>
                    <h6 class="footer-title">Services</h6>
                    <a href="/about" class="link link-hover">À propos</a>
                    <a href="/contact" class="link link-hover">Contact</a>
                </nav>
                <nav>
                    <h6 class="footer-title">Légal</h6>
                    <a href="/terms" class="link link-hover">Conditions</a>
                    <a href="/privacy" class="link link-hover">Confidentialité</a>
                </nav>
                <aside>
                    <p>© 2024 Mon Entreprise. Tous droits réservés.</p>
                </aside>
            </x-daisy::ui.layout.footer>
        </x-slot:preview>
        <x-slot:code>
            @php
                $baseCode = '<x-daisy::ui.layout.footer>
    <nav>
        <h6 class="footer-title">Services</h6>
        <a href="/about" class="link link-hover">À propos</a>
        <a href="/contact" class="link link-hover">Contact</a>
    </nav>
    <nav>
        <h6 class="footer-title">Légal</h6>
        <a href="/terms" class="link link-hover">Conditions</a>
        <a href="/privacy" class="link link-hover">Confidentialité</a>
    </nav>
    <aside>
        <p>© 2024 Mon Entreprise. Tous droits réservés.</p>
    </aside>
</x-daisy::ui.layout.footer>';
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
        </x-slot:code>
    </x-daisy::docs.sections.example>

    <x-daisy::docs.sections.variants name="footer">
        <x-slot:preview>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-semibold mb-2">Centré</p>
                    <x-daisy::ui.layout.footer class="bg-base-200" center>
                        <nav>
                            <h6 class="footer-title">Services</h6>
                            <a href="/about" class="link link-hover">À propos</a>
                            <a href="/contact" class="link link-hover">Contact</a>
                        </nav>
                        <aside>
                            <p>© 2024 Mon Entreprise.</p>
                        </aside>
                    </x-daisy::ui.layout.footer>
                </div>
            </div>
        </x-slot:preview>
        <x-slot:code>
            @php
                $variantsCode = '{{-- Centré --}}
<x-daisy::ui.layout.footer center>
    <nav>
        <h6 class="footer-title">Services</h6>
        <a href="/about" class="link link-hover">À propos</a>
        <a href="/contact" class="link link-hover">Contact</a>
    </nav>
    <aside>
        <p>© 2024 Mon Entreprise.</p>
    </aside>
</x-daisy::ui.layout.footer>';
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
                height="250px"
            />
        </x-slot:code>
    </x-daisy::docs.sections.variants>

    <x-daisy::docs.sections.api :category="$category" :name="$name" />
</x-daisy::docs.page>
