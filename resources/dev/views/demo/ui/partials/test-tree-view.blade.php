<!-- TreeView -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">TreeView</h2>
    <p class="text-sm opacity-70">
        Démo du lazy-loading: lorsqu'un nœud marqué <code>lazy</code> est ouvert, l'événement <code>tree:lazy</code> est émis.
        On intercepte cet événement pour appeler une route REST (<code>/demo/api/tree-children?node=...</code>) et on remplace le placeholder par les enfants retournés.
    </p>
    <div class="grid md:grid-cols-2 gap-6 items-start">
        <div class="space-y-3">
            <div class="text-sm opacity-70">Sélection simple</div>
            <x-daisy::ui.advanced.tree-view id="demoTreeSingle" selection="single" :persist="true" controlSize="xs" lazyUrl="/demo/api/tree-children" lazyParam="node" :search="true" :searchAuto="false" searchMin="1" searchDebounce="150" searchUrl="/demo/api/tree-search" searchParam="q" :data="[
                ['id' => 'root', 'label' => 'Racine', 'expanded' => true, 'children' => [
                    ['id' => 'a', 'label' => 'Dossier A', 'children' => [
                        ['id' => 'a1', 'label' => 'Fichier A1'],
                        ['id' => 'a2', 'label' => 'Fichier A2'],
                    ]],
                    ['id' => 'b', 'label' => 'Dossier B (lazy)', 'lazy' => true],
                    ['id' => 'c', 'label' => 'Fichier C'],
                ]],
            ]" />
            <div class="text-xs opacity-70">Événements: <code>tree:select</code>, <code>tree:lazy</code></div>
        </div>

        <div class="space-y-3">
            <div class="text-sm opacity-70">Sélection multiple</div>
            <x-daisy::ui.advanced.tree-view id="demoTreeMulti" selection="multiple" :persist="true" controlSize="xs" lazyUrl="/demo/api/tree-children" lazyParam="node" :search="true" :searchAuto="true" searchMin="2" searchDebounce="250" :data="[
                [
                    'id' => '1',
                    'label' => 'Projet Alpha',
                    'expanded' => true,
                    'children' => [
                        [
                            'id' => '1-1',
                            'label' => 'Kit UI',
                            'children' => [
                                ['id' => '1-1-1', 'label' => 'Roadmap.md'],
                                ['id' => '1-1-2', 'label' => 'Changelog.md (désactivé)', 'disabled' => true],
                            ],
                        ],
                        [
                            'id' => '1-2',
                            'label' => 'Site (désactivé)',
                            'disabled' => true,
                            'children' => [
                                ['id' => '1-2-1', 'label' => 'Home.vue'],
                                ['id' => '1-2-2', 'label' => 'About.vue'],
                            ],
                        ],
                        [
                            'id' => '1-3',
                            'label' => 'Sandbox (états mixtes au chargement)',
                            'children' => [
                                ['id' => '1-3-1', 'label' => 'Draft.md', 'selected' => true],
                                ['id' => '1-3-2', 'label' => 'Notes.md', 'selected' => false],
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '2',
                    'label' => 'Projet Beta',
                    'expanded' => true,
                    'children' => [
                        [
                            'id' => '2-1',
                            'label' => 'Documentation',
                            'children' => [
                                ['id' => '2-1-1', 'label' => 'README.md'],
                                ['id' => '2-1-2', 'label' => 'INSTALL.md'],
                            ],
                        ],
                        [
                            'id' => '2-2',
                            'label' => 'Sources',
                            'children' => [
                                ['id' => '2-2-1', 'label' => 'main.js'],
                                ['id' => '2-2-2', 'label' => 'app.vue'],
                            ],
                        ],
                        [
                            'id' => '2-3',
                            'label' => 'Dossier Lazy',
                            'lazy' => true,
                        ],
                    ],
                ],
            ]" />
        </div>
    </div>

    <div class="divider"></div>
    <div class="space-y-2">
        <div class="flex gap-2">
            <button id="btnReadSelected" class="btn btn-primary btn-sm">Lire la sélection (multi)</button>
            <button id="btnExpandB" class="btn btn-ghost btn-sm">Développer B (lazy)</button>
        </div>
        <pre id="selectedOutput" class="mockup-code w-full"><code></code></pre>
    </div>

    <div class="divider"></div>
    <div class="space-y-2">
        <div class="text-sm opacity-70">Exemple d'endpoint REST (Laravel) pour charger les enfants</div>
        <pre class="mockup-code w-full"><code class="language-php">// routes/web.php
Route::get('/demo/api/tree-children', function (\Illuminate\Http\Request $request) {
    $node = (string) $request->query('node', '');
    $data = match ($node) {
        'b' => [
            ['id' => 'b1', 'label' => 'Fichier B1'],
            // Exemple de nœud lazy imbriqué (disabled supporté)
            ['id' => 'b2', 'label' => 'Dossier B2 (lazy, disabled)', 'lazy' => true, 'disabled' => true],
            ['id' => 'b3', 'label' => 'Fichier B3'],
        ],
        'b2' => [
            ['id' => 'b2-1', 'label' => 'Fichier B2-1'],
            ['id' => 'b2-2', 'label' => 'Dossier B2-2 (avec enfants)', 'children' => [
                ['id' => 'b2-2-1', 'label' => 'Fichier B2-2-1'],
                ['id' => 'b2-2-2', 'label' => 'Fichier B2-2-2'],
            ]],
        ],
        'root' => [
            ['id' => 'r1', 'label' => 'Fichier Racine 1'],
            ['id' => 'r2', 'label' => 'Fichier Racine 2'],
        ],
        default => [
            ['id' => $node.'-1', 'label' => 'Fichier '.$node.'-1'],
            ['id' => $node.'-2', 'label' => 'Fichier '.$node.'-2'],
        ],
    };
    return response()->json($data);
})->name('demo.tree.children');</code></pre>
    </div>

    <script>
    (function(){
        document.addEventListener('DOMContentLoaded', () => {
            const single = document.getElementById('demoTreeSingle');
            const multi = document.getElementById('demoTreeMulti');
            const out = document.getElementById('selectedOutput')?.querySelector('code');

            if (single) {
                single.addEventListener('tree:select', () => {});
                // Le lazy-loading est désormais géré automatiquement par le composant via data-lazy-url
            }

            if (multi) {
                multi.addEventListener('tree:select', () => {
                    const ids = window.DaisyTreeView.getSelected(multi);
                    if (out) out.textContent = JSON.stringify(ids);
                });
            }

            const btnRead = document.getElementById('btnReadSelected');
            if (btnRead && multi) btnRead.addEventListener('click', () => {
                const ids = window.DaisyTreeView.getSelected(multi);
                if (out) out.textContent = JSON.stringify(ids);
            });

            const btnExpandB = document.getElementById('btnExpandB');
            if (btnExpandB && single) btnExpandB.addEventListener('click', () => {
                if (single.__treeApi) single.__treeApi.expand('b');
            });
        });
    })();
    </script>
</section>


