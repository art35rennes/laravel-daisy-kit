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
            const btn = document.getElementById('sectionNavBtn');
            const iconOpen = document.getElementById('sectionNavIconOpen');
            const iconClose = document.getElementById('sectionNavIconClose');
            if (!root || !panel || !btn) return;
            function buildList(){
                if (!list) return;
                list.innerHTML = '';
                const seen = new Set();
                const wrap = document.querySelector('div.space-y-10');
                const sections = wrap ? wrap.querySelectorAll('section') : [];
                sections.forEach((sec) => {
                    const h2 = sec.querySelector('h2');
                    if (!h2) return;
                    let id = sec.id || h2.textContent.toLowerCase().trim().replace(/[^\w\s-]/g,'').replace(/\s+/g,'-');
                    let base = id, i = 2;
                    while (seen.has(id)) { id = base + '-' + (i++); }
                    seen.add(id);
                    if (!sec.id) sec.id = id;
                    const li = document.createElement('li');
                    const a = document.createElement('a');
                    a.href = '#' + id;
                    a.textContent = h2.textContent.trim();
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
                if (willOpen) { buildList(); adjustOverflow(); }
            }
            btn.addEventListener('click', () => toggle());
            panel.addEventListener('click', (e) => {
                if (e.target.tagName === 'A') toggle(false);
            });
            document.addEventListener('click', (e) => {
                if (!root.contains(e.target)) toggle(false);
            });
            window.addEventListener('resize', adjustOverflow);
        })();
    </script>

    <div class="space-y-10">
        @include('daisy-dev::demo.partials.buttons')
        @include('daisy-dev::demo.partials.stepper')
        @include('daisy-dev::demo.partials.inputs')
        @include('daisy-dev::demo.partials.selects')
        @include('daisy-dev::demo.partials.textareas')
        @include('daisy-dev::demo.partials.checkbox')
        @include('daisy-dev::demo.partials.radio')
        @include('daisy-dev::demo.partials.toggle')
        @include('daisy-dev::demo.partials.loading')
        @include('daisy-dev::demo.partials.links')
        @include('daisy-dev::demo.partials.badges')
        @include('daisy-dev::demo.partials.avatars')
        @include('daisy-dev::demo.partials.divider')
        @include('daisy-dev::demo.partials.label')
        @include('daisy-dev::demo.partials.kbd')
        @include('daisy-dev::demo.partials.breadcrumbs')
        @include('daisy-dev::demo.partials.dropdown')
        @include('daisy-dev::demo.partials.alert')
        @include('daisy-dev::demo.partials.notifications')
        @include('daisy-dev::demo.partials.modal')
        @include('daisy-dev::demo.partials.icons')
        @include('daisy-dev::demo.partials.login-buttons')
        @include('daisy-dev::demo.partials.card')
        @include('daisy-dev::demo.partials.indicator')
        @include('daisy-dev::demo.partials.progress')
        @include('daisy-dev::demo.partials.radial-progress')
        @include('daisy-dev::demo.partials.rating')
        @include('daisy-dev::demo.partials.select-advanced')
        @include('daisy-dev::demo.partials.stat')
        @include('daisy-dev::demo.partials.skeleton')
        @include('daisy-dev::demo.partials.stack')
        @include('daisy-dev::demo.partials.timeline')
        @include('daisy-dev::demo.partials.steps')
        @include('daisy-dev::demo.partials.tooltip')
        @include('daisy-dev::demo.partials.popover')
        @include('daisy-dev::demo.partials.popconfirm')
        @include('daisy-dev::demo.partials.theme-controller')
        @include('daisy-dev::demo.partials.collapse')
        @include('daisy-dev::demo.partials.accordion')
        @include('daisy-dev::demo.partials.tabs')
        @include('daisy-dev::demo.partials.swap')
        @include('daisy-dev::demo.partials.menu')
        @include('daisy-dev::demo.partials.navbar')
        @include('daisy-dev::demo.partials.drawer')
        @include('daisy-dev::demo.partials.pagination')
        @include('daisy-dev::demo.partials.table')
        @include('daisy-dev::demo.partials.list')
        @include('daisy-dev::demo.partials.join')
        @include('daisy-dev::demo.partials.fieldset')
        @include('daisy-dev::demo.partials.file-input')
        @include('daisy-dev::demo.partials.range')
        @include('daisy-dev::demo.partials.mask')
        @include('daisy-dev::demo.partials.hero')
        @include('daisy-dev::demo.partials.footer')
        @include('daisy-dev::demo.partials.carousel')
        @include('daisy-dev::demo.partials.chat')
        @include('daisy-dev::demo.partials.countdown')
        @include('daisy-dev::demo.partials.diff')
        @include('daisy-dev::demo.partials.dock')
        @include('daisy-dev::demo.partials.mockup-browser')
        @include('daisy-dev::demo.partials.mockup-code')
        @include('daisy-dev::demo.partials.mockup-phone')
        @include('daisy-dev::demo.partials.mockup-window')
        @include('daisy-dev::demo.partials.status')
        @include('daisy-dev::demo.partials.calendar')
        @include('daisy-dev::demo.partials.filter')
        @include('daisy-dev::demo.partials.color-picker')
        @include('daisy-dev::demo.partials.validator')
        @include('daisy-dev::demo.partials.tree-view')
        @include('daisy-dev::demo.partials.code-editor')
        @include('daisy-dev::demo.partials.scrollspy')
        @include('daisy-dev::demo.partials.lightbox')
        @include('daisy-dev::demo.partials.media-gallery')
        @include('daisy-dev::demo.partials.input-mask')
        @include('daisy-dev::demo.partials.embeds')
        @include('daisy-dev::demo.partials.scroll-status')
        @include('daisy-dev::demo.partials.transfer')
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


