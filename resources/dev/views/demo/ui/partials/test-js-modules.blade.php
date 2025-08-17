<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Modules JS (runtime & lazy-loading)</h2>

    <div class="space-y-2">
        <div class="label"><span class="label-text">Objectifs</span></div>
        <ul class="list-disc pl-6">
            <li>Charger les fonctionnalités au plus près du besoin via des imports dynamiques based sur des sélecteurs <code>data-*</code>.</li>
            <li>Préserver les performances (éviter les janks) grâce à la planification (idle, frame suivante) et à des limiteurs de concurrence.</li>
            <li>Exposer une API simple et générique utilisable dans n'importe quel layout.</li>
        </ul>
    </div>

    <div class="space-y-2">
        <div class="label"><span class="label-text">APIs principales (scheduler)</span></div>
        <div class="text-sm opacity-90">
            <div><code>importWhenIdle(selector, importer, timeout=300)</code> · Import différé si le sélecteur existe.</div>
            <div><code>importWhenNearViewport(selector, importer, { rootMargin, threshold })</code> · Import quand l'élément approche du viewport.</div>
            <div><code>initWhenVisible(targets, init, { maxPerFrame, budgetMs, ... })</code> · Initialiser par petits lots à l'apparition.</div>
            <div><code>createLimiter(maxConcurrent)</code> · Limiter le nombre de tâches lourdes simultanées.</div>
            <div><code>postTask(fn, { priority, delay, signal })</code> · Planifier en arrière-plan (fallback setTimeout).</div>
        </div>
    </div>

    <div class="space-y-2">
        <div class="label"><span class="label-text">Patrons d’activation par attributs</span></div>
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Sélecteur</th>
                        <th>Module</th>
                        <th>But</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td><code>[data-treeview="1"]</code></td><td>treeview</td><td>Arbre interactif (sélection, clavier).</td></tr>
                    <tr><td><code>[data-scrollspy="1"]</code></td><td>scrollspy</td><td>Suivi et mise en évidence de section.</td></tr>
                    <tr><td><code>[data-popconfirm]</code>, <code>[data-popconfirm-modal]</code></td><td>popconfirm</td><td>Confirmation accessible.</td></tr>
                    <tr><td><code>[data-popover]</code></td><td>popover</td><td>Infobulle riche contrôlée.</td></tr>
                    <tr><td><code>[data-stepper]</code></td><td>stepper</td><td>Navigation pas-à-pas.</td></tr>
                    <tr><td><code>[data-table-select]</code>≠<code>"none"</code></td><td>table</td><td>Gestion sélection table.</td></tr>
                    <tr><td><code>[data-colorpicker="1"]</code></td><td>color-picker</td><td>Sélecteur de couleur.</td></tr>
                    <tr><td><code>[data-fileinput="1"]</code></td><td>file-input</td><td>Aperçu et contraintes d’upload.</td></tr>
                    <tr><td><code>input[data-inputmask="1"]</code>, <code>input[data-obfuscate="1"]</code></td><td>input-mask</td><td>Masques & obfuscation.</td></tr>
                    <tr><td><code>[data-scrollstatus="1"]</code></td><td>scroll-status</td><td>Statut de scroll / lecture.</td></tr>
                    <tr><td><code>[data-transfer="1"]</code></td><td>transfer</td><td>Listes transférables.</td></tr>
                    <tr><td><code>[data-lightbox="1"]</code></td><td>lightbox</td><td>Galerie lightbox.</td></tr>
                    <tr><td><code>[data-media-gallery="1"]</code></td><td>media-gallery</td><td>Grille média interactive.</td></tr>
                    <tr><td><code>.collapse .code-editor</code>, <code>.collapse trix-editor</code></td><td>lazy-editors</td><td>Chargement de CodeMirror / Trix à l'ouverture.</td></tr>
                    <tr><td><code>[data-chart="1"]</code></td><td>chart</td><td>Chart.js avec thème DaisyUI.</td></tr>
                    <tr><td><code>[data-leaflet="1"]</code></td><td>leaflet</td><td>Carte Leaflet et plug-ins associés.</td></tr>
                    <tr><td><code>.cally</code> ou balises <code>calendar-*</code></td><td>cally</td><td>Web Components calendrier.</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="space-y-2">
        <div class="label"><span class="label-text">Comportements globaux</span></div>
        <ul class="list-disc pl-6 text-sm">
            <li><strong>Sidebar</strong> · <code>[data-sidebar-root] .sidebar-toggle</code> gère l’expansion, persistance <code>localStorage</code>.</li>
            <li><strong>Radios décochables</strong> · <code>data-uncheckable="1"</code> permet de décocher un radio déjà coché.</li>
            <li><strong>Checkbox indéterminées</strong> · <code>data-indeterminate="true"</code> initialise l’état “mixed”.</li>
        </ul>
    </div>

    <div class="space-y-2">
        <div class="label"><span class="label-text">Intégration des assets</span></div>
        <div class="text-sm opacity-90">
            Inclure une seule fois dans votre layout:
            <pre class="mt-2 p-3 bg-base-300 rounded-box overflow-x-auto"><code>@include('daisy::components.partials.assets')</code></pre>
            L’injection peut être contrôlée via <code>config/daisy-kit.php</code> (<code>auto_assets</code>, <code>use_vite</code>, <code>vite_build_directory</code>).
        </div>
    </div>

    <div class="space-y-2">
        <div class="label"><span class="label-text">Bonnes pratiques</span></div>
        <ul class="list-disc pl-6 text-sm">
            <li>Préférez <code>importWhenNearViewport</code> pour les modules lourds (charts, éditeurs, cartes).</li>
            <li>Utilisez <code>createLimiter</code> pour séquencer plusieurs imports coûteux.</li>
            <li>Activez via <code>data-*</code> depuis les composants Blade pour garder le JS générique et réutilisable.</li>
        </ul>
    </div>
</section>


