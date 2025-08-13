<x-daisy::layout.app title="DaisyUI Kit - Demo">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h1 class="text-2xl font-semibold">DaisyUI Kit - Demo</h1>
        <div class="flex items-center gap-3">
            <!-- Sélecteur unique des thèmes DaisyUI (incluant Light/Dark) -->
            <label class="form-control w-64">
                <div class="label"><span class="label-text">Thème DaisyUI</span></div>
                <select id="themeSelect" class="select select-bordered">
                    <option value="light">Light</option>
                    <option value="dark">Dark</option>
                    <option value="cupcake">Cupcake</option>
                    <option value="bumblebee">Bumblebee</option>
                    <option value="emerald">Emerald</option>
                    <option value="corporate">Corporate</option>
                    <option value="synthwave">Synthwave</option>
                    <option value="retro">Retro</option>
                    <option value="cyberpunk">Cyberpunk</option>
                    <option value="valentine">Valentine</option>
                    <option value="halloween">Halloween</option>
                    <option value="garden">Garden</option>
                    <option value="forest">Forest</option>
                    <option value="aqua">Aqua</option>
                    <option value="lofi">Lofi</option>
                    <option value="pastel">Pastel</option>
                    <option value="fantasy">Fantasy</option>
                    <option value="wireframe">Wireframe</option>
                    <option value="black">Black</option>
                    <option value="luxury">Luxury</option>
                    <option value="dracula">Dracula</option>
                    <option value="cmyk">CMYK</option>
                    <option value="autumn">Autumn</option>
                    <option value="business">Business</option>
                    <option value="acid">Acid</option>
                    <option value="lemonade">Lemonade</option>
                    <option value="night">Night</option>
                    <option value="coffee">Coffee</option>
                    <option value="winter">Winter</option>
                </select>
            </label>
        </div>
    </div>
    <script>
        (function() {
            const THEME_KEY = 'daisy-theme';
            const htmlEl = document.documentElement;
            const themeSelect = document.getElementById('themeSelect');
            const controllers = () => Array.from(document.querySelectorAll('.theme-controller'));

            function applyTheme(theme) {
                if (!theme) return;
                htmlEl.setAttribute('data-theme', theme);
                try { localStorage.setItem(THEME_KEY, theme); } catch (_) {}
                controllers().forEach((el) => {
                    if (el.type === 'radio') {
                        el.checked = (el.value === theme);
                    } else if (el.tagName === 'SELECT') {
                        el.value = theme;
                    }
                });
            }

            function readSavedTheme() {
                try { return localStorage.getItem(THEME_KEY); } catch (_) { return null; }
            }

            function init() {
                const saved = readSavedTheme();
                const current = saved || htmlEl.getAttribute('data-theme') || 'light';
                applyTheme(current);
                if (themeSelect) themeSelect.value = current;
            }

            if (themeSelect) {
                themeSelect.addEventListener('change', (e) => applyTheme(e.target.value));
            }
            document.addEventListener('change', (e) => {
                const t = e.target;
                if (t && t.classList && t.classList.contains('theme-controller')) {
                    applyTheme(t.value);
                    if (themeSelect) themeSelect.value = t.value;
                }
            });

            document.addEventListener('DOMContentLoaded', init);
        })();
    </script>

    <!-- Floating Section Navigator (sans Alpine) -->
    <div id="sectionNav" class="fixed bottom-6 right-6 z-50">
        <div id="sectionNavPanel" class="toast toast-end mb-4 hidden">
            <div id="sectionNavBox" class="bg-base-200 rounded-box shadow-lg p-3 w-72">
                <div class="font-semibold mb-2">All sections</div>
                <div class="mb-2">
                    <label class="input input-bordered flex items-center gap-2 w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4 opacity-70"><path fill-rule="evenodd" d="M10.5 3.75a6.75 6.75 0 1 0 3.897 12.303l3.775 3.775a.75.75 0 1 0 1.06-1.06l-3.775-3.776A6.75 6.75 0 0 0 10.5 3.75ZM5.25 10.5a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Z" clip-rule="evenodd"/></svg>
                        <input id="sectionNavSearch" type="text" placeholder="Rechercher..." class="grow" autocomplete="off" />
                    </label>
                </div>
                <ul id="sectionNavList" class="menu"></ul>
            </div>
        </div>
        <button id="sectionNavBtn" class="btn btn-primary btn-circle shadow-lg" aria-label="Open section navigator">
            <svg id="sectionNavIconOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            <svg id="sectionNavIconClose" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 hidden"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>
    <script>
        (function(){
            const root = document.getElementById('sectionNav');
            const panel = document.getElementById('sectionNavPanel');
            const box = document.getElementById('sectionNavBox');
            const list = document.getElementById('sectionNavList');
            const search = document.getElementById('sectionNavSearch');
            const btn = document.getElementById('sectionNavBtn');
            const iconOpen = document.getElementById('sectionNavIconOpen');
            const iconClose = document.getElementById('sectionNavIconClose');
            if (!root || !panel || !btn) return;
            function normalizeText(t){ return (t || '').toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu,''); }
            function collectSections(){
                const wrap = document.querySelector('div.space-y-10');
                const sections = wrap ? Array.from(wrap.querySelectorAll('section')) : [];
                const seen = new Set();
                const data = [];
                for (const sec of sections) {
                    const h2 = sec.querySelector('h2');
                    if (!h2) continue;
                    const label = h2.textContent.trim();
                    let id = sec.id || label.toLowerCase().trim().replace(/[^\w\s-]/g,'').replace(/\s+/g,'-');
                    let base = id, i = 2;
                    while (seen.has(id)) { id = base + '-' + (i++); }
                    seen.add(id);
                    if (!sec.id) sec.id = id;
                    data.push({ id, label, labelKey: normalizeText(label) });
                }
                // Tri alphabétique insensible à la casse/accents
                data.sort((a,b) => a.label.localeCompare(b.label, 'fr', { sensitivity: 'base' }));
                return data;
            }
            let cachedData = [];
            function buildList(filter = ''){
                if (!list) return;
                if (!cachedData.length) cachedData = collectSections();
                list.innerHTML = '';
                const key = normalizeText(filter);
                cachedData.filter((d) => !key || d.labelKey.includes(key)).forEach((d) => {
                    const li = document.createElement('li');
                    const a = document.createElement('a');
                    a.href = '#' + d.id;
                    a.textContent = d.label;
                    li.appendChild(a);
                    list.appendChild(li);
                });
            }

            function adjustOverflow(){
                if (!box) return;
                const viewport = window.innerHeight;
                const maxH = Math.max(240, Math.floor(viewport * 0.7));
                box.style.maxHeight = maxH + 'px';
                const needScroll = box.scrollHeight > box.clientHeight;
                box.style.overflowY = needScroll ? 'auto' : 'visible';
            }

            function toggle(open){
                const willOpen = open ?? panel.classList.contains('hidden');
                panel.classList.toggle('hidden', !willOpen);
                iconOpen.classList.toggle('hidden', willOpen);
                iconClose.classList.toggle('hidden', !willOpen);
                if (willOpen) {
                    cachedData = []; // force recalcul
                    if (search) search.value = '';
                    buildList();
                    adjustOverflow();
                    // Focus le champ de recherche après ouverture
                    if (search) setTimeout(() => search.focus(), 0);
                }
            }
            btn.addEventListener('click', () => toggle());
            panel.addEventListener('click', (e) => {
                if (e.target.tagName === 'A') toggle(false);
            });
            document.addEventListener('click', (e) => {
                if (!root.contains(e.target)) toggle(false);
            });
            window.addEventListener('resize', adjustOverflow);
            if (search) search.addEventListener('input', () => buildList(search.value));
            // Raccourci clavier "/" pour ouvrir et focaliser la recherche
            document.addEventListener('keydown', (e) => {
                if (e.key === '/' && !e.ctrlKey && !e.metaKey && !e.altKey) {
                    if (panel.classList.contains('hidden')) toggle(true);
                    if (search) { e.preventDefault(); search.focus(); }
                }
            });
        })();
    </script>

    <div class="space-y-10">
        <!-- Components / Actions -->
        <section>
            <h2 class="text-xl font-semibold">Components · Actions</h2>
            <div class="space-y-6">
                @include('daisy-dev::demo.partials.test-buttons')
                @include('daisy-dev::demo.partials.test-dropdown')
                @include('daisy-dev::demo.partials.test-modal')
                @include('daisy-dev::demo.partials.test-swap')
                @include('daisy-dev::demo.partials.test-theme-controller')
            </div>
        </section>

        <!-- Components / Data display -->
        <section>
            <h2 class="text-xl font-semibold">Components · Data display</h2>
            <div class="space-y-6">
                @include('daisy-dev::demo.partials.test-accordion')
                @include('daisy-dev::demo.partials.test-avatars')
                @include('daisy-dev::demo.partials.test-badges')
                @include('daisy-dev::demo.partials.test-card')
                @include('daisy-dev::demo.partials.test-carousel')
                @include('daisy-dev::demo.partials.test-chat')
                @include('daisy-dev::demo.partials.test-collapse')
                @include('daisy-dev::demo.partials.test-countdown')
                @include('daisy-dev::demo.partials.test-diff')
                @include('daisy-dev::demo.partials.test-kbd')
                @include('daisy-dev::demo.partials.test-list')
                @include('daisy-dev::demo.partials.test-stat')
                @include('daisy-dev::demo.partials.test-status')
                @include('daisy-dev::demo.partials.test-table')
                @include('daisy-dev::demo.partials.test-timeline')
                @include('daisy-dev::demo.partials.test-lightbox')
                @include('daisy-dev::demo.partials.test-media-gallery')
                @include('daisy-dev::demo.partials.test-embeds')
            </div>
        </section>

        <!-- Components / Navigation -->
        <section>
            <h2 class="text-xl font-semibold">Components · Navigation</h2>
            <div class="space-y-6">
                @include('daisy-dev::demo.partials.test-breadcrumbs')
                @include('daisy-dev::demo.partials.test-dock')
                @include('daisy-dev::demo.partials.test-links')
                @include('daisy-dev::demo.partials.test-menu')
                @include('daisy-dev::demo.partials.test-navbar')
                @include('daisy-dev::demo.partials.test-pagination')
                @include('daisy-dev::demo.partials.test-steps')
                @include('daisy-dev::demo.partials.test-tabs')
                @include('daisy-dev::demo.partials.test-scrollspy')
                @include('daisy-dev::demo.partials.test-tree-view')
            </div>
        </section>

        <!-- Components / Feedback -->
        <section>
            <h2 class="text-xl font-semibold">Components · Feedback</h2>
            <div class="space-y-6">
                @include('daisy-dev::demo.partials.test-alert')
                @include('daisy-dev::demo.partials.test-loading')
                @include('daisy-dev::demo.partials.test-progress')
                @include('daisy-dev::demo.partials.test-radial-progress')
                @include('daisy-dev::demo.partials.test-skeleton')
                @include('daisy-dev::demo.partials.test-tooltip')
                @include('daisy-dev::demo.partials.test-popover')
                @include('daisy-dev::demo.partials.test-popconfirm')
                @include('daisy-dev::demo.partials.test-scroll-status')
            </div>
        </section>

        <!-- Components / Data input -->
        <section>
            <h2 class="text-xl font-semibold">Components · Data input</h2>
            <div class="space-y-6">
                @include('daisy-dev::demo.partials.test-calendar')
                @include('daisy-dev::demo.partials.test-checkbox')
                @include('daisy-dev::demo.partials.test-fieldset')
                @include('daisy-dev::demo.partials.test-file-input')
                @include('daisy-dev::demo.partials.test-filter')
                @include('daisy-dev::demo.partials.test-label')
                @include('daisy-dev::demo.partials.test-radio')
                @include('daisy-dev::demo.partials.test-range')
                @include('daisy-dev::demo.partials.test-rating')
                @include('daisy-dev::demo.partials.test-selects')
                @include('daisy-dev::demo.partials.test-inputs')
                @include('daisy-dev::demo.partials.test-textareas')
                @include('daisy-dev::demo.partials.test-toggle')
                @include('daisy-dev::demo.partials.test-validator')
                @include('daisy-dev::demo.partials.test-code-editor')
                @include('daisy-dev::demo.partials.test-color-picker')
                @include('daisy-dev::demo.partials.test-input-mask')
                @include('daisy-dev::demo.partials.test-transfer')
            </div>
        </section>

        <!-- Components / Layout -->
        <section>
            <h2 class="text-xl font-semibold">Components · Layout</h2>
            <div class="space-y-6">
                @include('daisy-dev::demo.partials.test-divider')
                @include('daisy-dev::demo.partials.test-drawer')
                @include('daisy-dev::demo.partials.test-footer')
                @include('daisy-dev::demo.partials.test-hero')
                @include('daisy-dev::demo.partials.test-indicator')
                @include('daisy-dev::demo.partials.test-join')
                @include('daisy-dev::demo.partials.test-mask')
                @include('daisy-dev::demo.partials.test-stack')
            </div>
        </section>

        <!-- Components / Mockup -->
        <section>
            <h2 class="text-xl font-semibold">Components · Mockup</h2>
            <div class="space-y-6">
                @include('daisy-dev::demo.partials.test-mockup-browser')
                @include('daisy-dev::demo.partials.test-mockup-code')
                @include('daisy-dev::demo.partials.test-mockup-phone')
                @include('daisy-dev::demo.partials.test-mockup-window')
            </div>
        </section>
    </div>

    <script>
    // Marquer toutes les images de la démo en lazy pour éviter que l'onglet reste en "chargement"
    (function(){
      document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('img:not([loading])').forEach((img) => {
          img.setAttribute('loading', 'lazy');
          img.setAttribute('decoding', 'async');
        });
      });
    })();
    </script>
</x-daisy::layout.app>


