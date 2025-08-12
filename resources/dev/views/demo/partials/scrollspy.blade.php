<!-- ScrollSpy -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">ScrollSpy</h2>
    <div class="grid md:grid-cols-3 gap-6 items-start">
        <div class="md:col-span-2">
            <div id="spyContainer" class="h-80 overflow-y-auto bg-base-100 rounded-box border border-base-300 p-4 space-y-12">
                <section id="sec-1">
                    <h3 class="text-lg font-semibold mb-2">Section 1</h3>
                    <p>
                        Le composant ScrollSpy permet de mettre en évidence dynamiquement la section actuellement visible dans une zone de contenu défilante. Il est particulièrement utile pour les longues pages ou les documentations techniques.
                    </p>
                </section>
                <section id="sec-2">
                    <h3 class="text-lg font-semibold mb-2">Section 2</h3>
                    <p>
                        Parmi les fonctionnalités principales du composant ScrollSpy, on retrouve la détection automatique des sections visibles, la génération dynamique du menu de navigation, et la possibilité de personnaliser les classes CSS.
                    </p>
                </section>
                <section id="sec-3">
                    <h3 class="text-lg font-semibold mb-2">Section 3</h3>
                    <p>
                        L'API du composant ScrollSpy est conçue pour être simple et flexible. Il suffit de spécifier le sélecteur du conteneur à surveiller via l'attribut container, et d'indiquer le type d'éléments à suivre via l'attribut track.
                    </p>
                </section>
                <section id="sec-4">
                    <h3 class="text-lg font-semibold mb-2">Section 4</h3>
                    <p>
                        Exemple d'utilisation classique dans une documentation technique, un blog ou une page produit longue.
                    </p>
                </section>
            </div>
        </div>
        <div>
            <x-daisy::ui.scrollspy :items="[
                ['id' => 'sec-1', 'label' => 'Section 1'],
                ['id' => 'sec-2', 'label' => 'Section 2'],
                ['id' => 'sec-3', 'label' => 'Section 3'],
                ['id' => 'sec-4', 'label' => 'Section 4'],
            ]" container="#spyContainer" :smoothScroll="true" offset="8" navClass="menu menu-sm bg-base-100 rounded-box p-2 w-full" activeClass="active" />
        </div>
    </div>

    <div class="divider">Auto-génération</div>
    <div class="grid md:grid-cols-3 gap-6 items-start">
        <div class="md:col-span-2">
            <div id="spyContainerAuto" class="h-80 overflow-y-auto bg-base-100 rounded-box border border-base-300 p-4 space-y-12">
                <section>
                    <h3 class="text-lg font-semibold mb-2">Intro</h3>
                    <p>Présentation et principe général du composant ScrollSpy.</p>
                </section>
                <section>
                    <h3 class="text-lg font-semibold mb-2">Features</h3>
                    <p>Détection des sections, menu auto-généré, smooth scroll, offset personnalisé, etc.</p>
                </section>
                <section>
                    <h3 class="text-lg font-semibold mb-2">API</h3>
                    <p>Attributs principaux: container, track, autogen, navClass, activeClass, offset.</p>
                </section>
                <section>
                    <h3 class="text-lg font-semibold mb-2">Examples</h3>
                    <p>Documentation, blogs, pages produits longues, etc.</p>
                </section>
            </div>
        </div>
        <div>
            <x-daisy::ui.scrollspy container="#spyContainerAuto" :autogen="true" track="section" navClass="menu menu-sm bg-base-100 rounded-box p-2 w-full" />
        </div>
    </div>
</section>


