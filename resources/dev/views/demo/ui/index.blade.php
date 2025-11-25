@php
    $prefix = config('daisy-kit.docs.prefix', 'docs');
@endphp
<x-daisy::layout.app title="DaisyUI Kit - Demo" :container="false">
    {{-- Navbar avec navigation Docs/Démo/Template --}}
    <x-daisy::ui.navigation.navbar bg="base-100" shadow="sm" fixed="false" class="border-b border-base-200">
        <x-slot:start>
            <h1 class="text-lg font-semibold">DaisyUI Kit</h1>
        </x-slot:start>
        <x-slot:center>
            <div class="join">
                @if (Route::has('daisy.docs.index'))
                    <a href="{{ route('daisy.docs.index') }}" class="btn btn-sm join-item btn-ghost">Docs</a>
                @else
                    <a href="/{{ $prefix }}" class="btn btn-sm join-item btn-ghost">Docs</a>
                @endif
                <a href="{{ route('demo') }}" class="btn btn-sm join-item btn-ghost btn-active">Démo</a>
                @if (Route::has('daisy.docs.templates'))
                    <a href="{{ route('daisy.docs.templates') }}" class="btn btn-sm join-item btn-ghost">Template</a>
                @else
                    <a href="/{{ $prefix }}/templates" class="btn btn-sm join-item btn-ghost">Template</a>
                @endif
            </div>
        </x-slot:center>
        <x-slot:end>
            <x-daisy::ui.advanced.theme-controller 
                variant="dropdown" 
                :themes="['light', 'dark', 'cupcake', 'bumblebee', 'emerald', 'corporate', 'synthwave', 'retro', 'cyberpunk', 'valentine', 'halloween', 'garden', 'forest', 'aqua', 'lofi', 'pastel', 'fantasy', 'wireframe', 'black', 'luxury', 'dracula', 'cmyk', 'autumn', 'business', 'acid', 'lemonade', 'night', 'coffee', 'winter']"
                label="Theme"
                size="sm"
            />
        </x-slot:end>
    </x-daisy::ui.navigation.navbar>

    <div class="container mx-auto px-4 sm:px-6 py-4 sm:py-6">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <h1 class="text-xl sm:text-2xl font-semibold">DaisyUI Kit - Demo</h1>
        </div>
    
    <!-- Sélecteur de thème flottant -->
    <div id="themePicker" class="fixed top-4 right-4 z-50 hidden md:block">
        <div id="themePickerBox" class="bg-base-100 rounded-lg shadow-lg p-3 border border-base-300 w-56 sm:w-64 max-w-[calc(100vw-2rem)]">
            <label class="form-control w-full">
                <div class="label"><span class="label-text text-xs">Thème DaisyUI</span></div>
                <select id="themeSelect" class="select select-bordered select-sm w-full">
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
            const themePicker = document.getElementById('themePicker');
            const themePickerBox = document.getElementById('themePickerBox');
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
                adjustThemePicker();
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

            function adjustThemePicker(){
                if (!themePickerBox) return;
                const vw = window.innerWidth;
                // Max width: viewport - 2rem
                const maxW = Math.max(240, vw - 32);
                themePickerBox.style.maxWidth = maxW + 'px';
                // Décaler si dépasse à droite malgré right-4
                const r = themePickerBox.getBoundingClientRect();
                const shift = Math.max(0, r.right - vw + 8);
                themePicker.style.transform = shift ? `translateX(-${shift}px)` : '';
            }

            window.addEventListener('resize', adjustThemePicker);

            document.addEventListener('DOMContentLoaded', init);
        })();
    </script>

    <!-- Floating Section Navigator (sans Alpine) -->
    <div id="sectionNav" class="fixed bottom-6 right-6 z-50 hidden md:block">
        <div id="sectionNavPanel" class="absolute bottom-16 right-0 hidden">
            <div id="sectionNavBox" class="bg-base-200 rounded-box shadow-lg p-3 w-72 sm:w-80 max-w-[calc(100vw-2rem)]">
                <div class="font-semibold mb-2">Sections</div>
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
            function slugify(text){ return (text || '').toLowerCase().trim().replace(/[^\w\s-]/g,'').replace(/\s+/g,'-'); }
            function collectSections(){
                const wrap = document.querySelector('div.space-y-10');
                const sections = wrap ? Array.from(wrap.querySelectorAll(':scope > section')) : [];
                const seen = new Set();
                const data = [];
                for (const sec of sections) {
                    const h2 = sec.querySelector('h2');
                    if (!h2) continue;
                    const label = h2.textContent.trim();
                    let id = sec.id || slugify(label);
                    let base = id, i = 2;
                    while (seen.has(id)) { id = base + '-' + (i++); }
                    seen.add(id);
                    if (!sec.id) sec.id = id;
                    // On ne conserve que les sections de premier niveau, dans l'ordre du document
                    data.push({ id, label, labelKey: normalizeText(label), level: 1 });
                }
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
                const viewportH = window.innerHeight;
                const viewportW = window.innerWidth;
                // Hauteur max à 70% de l'écran, min 240px
                const maxH = Math.max(240, Math.floor(viewportH * 0.7));
                box.style.maxHeight = maxH + 'px';
                // Largeur max: bord à 1rem
                box.style.maxWidth = Math.max(240, viewportW - 32) + 'px';
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
                    // Si le panneau déborde horizontalement, le décaler à gauche
                    const pr = panel.getBoundingClientRect();
                    const shift = Math.max(0, pr.right - window.innerWidth + 16);
                    panel.style.transform = shift ? `translateX(-${shift}px)` : '';
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
            // Observer: si le contenu change, invalider le cache
            const wrap = document.querySelector('div.space-y-10');
            if (wrap) {
                const mo = new MutationObserver(() => { cachedData = []; if (!panel.classList.contains('hidden')) buildList(search?.value || ''); });
                mo.observe(wrap, { childList: true, subtree: true });
            }
        })();
    </script>

    <div class="space-y-10">
        <!-- Components / Actions -->
        <section>
            <h2 class="text-xl font-semibold">Components · Actions</h2>
            <div class="space-y-6">
                @include('daisy-dev::demo.ui.partials.test-buttons')
                @include('daisy-dev::demo.ui.partials.test-dropdown')
                @include('daisy-dev::demo.ui.partials.test-modal')
                @include('daisy-dev::demo.ui.partials.test-swap')
                @include('daisy-dev::demo.ui.partials.test-theme-controller')
                @include('daisy-dev::demo.ui.partials.test-login-buttons')
            </div>
        </section>

        <!-- Components / Data display -->
        <section>
            <h2 class="text-xl font-semibold">Components · Data display</h2>
            <div class="space-y-6">
                @include('daisy-dev::demo.ui.partials.test-accordion')
                @include('daisy-dev::demo.ui.partials.test-avatars')
                @include('daisy-dev::demo.ui.partials.test-badges')
                @include('daisy-dev::demo.ui.partials.test-card')
                @include('daisy-dev::demo.ui.partials.test-carousel')
                @include('daisy-dev::demo.ui.partials.test-chat')
                @include('daisy-dev::demo.ui.partials.test-collapse')
                @include('daisy-dev::demo.ui.partials.test-countdown')
                @include('daisy-dev::demo.ui.partials.test-diff')
                @include('daisy-dev::demo.ui.partials.test-icons')
                @include('daisy-dev::demo.ui.partials.test-kbd')
                @include('daisy-dev::demo.ui.partials.test-list')
                @include('daisy-dev::demo.ui.partials.test-stat')
                @include('daisy-dev::demo.ui.partials.test-status')
                @include('daisy-dev::demo.ui.partials.test-table')
                @include('daisy-dev::demo.ui.partials.test-timeline')
                @include('daisy-dev::demo.ui.partials.test-lightbox')
                @include('daisy-dev::demo.ui.partials.test-media-gallery')
                {{-- @include('daisy-dev::demo.ui.partials.test-embeds') --}}
            </div>
        </section>

        <!-- Components / Navigation -->
        <section>
            <h2 class="text-xl font-semibold">Components · Navigation</h2>
            <div class="space-y-6">
                @include('daisy-dev::demo.ui.partials.test-breadcrumbs')
                @include('daisy-dev::demo.ui.partials.test-dock')
                @include('daisy-dev::demo.ui.partials.test-links')
                @include('daisy-dev::demo.ui.partials.test-menu')
                @include('daisy-dev::demo.ui.partials.test-navbar')
                @include('daisy-dev::demo.ui.partials.test-pagination')
                @include('daisy-dev::demo.ui.partials.test-steps')
                @include('daisy-dev::demo.ui.partials.test-stepper')
                @include('daisy-dev::demo.ui.partials.test-tabs')
                @include('daisy-dev::demo.ui.partials.test-scrollspy')
                @include('daisy-dev::demo.ui.partials.test-tree-view')
            </div>
        </section>

        <!-- Components / Feedback -->
        <section>
            <h2 class="text-xl font-semibold">Components · Feedback</h2>
            <div class="space-y-6">
                @include('daisy-dev::demo.ui.partials.test-callout')
                @include('daisy-dev::demo.ui.partials.test-alert')
                @include('daisy-dev::demo.ui.partials.test-loading')
                @include('daisy-dev::demo.ui.partials.test-progress')
                @include('daisy-dev::demo.ui.partials.test-radial-progress')
                @include('daisy-dev::demo.ui.partials.test-skeleton')
                @include('daisy-dev::demo.ui.partials.test-tooltip')
                @include('daisy-dev::demo.ui.partials.test-popover')
                @include('daisy-dev::demo.ui.partials.test-popconfirm')
                @include('daisy-dev::demo.ui.partials.test-scroll-status')
                @include('daisy-dev::demo.ui.partials.test-notifications')
                @include('daisy-dev::demo.ui.partials.test-onboarding')
            </div>
        </section>

        <!-- Components / Data input -->
        <section>
            <h2 class="text-xl font-semibold">Components · Data input</h2>
            <div class="space-y-6">
                @include('daisy-dev::demo.ui.partials.test-checkbox')
                @include('daisy-dev::demo.ui.partials.test-fieldset')
                @include('daisy-dev::demo.ui.partials.test-file-input')
                @include('daisy-dev::demo.ui.partials.test-filter')
                @include('daisy-dev::demo.ui.partials.test-label')
                @include('daisy-dev::demo.ui.partials.test-radio')
                @include('daisy-dev::demo.ui.partials.test-range')
                @include('daisy-dev::demo.ui.partials.test-rating')
                @include('daisy-dev::demo.ui.partials.test-selects')
                @include('daisy-dev::demo.ui.partials.test-inputs')
                @include('daisy-dev::demo.ui.partials.test-textareas')
                @include('daisy-dev::demo.ui.partials.test-toggle')
                @include('daisy-dev::demo.ui.partials.test-validator')
                @include('daisy-dev::demo.ui.partials.test-color-picker')
                @include('daisy-dev::demo.ui.partials.test-input-mask')
                @include('daisy-dev::demo.ui.partials.test-transfer')
                @include('daisy-dev::demo.ui.partials.test-sign')
            </div>
        </section>

        <!-- Components / Layout -->
        <section>
            <h2 class="text-xl font-semibold">Components · Layout</h2>
            <div class="space-y-6">
                @include('daisy-dev::demo.ui.partials.test-divider')
                @include('daisy-dev::demo.ui.partials.test-drawer')
                @include('daisy-dev::demo.ui.partials.test-footer')
                @include('daisy-dev::demo.ui.partials.test-hero')
                @include('daisy-dev::demo.ui.partials.test-indicator')
                @include('daisy-dev::demo.ui.partials.test-copyable')
                @include('daisy-dev::demo.ui.partials.test-join')
                @include('daisy-dev::demo.ui.partials.test-mask')
                @include('daisy-dev::demo.ui.partials.test-stack')
                @include('daisy-dev::demo.ui.partials.test-grid-layout')
                @include('daisy-dev::demo.ui.partials.test-layouts')
            </div>
        </section>

        <!-- Components / Mockup -->
        <section>
            <h2 class="text-xl font-semibold">Components · Mockup</h2>
            <div class="space-y-6">
                @include('daisy-dev::demo.ui.partials.test-mockup-browser')
                @include('daisy-dev::demo.ui.partials.test-mockup-code')
                @include('daisy-dev::demo.ui.partials.test-mockup-phone')
                @include('daisy-dev::demo.ui.partials.test-mockup-window')
            </div>
        </section>

        <!-- Catalogue auto (piloté par le manifeste) -->
        <section>
            <h2 class="text-xl font-semibold">Catalogue · Auto (manifeste)</h2>
            @php
                $manifestPath = base_path('resources/dev/data/components.json');
                $components = [];
                if (is_file($manifestPath)) {
                    $json = json_decode(file_get_contents($manifestPath), true);
                    $components = $json['components'] ?? [];
                }
                $byCategory = [];
                foreach ($components as $c) {
                    $cat = $c['category'] ?? 'other';
                    $byCategory[$cat][] = $c;
                }
                ksort($byCategory);
            @endphp
            <div class="space-y-6">
                @foreach($byCategory as $cat => $items)
                    <div class="space-y-2">
                        <div class="font-medium opacity-70">{{ ucfirst(str_replace('-', ' ', $cat)) }}</div>
                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($items as $it)
                                <div class="card bg-base-100 border border-base-300">
                                    <div class="card-body p-4">
                                        <div class="card-title text-base">{{ $it['name'] ?? '' }}</div>
                                        <div class="text-xs opacity-70 break-all">{{ $it['view'] ?? '' }}</div>
                                        <div class="mt-2 flex flex-wrap gap-1">
                                            @foreach(($it['tags'] ?? []) as $tag)
                                                <span class="badge badge-ghost badge-sm">{{ $tag }}</span>
                                            @endforeach
                                            @if(!empty($it['jsModule']))
                                                <span class="badge badge-info badge-sm">js: {{ $it['jsModule'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>


        <!-- Extensions · Composants avec dépendances externes -->
        <section>
            <h2 class="text-xl font-semibold">Extensions · Dépendances externes</h2>
            <div class="space-y-6">
                @include('daisy-dev::demo.ui.partials.test-charts')
                @include('daisy-dev::demo.ui.partials.test-leaflet')
                @include('daisy-dev::demo.ui.partials.test-calendar')
                {{-- @include('daisy-dev::demo.ui.partials.test-calendar-full') --}}
                @include('daisy-dev::demo.ui.partials.test-wysiwyg')
                @include('daisy-dev::demo.ui.partials.test-code-editor')
            </div>
        </section>


        <!-- JavaScript / Runtime -->
        <section>
            <h2 class="text-xl font-semibold">JavaScript · Runtime</h2>
            <div class="space-y-6">
                @include('daisy-dev::demo.ui.partials.test-js-modules')
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


