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
                // synchroniser les radios .theme-controller
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
            // Quand on clique sur les contrôleurs daisyUI, synchroniser le select + storage
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
        <!-- Buttons -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Buttons</h2>
            <div class="space-y-3">
                <div class="flex flex-wrap gap-2">
                    <x-daisy::ui.button color="primary">Primary</x-daisy::ui.button>
                    <x-daisy::ui.button color="secondary">Secondary</x-daisy::ui.button>
                    <x-daisy::ui.button color="accent">Accent</x-daisy::ui.button>
                    <x-daisy::ui.button color="neutral">Neutral</x-daisy::ui.button>
                    <x-daisy::ui.button color="info">Info</x-daisy::ui.button>
                    <x-daisy::ui.button color="success">Success</x-daisy::ui.button>
                    <x-daisy::ui.button color="warning">Warning</x-daisy::ui.button>
                    <x-daisy::ui.button color="error">Error</x-daisy::ui.button>
                </div>
                <div class="flex flex-wrap gap-2">
                    <x-daisy::ui.button variant="outline" color="primary">Outline</x-daisy::ui.button>
                    <x-daisy::ui.button variant="ghost" color="primary">Ghost</x-daisy::ui.button>
                    <x-daisy::ui.button variant="link" color="primary">Link</x-daisy::ui.button>
                    <x-daisy::ui.button variant="soft" color="primary">Soft</x-daisy::ui.button>
                    <x-daisy::ui.button variant="dash" color="primary">Dash</x-daisy::ui.button>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-daisy::ui.button size="xs" color="primary">XS</x-daisy::ui.button>
                    <x-daisy::ui.button size="sm" color="primary">SM</x-daisy::ui.button>
                    <x-daisy::ui.button size="md" color="primary">MD</x-daisy::ui.button>
                    <x-daisy::ui.button size="lg" color="primary">LG</x-daisy::ui.button>
                    <x-daisy::ui.button size="xl" color="primary">XL</x-daisy::ui.button>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-daisy::ui.button class="btn-xs sm:btn-sm md:btn-md lg:btn-lg xl:btn-xl">Responsive</x-daisy::ui.button>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-daisy::ui.button color="primary" :loading="true">Loading</x-daisy::ui.button>
                    <x-daisy::ui.button color="primary" :active="true">Active</x-daisy::ui.button>
                    <x-daisy::ui.button color="primary" :noAnimation="true">No animation</x-daisy::ui.button>
                    <x-daisy::ui.button disabled>Disabled</x-daisy::ui.button>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-daisy::ui.button color="primary" :wide="true">Wide</x-daisy::ui.button>
                    <x-daisy::ui.button color="primary" :block="true">Block</x-daisy::ui.button>
                    <x-daisy::ui.button color="primary" :circle="true">
                        <x-slot:icon>
                            <x-heroicon-o-heart class="h-5 w-5" />
                        </x-slot:icon>
                    </x-daisy::ui.button>
                    <x-daisy::ui.button color="primary" :square="true">
                        <x-slot:icon>
                            <x-heroicon-o-x-mark class="h-5 w-5" />
                        </x-slot:icon>
                    </x-daisy::ui.button>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-daisy::ui.button color="primary">
                        <x-slot:icon>
                            <x-heroicon-o-arrow-right class="h-5 w-5" />
                        </x-slot:icon>
                        Icône à gauche
                    </x-daisy::ui.button>
                    <x-daisy::ui.button variant="link" color="primary">
                        Icône à droite
                        <x-slot:iconRight>
                            <x-lucide-external-link class="h-5 w-5" />
                        </x-slot:iconRight>
                    </x-daisy::ui.button>
                </div>
            </div>
        </section>

        <!-- Inputs -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Inputs</h2>
            <div class="space-y-3">
                <div class="grid md:grid-cols-3 gap-3">
                    <div class="form-control">
                        <x-daisy::ui.label for="email" value="Email" />
                        <x-daisy::ui.input id="email" type="email" placeholder="john@doe.dev" />
                    </div>
                    <div class="form-control">
                        <x-daisy::ui.label for="name" value="Name" />
                        <x-daisy::ui.input id="name" placeholder="Jane Doe" variant="ghost" />
                    </div>
                    <div class="form-control">
                        <x-daisy::ui.label for="password" value="Password" />
                        <x-daisy::ui.input id="password" type="password" color="primary" />
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <x-daisy::ui.input size="xs" placeholder="Xsmall" />
                    <x-daisy::ui.input size="sm" placeholder="Small" />
                    <x-daisy::ui.input size="md" placeholder="Medium" />
                    <x-daisy::ui.input size="lg" placeholder="Large" />
                    <x-daisy::ui.input size="xl" placeholder="Xlarge" />
                </div>
                <div class="grid md:grid-cols-3 gap-3">
                    <x-daisy::ui.input placeholder="Disabled" disabled />
                    <x-daisy::ui.input placeholder="Error" color="error" />
                    <x-daisy::ui.input placeholder="Success" color="success" />
                </div>

                <!-- Input-wrapper avec contenu (préfixe/suffixe) -->
                <label class="input">
                    <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></g></svg>
                    <input type="search" class="grow" placeholder="Search" />
                    <kbd class="kbd kbd-sm">⌘</kbd>
                    <kbd class="kbd kbd-sm">K</kbd>
                </label>
                <label class="input">
                    Path
                    <input type="text" class="grow" placeholder="src/app/" />
                    <span class="badge badge-neutral badge-xs">Optional</span>
                </label>
            </div>
        </section>

        <!-- Selects -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Selects</h2>
            <div class="space-y-3">
                <div class="grid md:grid-cols-3 gap-3">
                    <x-daisy::ui.select variant="bordered">
                        <option value="">Select…</option>
                        <option>France</option>
                        <option>Belgium</option>
                        <option>Canada</option>
                    </x-daisy::ui.select>
                    <x-daisy::ui.select variant="ghost">
                        <option>Ghost</option>
                    </x-daisy::ui.select>
                    <x-daisy::ui.select disabled>
                        <option>Disabled</option>
                    </x-daisy::ui.select>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <x-daisy::ui.select size="sm">
                        <option>Small</option>
                    </x-daisy::ui.select>
                    <x-daisy::ui.select>
                        <option>Medium</option>
                    </x-daisy::ui.select>
                    <x-daisy::ui.select size="lg">
                        <option>Large</option>
                    </x-daisy::ui.select>
                </div>
            </div>
        </section>

        <!-- Textareas -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Textareas</h2>
            <div class="space-y-3">
                <div class="grid md:grid-cols-3 gap-3">
                    <x-daisy::ui.textarea rows="3" placeholder="Default" />
                    <x-daisy::ui.textarea rows="3" variant="ghost" placeholder="Ghost" />
                    <x-daisy::ui.textarea rows="3" color="primary" placeholder="Primary" />
                </div>
                <div class="grid grid-cols-5 gap-3">
                    <x-daisy::ui.textarea size="xs" rows="2" placeholder="Xsmall" />
                    <x-daisy::ui.textarea size="sm" rows="2" placeholder="Small" />
                    <x-daisy::ui.textarea size="md" rows="3" placeholder="Medium" />
                    <x-daisy::ui.textarea size="lg" rows="4" placeholder="Large" />
                    <x-daisy::ui.textarea size="xl" rows="5" placeholder="Xlarge" />
                </div>
                <x-daisy::ui.textarea placeholder="Disabled" :disabled="true" />
            </div>
        </section>

        <!-- Checkbox -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Checkbox</h2>
            <div class="flex flex-wrap items-center gap-4">
                <label class="flex items-center gap-2">
                    <x-daisy::ui.checkbox />
                    <span>Default</span>
                </label>
                <label class="flex items-center gap-2">
                    <x-daisy::ui.checkbox color="primary" :checked="true" />
                    <span>Primary checked</span>
                </label>
                <label class="flex items-center gap-2 opacity-70">
                    <x-daisy::ui.checkbox :disabled="true" />
                    <span>Disabled</span>
                </label>
                <x-daisy::ui.checkbox size="xs" />
                <x-daisy::ui.checkbox size="sm" />
                <x-daisy::ui.checkbox size="md" />
                <x-daisy::ui.checkbox size="lg" />
                <x-daisy::ui.checkbox size="xl" />

                <!-- Indeterminate via JS -->
                <div class="flex items-center gap-2">
                    <x-daisy::ui.checkbox id="demo-indeterminate" :indeterminate="true" />
                    <span>Indeterminate (JS)</span>
                </div>
            </div>
        </section>

        <!-- Radio -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Radio</h2>
            <div class="flex flex-wrap items-center gap-4">
                <!-- Basique -->
                <x-daisy::ui.radio name="r1" value="a" :checked="true" />
                <x-daisy::ui.radio name="r1" value="b" />
                <!-- Couleurs -->
                <x-daisy::ui.radio name="r2" value="n" color="neutral" :checked="true" />
                <x-daisy::ui.radio name="r2" value="p" color="primary" />
                <x-daisy::ui.radio name="r2" value="s" color="secondary" />
                <x-daisy::ui.radio name="r2" value="a" color="accent" />
                <x-daisy::ui.radio name="r2" value="i" color="info" />
                <x-daisy::ui.radio name="r2" value="su" color="success" />
                <x-daisy::ui.radio name="r2" value="w" color="warning" />
                <x-daisy::ui.radio name="r2" value="e" color="error" />
                <!-- Tailles -->
                <x-daisy::ui.radio name="r3" value="xs" size="xs" :checked="true" />
                <x-daisy::ui.radio name="r3" value="sm" size="sm" />
                <x-daisy::ui.radio name="r3" value="md" size="md" />
                <x-daisy::ui.radio name="r3" value="lg" size="lg" />
                <x-daisy::ui.radio name="r3" value="xl" size="xl" />
                <!-- Disabled -->
                <x-daisy::ui.radio name="r4" value="d1" :disabled="true" :checked="true" />
                <x-daisy::ui.radio name="r4" value="d2" :disabled="true" />
            </div>
        </section>

        <!-- Toggle -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Toggle</h2>
            <div class="flex flex-wrap items-center gap-4">
                <label class="flex items-center gap-2">
                    <x-daisy::ui.toggle />
                    <span>Default</span>
                </label>
                <label class="flex items-center gap-2">
                    <x-daisy::ui.toggle color="primary" :checked="true" />
                    <span>Primary ON</span>
                </label>
                <label class="flex items-center gap-2 opacity-70">
                    <x-daisy::ui.toggle :disabled="true" />
                    <span>Disabled</span>
                </label>
                <x-daisy::ui.toggle size="xs" />
                <x-daisy::ui.toggle size="sm" />
                <x-daisy::ui.toggle size="md" />
                <x-daisy::ui.toggle size="lg" />
                <x-daisy::ui.toggle size="xl" />
                <x-daisy::ui.toggle :indeterminate="true" />
            </div>
        </section>

        <!-- Loading -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Loading</h2>
            <div class="flex flex-wrap items-center gap-4">
                <!-- Spinner: xs..xl -->
                <x-daisy::ui.loading shape="spinner" size="xs" />
                <x-daisy::ui.loading shape="spinner" size="sm" />
                <x-daisy::ui.loading shape="spinner" size="md" />
                <x-daisy::ui.loading shape="spinner" size="lg" />
                <x-daisy::ui.loading shape="spinner" size="xl" />

                <!-- Dots/Ring/Ball/Bars/Infinity avec couleurs -->
                <x-daisy::ui.loading shape="ring" size="sm" color="primary" />
                <x-daisy::ui.loading shape="dots" size="sm" color="info" />
                <x-daisy::ui.loading shape="ball" size="md" color="success" />
                <x-daisy::ui.loading shape="bars" size="lg" color="warning" />
                <x-daisy::ui.loading shape="infinity" size="lg" color="error" />
            </div>
        </section>

        <!-- Links -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Links</h2>
            <div class="flex flex-wrap gap-4 items-center">
                <x-daisy::ui.link href="#" :underline="false">Normal underline</x-daisy::ui.link>
                <x-daisy::ui.link href="#" color="primary">Primary</x-daisy::ui.link>
                <x-daisy::ui.link href="#" color="secondary">Secondary</x-daisy::ui.link>
                <x-daisy::ui.link href="#" color="accent">Accent</x-daisy::ui.link>
                <x-daisy::ui.link href="#" color="neutral">Neutral</x-daisy::ui.link>
                <x-daisy::ui.link href="#" color="success">Success</x-daisy::ui.link>
                <x-daisy::ui.link href="#" color="info">Info</x-daisy::ui.link>
                <x-daisy::ui.link href="#" color="warning">Warning</x-daisy::ui.link>
                <x-daisy::ui.link href="#" color="error">Error</x-daisy::ui.link>
                <x-daisy::ui.link href="https://daisyui.com" external>Externe</x-daisy::ui.link>
            </div>
        </section>

        <!-- Badges -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Badges</h2>
            <div class="space-y-2">
                <div class="flex flex-wrap gap-2 items-center">
                    <x-daisy::ui.badge color="neutral">Neutral</x-daisy::ui.badge>
                    <x-daisy::ui.badge color="primary">Primary</x-daisy::ui.badge>
                    <x-daisy::ui.badge color="secondary">Secondary</x-daisy::ui.badge>
                    <x-daisy::ui.badge color="accent">Accent</x-daisy::ui.badge>
                    <x-daisy::ui.badge color="info">Info</x-daisy::ui.badge>
                    <x-daisy::ui.badge color="success">Success</x-daisy::ui.badge>
                    <x-daisy::ui.badge color="warning">Warning</x-daisy::ui.badge>
                    <x-daisy::ui.badge color="error">Error</x-daisy::ui.badge>
                </div>
                <div class="flex flex-wrap gap-2 items-center">
                    <x-daisy::ui.badge color="primary" variant="outline">Outline</x-daisy::ui.badge>
                    <x-daisy::ui.badge color="primary" variant="dash">Dash</x-daisy::ui.badge>
                    <x-daisy::ui.badge color="primary" variant="ghost">Ghost</x-daisy::ui.badge>
                    <x-daisy::ui.badge color="success" variant="soft">Soft</x-daisy::ui.badge>
                </div>
                <div class="flex flex-wrap gap-2 items-center">
                    <x-daisy::ui.badge size="xs">XS</x-daisy::ui.badge>
                    <x-daisy::ui.badge size="sm">SM</x-daisy::ui.badge>
                    <x-daisy::ui.badge size="md">MD</x-daisy::ui.badge>
                    <x-daisy::ui.badge size="lg">LG</x-daisy::ui.badge>
                    <x-daisy::ui.badge size="xl">XL</x-daisy::ui.badge>
                </div>
                <div class="space-y-1">
                    <h3 class="text-xl font-semibold">Heading 1 <x-daisy::ui.badge size="xl">Badge</x-daisy::ui.badge></h3>
                    <h4 class="text-lg font-semibold">Heading 2 <x-daisy::ui.badge size="lg">Badge</x-daisy::ui.badge></h4>
                    <h5 class="text-base font-semibold">Heading 3 <x-daisy::ui.badge size="md">Badge</x-daisy::ui.badge></h5>
                    <h6 class="text-sm font-semibold">Heading 4 <x-daisy::ui.badge size="sm">Badge</x-daisy::ui.badge></h6>
                    <p class="text-xs">Paragraph <x-daisy::ui.badge size="xs">Badge</x-daisy::ui.badge></p>
                </div>
                <div class="flex items-center gap-3">
                    <button class="btn">Inbox <x-daisy::ui.badge size="sm" class="ml-2">+99</x-daisy::ui.badge></button>
                    <button class="btn">Inbox <x-daisy::ui.badge size="sm" color="secondary" class="ml-2">+99</x-daisy::ui.badge></button>
                </div>
            </div>
        </section>

        <!-- Avatars -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Avatars</h2>
            <div class="space-y-3">
            <div class="flex flex-wrap items-center gap-4">
                <x-daisy::ui.avatar src="https://i.pravatar.cc/100?img=3" alt="Avatar" />
                <x-daisy::ui.avatar placeholder="JS" />
                <x-daisy::ui.avatar size="sm" placeholder="SM" />
                <x-daisy::ui.avatar size="lg" placeholder="LG" />
                    <x-daisy::ui.avatar size="xl" placeholder="XL" />
                    <x-daisy::ui.avatar size="xxl" placeholder="XXL" />
                </div>
                <div class="flex flex-wrap items-center gap-4">
                <x-daisy::ui.avatar rounded="md" placeholder="MD" />
                    <x-daisy::ui.avatar rounded="xl" placeholder="XL" />
                <x-daisy::ui.avatar rounded="none" placeholder="--" />
                </div>
                <div class="flex flex-wrap items-center gap-4">
                    <x-daisy::ui.avatar src="https://i.pravatar.cc/100?img=9" status="online" />
                    <x-daisy::ui.avatar src="https://i.pravatar.cc/100?img=10" status="offline" />
                    <div class="avatar-group -space-x-4 rtl:space-x-reverse">
                        <x-daisy::ui.avatar src="https://i.pravatar.cc/100?img=11" />
                        <x-daisy::ui.avatar src="https://i.pravatar.cc/100?img=12" />
                        <x-daisy::ui.avatar src="https://i.pravatar.cc/100?img=13" />
                        <x-daisy::ui.avatar placeholder="+99" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Divider -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Divider</h2>
            <div class="space-y-6">
                <!-- Vertical (défaut) avec texte -->
                <div class="flex w-full flex-col">
                    <div class="card bg-base-300 rounded-box grid h-20 place-items-center">content</div>
                    <x-daisy::ui.divider>OR</x-daisy::ui.divider>
                    <div class="card bg-base-300 rounded-box grid h-20 place-items-center">content</div>
                    </div>

                <!-- Horizontal -->
                <div class="flex w-full">
                    <div class="card bg-base-300 rounded-box grid h-20 grow place-items-center">content</div>
                    <x-daisy::ui.divider :horizontal="true">OR</x-daisy::ui.divider>
                    <div class="card bg-base-300 rounded-box grid h-20 grow place-items-center">content</div>
                </div>

                <!-- Sans texte -->
                <div class="flex w-full flex-col">
                    <div class="card bg-base-300 rounded-box grid h-20 place-items-center">content</div>
                    <x-daisy::ui.divider />
                    <div class="card bg-base-300 rounded-box grid h-20 place-items-center">content</div>
                </div>

                <!-- Responsive: lg horizontal -->
                <div class="flex w-full flex-col lg:flex-row">
                    <div class="card bg-base-300 rounded-box grid h-32 grow place-items-center">content</div>
                    <x-daisy::ui.divider horizontalAt="lg">OR</x-daisy::ui.divider>
                    <div class="card bg-base-300 rounded-box grid h-32 grow place-items-center">content</div>
                </div>

                <!-- Couleurs et placements -->
                <div class="flex w-full flex-col">
                    <x-daisy::ui.divider>Default</x-daisy::ui.divider>
                    <x-daisy::ui.divider color="neutral">Neutral</x-daisy::ui.divider>
                    <x-daisy::ui.divider color="primary">Primary</x-daisy::ui.divider>
                    <x-daisy::ui.divider color="secondary">Secondary</x-daisy::ui.divider>
                    <x-daisy::ui.divider color="accent">Accent</x-daisy::ui.divider>
                    <x-daisy::ui.divider color="success">Success</x-daisy::ui.divider>
                    <x-daisy::ui.divider color="warning">Warning</x-daisy::ui.divider>
                    <x-daisy::ui.divider color="info">Info</x-daisy::ui.divider>
                    <x-daisy::ui.divider color="error">Error</x-daisy::ui.divider>
                </div>
                <div class="flex w-full flex-col">
                    <x-daisy::ui.divider position="start">Start</x-daisy::ui.divider>
                    <x-daisy::ui.divider>Default</x-daisy::ui.divider>
                    <x-daisy::ui.divider position="end">End</x-daisy::ui.divider>
                </div>
            </div>
        </section>

        <!-- Label -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Label</h2>
            <div class="space-y-6">
            <div class="flex items-center gap-4">
                <x-daisy::ui.label for="demo-lbl" value="Label" alt="optionnel" />
                </div>

                <!-- Label intégré dans input (prefix/suffix) -->
                <label class="input">
                    <span class="label">https://</span>
                    <input type="text" placeholder="URL" />
                </label>
                <label class="input">
                    <input type="text" placeholder="domain name" />
                    <span class="label">.com</span>
                </label>
                <label class="select w-56">
                    <span class="label">Type</span>
                    <select>
                        <option>Personal</option>
                        <option>Business</option>
                    </select>
                </label>
                <label class="input w-56">
                    <span class="label">Publish date</span>
                    <input type="date" />
                </label>

                <!-- Floating label -->
                <div class="grid md:grid-cols-5 gap-3">
                    <x-daisy::ui.label floating span="Extra Small">
                        <input type="text" placeholder="Extra Small" class="input input-xs" />
                    </x-daisy::ui.label>
                    <x-daisy::ui.label floating span="Small">
                        <input type="text" placeholder="Small" class="input input-sm" />
                    </x-daisy::ui.label>
                    <x-daisy::ui.label floating span="Medium">
                        <input type="text" placeholder="Medium" class="input input-md" />
                    </x-daisy::ui.label>
                    <x-daisy::ui.label floating span="Large">
                        <input type="text" placeholder="Large" class="input input-lg" />
                    </x-daisy::ui.label>
                    <x-daisy::ui.label floating span="Extra Large">
                        <input type="text" placeholder="Extra Large" class="input input-xl" />
                    </x-daisy::ui.label>
                </div>
            </div>
        </section>

        <!-- Kbd -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Kbd</h2>
            <div class="space-y-3">
            <div class="flex items-center gap-2">
                    <x-daisy::ui.kbd size="sm">⌘</x-daisy::ui.kbd>
                <x-daisy::ui.kbd>K</x-daisy::ui.kbd>
                    <x-daisy::ui.kbd size="lg">F</x-daisy::ui.kbd>
                    <x-daisy::ui.kbd size="xl">ESC</x-daisy::ui.kbd>
                </div>
                <p>Press <x-daisy::ui.kbd size="sm">F</x-daisy::ui.kbd> to pay respects.</p>
                <div>
                    <x-daisy::ui.kbd :keys="['ctrl','shift','del']" />
                </div>
                <div class="my-1 flex w-full justify-center gap-1">
                    @foreach(str_split('qwertyuiop') as $k)
                        <x-daisy::ui.kbd>{{ $k }}</x-daisy::ui.kbd>
                    @endforeach
                </div>
                <div class="my-1 flex w-full justify-center gap-1">
                    @foreach(str_split('asdfghjkl') as $k)
                        <x-daisy::ui.kbd>{{ $k }}</x-daisy::ui.kbd>
                    @endforeach
                </div>
                <div class="my-1 flex w-full justify-center gap-1">
                    @foreach(str_split('zxcvbnm/') as $k)
                        <x-daisy::ui.kbd>{{ $k }}</x-daisy::ui.kbd>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Breadcrumbs -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Breadcrumbs</h2>
            <div class="space-y-3">
            <x-daisy::ui.breadcrumbs :items="[
                ['label' => 'Home', 'href' => '/'],
                ['label' => 'Library', 'href' => '#'],
                ['label' => 'Data']
            ]" />

                <x-daisy::ui.breadcrumbs size="sm" as="nav" label="Breadcrumb with icons" :items="[
                    ['label' => 'Home', 'href' => '/', 'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' class=\'h-4 w-4 stroke-current\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z\'></path></svg>'],
                    ['label' => 'Documents', 'href' => '#', 'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' class=\'h-4 w-4 stroke-current\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z\'></path></svg>'],
                    ['label' => 'Add Document', 'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' class=\'h-4 w-4 stroke-current\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\'></path></svg>'],
                ]" />

                <x-daisy::ui.breadcrumbs size="sm" class="max-w-xs" :items="[
                    ['label' => 'Long text 1'],
                    ['label' => 'Long text 2'],
                    ['label' => 'Long text 3'],
                    ['label' => 'Long text 4'],
                    ['label' => 'Long text 5'],
                ]" />
            </div>
        </section>

        <!-- Dropdown -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Dropdown</h2>
            <div class="space-y-3">
                <x-daisy::ui.dropdown label="Dropdown">
                    <li><a>Item 1</a></li>
                    <li><a>Item 2</a></li>
                </x-daisy::ui.dropdown>
                <x-daisy::ui.dropdown label="End" :end="true">
                    <li><a>Item 1</a></li>
                    <li><a>Item 2</a></li>
                </x-daisy::ui.dropdown>
                <x-daisy::ui.dropdown label="Hover" :hover="true">
                    <li><a>Item 1</a></li>
                    <li><a>Item 2</a></li>
                </x-daisy::ui.dropdown>
            </div>
        </section>

        <!-- Alert -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Alert</h2>
            <div class="space-y-4">
                <!-- Basique -->
                <x-daisy::ui.alert color="info">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info h-6 w-6 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </x-slot:icon>
                     New software update available.
                </x-daisy::ui.alert>

                <!-- Couleurs -->
                <x-daisy::ui.alert color="success">Your purchase has been confirmed!</x-daisy::ui.alert>
                <x-daisy::ui.alert color="warning">Warning: Low disk space.</x-daisy::ui.alert>
                <x-daisy::ui.alert color="error">Error: Something went wrong.</x-daisy::ui.alert>

                <!-- Variantes -->
                <x-daisy::ui.alert color="info" variant="soft">Info (soft)</x-daisy::ui.alert>
                <x-daisy::ui.alert color="info" variant="outline">Info (outline)</x-daisy::ui.alert>
                <x-daisy::ui.alert color="info" variant="dash">Info (dash)</x-daisy::ui.alert>

                <!-- Orientation -->
                <x-daisy::ui.alert color="info" :vertical="true">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info h-6 w-6 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </x-slot:icon>
                    we use cookies for no reason.
                    <x-slot:actions>
                        <x-daisy::ui.button size="sm">Deny</x-daisy::ui.button>
                        <x-daisy::ui.button size="sm" color="primary">Accept</x-daisy::ui.button>
                    </x-slot:actions>
                </x-daisy::ui.alert>
                <x-daisy::ui.alert color="info" horizontalAt="sm">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info h-6 w-6 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </x-slot:icon>
                    <div>
                        <h3 class="font-bold">New message!</h3>
                        <div class="text-xs">You have 1 unread message</div>
                    </div>
                    <x-slot:actions>
                        <x-daisy::ui.button size="sm">See</x-daisy::ui.button>
                    </x-slot:actions>
                </x-daisy::ui.alert>
            </div>
        </section>

        <!-- Notifications (déclenchables) -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Notifications (déclenchables)</h2>
            <div class="flex flex-wrap gap-2">
                <button class="btn btn-info btn-sm" onclick="window.triggerToast('info','Information','Nouvelle mise à jour disponible')">Info</button>
                <button class="btn btn-success btn-sm" onclick="window.triggerToast('success','Succès','Sauvegardé avec succès')">Succès</button>
                <button class="btn btn-warning btn-sm" onclick="window.triggerToast('warning','Attention','Connexion instable')">Attention</button>
                <button class="btn btn-error btn-sm" onclick="window.triggerToast('error','Erreur','Échec de l\'opération')">Erreur</button>
            </div>
            <x-daisy::ui.toast id="toastContainer" position="end" vertical="bottom" class="z-50"></x-daisy::ui.toast>
        </section>

        <!-- Modal -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Modal</h2>
            <div class="space-y-6">
                <!-- Méthode dialog standard -->
                <div class="space-y-2">
                    <x-daisy::ui.button onclick="document.getElementById('demo-modal').showModal()">Ouvrir (center)</x-daisy::ui.button>
                <x-daisy::ui.modal id="demo-modal" title="Exemple de modal">
                    Contenu de la modal.
                    <x-slot:actions>
                        <form method="dialog">
                            <x-daisy::ui.button>Fermer</x-daisy::ui.button>
                        </form>
                    </x-slot:actions>
                </x-daisy::ui.modal>
                </div>

                <!-- Positionnements -->
                <div class="flex flex-wrap gap-2">
                    <x-daisy::ui.button onclick="document.getElementById('demo-modal-top').showModal()">Top</x-daisy::ui.button>
                    <x-daisy::ui.button onclick="document.getElementById('demo-modal-bottom').showModal()">Bottom</x-daisy::ui.button>
                    <x-daisy::ui.button onclick="document.getElementById('demo-modal-start').showModal()">Start</x-daisy::ui.button>
                    <x-daisy::ui.button onclick="document.getElementById('demo-modal-end').showModal()">End</x-daisy::ui.button>
                </div>
                <x-daisy::ui.modal id="demo-modal-top" title="Modal Top" vertical="top">
                    Placée en haut
                    <x-slot:actions>
                        <form method="dialog">
                            <x-daisy::ui.button>Fermer</x-daisy::ui.button>
                        </form>
                    </x-slot:actions>
                </x-daisy::ui.modal>
                <x-daisy::ui.modal id="demo-modal-bottom" title="Modal Bottom" vertical="bottom">
                    Placée en bas
                    <x-slot:actions>
                        <form method="dialog">
                            <x-daisy::ui.button>Fermer</x-daisy::ui.button>
                        </form>
                    </x-slot:actions>
                </x-daisy::ui.modal>
                <x-daisy::ui.modal id="demo-modal-start" title="Modal Start" horizontal="start">
                    Alignée à gauche
                    <x-slot:actions>
                        <form method="dialog">
                            <x-daisy::ui.button>Fermer</x-daisy::ui.button>
                        </form>
                    </x-slot:actions>
                </x-daisy::ui.modal>
                <x-daisy::ui.modal id="demo-modal-end" title="Modal End" horizontal="end">
                    Alignée à droite
                    <x-slot:actions>
                        <form method="dialog">
                            <x-daisy::ui.button>Fermer</x-daisy::ui.button>
                        </form>
                    </x-slot:actions>
                </x-daisy::ui.modal>

                <!-- Backdrop option -->
                <div class="space-y-2">
                    <x-daisy::ui.button onclick="document.getElementById('demo-modal-nobackdrop').showModal()">Sans backdrop</x-daisy::ui.button>
                    <x-daisy::ui.modal id="demo-modal-nobackdrop" title="Sans backdrop" :backdrop="false">
                        Cliquez en dehors ne fermera pas (utiliser le bouton).
                        <x-slot:actions>
                            <form method="dialog">
                                <x-daisy::ui.button>Fermer</x-daisy::ui.button>
                            </form>
                        </x-slot:actions>
                    </x-daisy::ui.modal>
                </div>

                <!-- Largeur personnalisée via boxClass -->
                <div class="space-y-2">
                    <x-daisy::ui.button onclick="document.getElementById('demo-modal-lg').showModal()">Large</x-daisy::ui.button>
                    <x-daisy::ui.modal id="demo-modal-lg" title="Large" boxClass="max-w-3xl">
                        Modal plus large via classe utilitaire.
                        <x-slot:actions>
                            <form method="dialog">
                                <x-daisy::ui.button>Fermer</x-daisy::ui.button>
                            </form>
                        </x-slot:actions>
                    </x-daisy::ui.modal>
                </div>
            </div>
        </section>

        <!-- Icônes (blade-icons) -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Icônes (blade-icons)</h2>
            <div class="flex flex-wrap items-center gap-4 text-base-content/80">
                <!-- Heroicons -->
                <div class="flex items-center gap-2">
                    <x-heroicon-o-home class="h-6 w-6" />
                    <span>heroicon-o-home</span>
                </div>
                <div class="flex items-center gap-2">
                    <x-heroicon-o-arrow-right class="h-6 w-6" />
                    <span>heroicon-o-arrow-right</span>
                </div>
                <!-- Lucide -->
                <div class="flex items-center gap-2">
                    <x-lucide-external-link class="h-6 w-6" />
                    <span>lucide-external-link</span>
                </div>
                <div class="flex items-center gap-2">
                    <x-lucide-heart class="h-6 w-6" />
                    <span>lucide-heart</span>
                </div>
            </div>
        </section>

        <!-- Login Buttons -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Login Buttons</h2>
            <div class="flex flex-wrap gap-3">
                <x-daisy::ui.login-button provider="google" />
                <x-daisy::ui.login-button provider="apple" />
                <x-daisy::ui.login-button provider="microsoft" />
                <x-daisy::ui.login-button provider="github" />
                <x-daisy::ui.login-button provider="twitter" />
                <x-daisy::ui.login-button provider="facebook" />
                <x-daisy::ui.login-button provider="discord" />
                <x-daisy::ui.login-button provider="gitlab" />
                <x-daisy::ui.login-button provider="linkedin" />
                <x-daisy::ui.login-button provider="slack" />
                <x-daisy::ui.login-button provider="steam" />
                <x-daisy::ui.login-button provider="spotify" />
                <x-daisy::ui.login-button provider="yahoo" />
                <x-daisy::ui.login-button provider="wechat" />
                <x-daisy::ui.login-button provider="metamask" />
            </div>
        </section>

        <!-- Cards -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Card</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <x-daisy::ui.card title="Titre" color="base-100" size="sm">
                    Contenu simple
                    <x-slot:actions>
                        <x-daisy::ui.button size="sm">Action</x-daisy::ui.button>
                    </x-slot:actions>
                </x-daisy::ui.card>
                <x-daisy::ui.card title="Bordered" :bordered="true" :dash="true">
                    Carte avec bordure
                </x-daisy::ui.card>
                <x-daisy::ui.card title="Compact" :compact="true" size="lg">
                    Moins d'espacement
                </x-daisy::ui.card>
            </div>
            <div class="grid md:grid-cols-2 gap-6">
                <x-daisy::ui.card :side="true" title="Side" imageAlt="Exemple image">
                    <x-slot:figure>
                        <img src="https://picsum.photos/seed/picsum/200/200" alt="" />
                    </x-slot:figure>
                    Carte avec image latérale
                </x-daisy::ui.card>
                <x-daisy::ui.card :imageFull="true" title="Image Full" imageUrl="https://picsum.photos/seed/daisy/600/300" imageAlt="Image de démonstration">
                    Texte sur image full
                </x-daisy::ui.card>
            </div>
        </section>

        <!-- Indicator -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Indicator</h2>
            <div class="flex flex-wrap items-center gap-8">
                <!-- Status indicator -->
                <x-daisy::ui.indicator type="status" statusColor="success">
                    <div class="bg-base-300 grid h-32 w-32 place-items-center rounded-box">content</div>
                </x-daisy::ui.indicator>

                <!-- Badge indicator -->
                <x-daisy::ui.indicator label="New" color="primary">
                    <div class="bg-base-300 grid h-32 w-32 place-items-center rounded-box">content</div>
                </x-daisy::ui.indicator>

                <!-- Sur bouton -->
                <x-daisy::ui.indicator label="12" color="secondary">
                    <button class="btn">inbox</button>
                </x-daisy::ui.indicator>

                <!-- Avatar avec badge (image fiable) -->
                <div class="avatar indicator">
                    <span class="indicator-item indicator-bottom indicator-end badge badge-secondary">Justice</span>
                    <div class="h-20 w-20 rounded-lg">
                        <img alt="User avatar" src="https://i.pravatar.cc/100?img=15" />
                    </div>
                </div>

                <!-- Positionnements -->
                <div class="indicator">
                    <span class="indicator-item indicator-top indicator-start badge">↖︎</span>
                    <span class="indicator-item indicator-top indicator-center badge">↑</span>
                    <span class="indicator-item indicator-top indicator-end badge">↗︎</span>
                    <span class="indicator-item indicator-middle indicator-start badge">←</span>
                    <span class="indicator-item indicator-middle indicator-center badge">●</span>
                    <span class="indicator-item indicator-middle indicator-end badge">→</span>
                    <span class="indicator-item indicator-bottom indicator-start badge">↙︎</span>
                    <span class="indicator-item indicator-bottom indicator-center badge">↓</span>
                    <span class="indicator-item indicator-bottom indicator-end badge">↘︎</span>
                    <div class="bg-base-300 grid h-32 w-60 place-items-center rounded-box">Box</div>
                </div>
            </div>
        </section>

        <!-- Progress -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Progress</h2>
            <div class="space-y-3">
                <x-daisy::ui.progress value="0" class="w-56" />
                <x-daisy::ui.progress value="10" class="w-56" />
                <x-daisy::ui.progress value="40" class="w-56" />
                <x-daisy::ui.progress value="70" class="w-56" />
                <x-daisy::ui.progress value="100" class="w-56" />

                <x-daisy::ui.progress value="40" color="primary" class="w-56" />
                <x-daisy::ui.progress value="40" color="secondary" class="w-56" />
                <x-daisy::ui.progress value="40" color="accent" class="w-56" />
                <x-daisy::ui.progress value="40" color="neutral" class="w-56" />
                <x-daisy::ui.progress value="40" color="info" class="w-56" />
                <x-daisy::ui.progress value="40" color="success" class="w-56" />
                <x-daisy::ui.progress value="40" color="warning" class="w-56" />
                <x-daisy::ui.progress value="40" color="error" class="w-56" />

                <x-daisy::ui.progress class="w-56" />
            </div>
        </section>

        <!-- Radial Progress -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Radial Progress</h2>
            <div class="flex flex-wrap items-center gap-6">
                <!-- Valeurs -->
                <x-daisy::ui.radial-progress value="0">0%</x-daisy::ui.radial-progress>
                <x-daisy::ui.radial-progress value="20">20%</x-daisy::ui.radial-progress>
                <x-daisy::ui.radial-progress value="60">60%</x-daisy::ui.radial-progress>
                <x-daisy::ui.radial-progress value="80">80%</x-daisy::ui.radial-progress>
                <x-daisy::ui.radial-progress value="100">100%</x-daisy::ui.radial-progress>

                <!-- Couleur -->
                <x-daisy::ui.radial-progress value="70" color="primary">70%</x-daisy::ui.radial-progress>

                <!-- Fond + bordure -->
                <div class="radial-progress bg-primary text-primary-content border-primary border-4" style="--value:70; --size:5rem;" role="progressbar" aria-valuenow="70">70%</div>

                <!-- Taille / épaisseur -->
                <x-daisy::ui.radial-progress value="70" size="12rem" thickness="2px">70%</x-daisy::ui.radial-progress>
                <x-daisy::ui.radial-progress value="70" size="12rem" thickness="2rem">70%</x-daisy::ui.radial-progress>
            </div>
        </section>

        <!-- Rating -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Rating</h2>
            <div class="flex flex-wrap items-center gap-6">
                <x-daisy::ui.rating name="r1" :value="3" size="md" />
                <x-daisy::ui.rating name="r2" :value="4.5" :half="true" size="lg" />
                <x-daisy::ui.rating name="r3" :value="0" :half="true" :clearable="true" size="xl" />
                <x-daisy::ui.rating name="r4" :value="4" :readOnly="true" />
            </div>
        </section>

        <!-- Select -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Select</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <x-daisy::ui.select>
                    <option disabled selected>Pick a color</option>
                    <option>Crimson</option>
                    <option>Amber</option>
                    <option>Velvet</option>
                </x-daisy::ui.select>

                <x-daisy::ui.select variant="ghost">
                    <option disabled selected>Pick a font</option>
                    <option>Inter</option>
                    <option>Poppins</option>
                    <option>Raleway</option>
                </x-daisy::ui.select>

                <x-daisy::ui.select color="primary">
                    <option disabled selected>Pick a text editor</option>
                    <option>VScode</option>
                    <option>VScode fork</option>
                    <option>Another VScode fork</option>
                </x-daisy::ui.select>
            </div>

            <div class="grid md:grid-cols-5 gap-4">
                <x-daisy::ui.select size="xs"><option disabled selected>Xsmall</option><option>One</option></x-daisy::ui.select>
                <x-daisy::ui.select size="sm"><option disabled selected>Small</option><option>One</option></x-daisy::ui.select>
                <x-daisy::ui.select size="md"><option disabled selected>Medium</option><option>One</option></x-daisy::ui.select>
                <x-daisy::ui.select size="lg"><option disabled selected>Large</option><option>One</option></x-daisy::ui.select>
                <x-daisy::ui.select size="xl"><option disabled selected>Xlarge</option><option>One</option></x-daisy::ui.select>
            </div>

            <x-daisy::ui.select :disabled="true">
                <option>You can't touch this</option>
            </x-daisy::ui.select>
        </section>

        <!-- Stat -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Stat</h2>
            <div class="space-y-4">
                <!-- Simple -->
                <div class="stats shadow">
                    <x-daisy::ui.stat title="Total Page Views" value="89,400" desc="21% more than last month" />
                </div>

                <!-- Avec icônes / image -->
                <div class="stats shadow">
                <x-daisy::ui.stat>
                    <x-slot:figure>
                            <x-heroicon-o-heart class="inline-block h-8 w-8 stroke-current text-primary" />
                    </x-slot:figure>
                        <x-slot:title>Total Likes</x-slot:title>
                        <x-slot:value><span class="text-primary">25.6K</span></x-slot:value>
                        <x-slot:desc>21% more than last month</x-slot:desc>
                </x-daisy::ui.stat>
                    <x-daisy::ui.stat>
                        <x-slot:figure>
                            <x-heroicon-o-bolt class="inline-block h-8 w-8 stroke-current text-secondary" />
                        </x-slot:figure>
                        <x-slot:title>Page Views</x-slot:title>
                        <x-slot:value><span class="text-secondary">2.6M</span></x-slot:value>
                        <x-slot:desc>21% more than last month</x-slot:desc>
                    </x-daisy::ui.stat>
                    <x-daisy::ui.stat>
                        <x-slot:figure>
                            <div class="avatar avatar-online">
                                <div class="w-16 rounded-full">
                    <img src="https://i.pravatar.cc/100?img=48" />
                                </div>
                            </div>
                        </x-slot:figure>
                        <x-slot:value>86%</x-slot:value>
                        <x-slot:title>Tasks done</x-slot:title>
                        <x-slot:desc><span class="text-secondary">31 tasks remaining</span></x-slot:desc>
                    </x-daisy::ui.stat>
                </div>

                <!-- Responsive vertical/horizontal -->
                <div class="stats stats-vertical lg:stats-horizontal shadow">
                    <x-daisy::ui.stat title="Downloads" value="31K" desc="Jan 1st - Feb 1st" />
                    <x-daisy::ui.stat title="New Users" value="4,200" desc="↗︎ 400 (22%)" />
                    <x-daisy::ui.stat title="New Registers" value="1,200" desc="↘︎ 90 (14%)" />
                </div>

                <!-- Avec boutons (actions) -->
                <div class="stats bg-base-100 border-base-300 border">
                    <x-daisy::ui.stat title="Account balance" value="$89,400">
                        <x-slot:actions>
                            <button class="btn btn-xs btn-success">Add funds</button>
                        </x-slot:actions>
                    </x-daisy::ui.stat>
                    <x-daisy::ui.stat title="Current balance" value="$89,400">
                        <x-slot:actions>
                            <button class="btn btn-xs">Withdrawal</button>
                            <button class="btn btn-xs">Deposit</button>
                        </x-slot:actions>
                    </x-daisy::ui.stat>
                </div>
            </div>
        </section>

        <!-- Skeleton -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Skeleton</h2>
            <div class="flex items-center gap-4">
                <x-daisy::ui.skeleton width="w-24" height="h-6" />
                <x-daisy::ui.skeleton width="w-36" height="h-6" />
                <x-daisy::ui.skeleton width="w-16" height="h-16" rounded="full" />
            </div>

            <!-- Circle with content -->
            <div class="flex w-52 flex-col gap-4">
                <div class="flex items-center gap-4">
                    <x-daisy::ui.skeleton width="w-16" height="h-16" class="shrink-0" rounded="full" />
                    <div class="flex flex-col gap-4 w-full">
                        <x-daisy::ui.skeleton width="w-20" height="h-4" />
                        <x-daisy::ui.skeleton width="w-28" height="h-4" />
                    </div>
                </div>
                <x-daisy::ui.skeleton height="h-32" class="w-full" />
            </div>

            <!-- Rectangle with content -->
            <div class="flex w-52 flex-col gap-4">
                <x-daisy::ui.skeleton height="h-32" class="w-full" />
                <x-daisy::ui.skeleton width="w-28" height="h-4" />
                <x-daisy::ui.skeleton height="h-4" class="w-full" />
                <x-daisy::ui.skeleton height="h-4" class="w-full" />
            </div>
        </section>

        <!-- Stack -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Stack</h2>
            <div class="flex flex-wrap gap-8">
                <!-- 3 divs -->
                <x-daisy::ui.stack class="h-20 w-32">
                    <div class="bg-primary text-primary-content grid place-content-center rounded-box">1</div>
                    <div class="bg-accent text-accent-content grid place-content-center rounded-box">2</div>
                    <div class="bg-secondary text-secondary-content grid place-content-center rounded-box">3</div>
                </x-daisy::ui.stack>

                <!-- Images -->
                <x-daisy::ui.stack class="w-48">
                    <img src="https://picsum.photos/seed/s1/400/300" class="rounded-box" />
                    <img src="https://picsum.photos/seed/s2/400/300" class="rounded-box" />
                    <img src="https://picsum.photos/seed/s3/400/300" class="rounded-box" />
                </x-daisy::ui.stack>

                <!-- Cards -->
                <x-daisy::ui.stack class="size-28">
                    <div class="card bg-base-100 border border-base-content text-center">
                        <div class="card-body">A</div>
                    </div>
                    <div class="card bg-base-100 border border-base-content text-center">
                        <div class="card-body">B</div>
                    </div>
                    <div class="card bg-base-100 border border-base-content text-center">
                        <div class="card-body">C</div>
                    </div>
                </x-daisy::ui.stack>

                <!-- Alignements -->
                <div class="flex flex-col gap-4">
                    <x-daisy::ui.stack class="h-20 w-32" alignV="top">
                        <div class="card bg-base-200 text-center shadow-md"><div class="card-body">A</div></div>
                        <div class="card bg-base-200 text-center shadow"><div class="card-body">B</div></div>
                        <div class="card bg-base-200 text-center shadow-sm"><div class="card-body">C</div></div>
                    </x-daisy::ui.stack>
                    <x-daisy::ui.stack class="h-20 w-32" alignV="bottom" alignH="end">
                        <div class="card bg-base-200 text-center shadow-md"><div class="card-body">A</div></div>
                        <div class="card bg-base-200 text-center shadow"><div class="card-body">B</div></div>
                        <div class="card bg-base-200 text-center shadow-sm"><div class="card-body">C</div></div>
                    </x-daisy::ui.stack>
                </div>
            </div>
        </section>

        <!-- Timeline -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Timeline</h2>
            <div class="space-y-6">
                <!-- Texte des deux côtés + icône -->
            <x-daisy::ui.timeline :items="[
                    ['when' => '1984', 'title' => 'First Macintosh computer'],
                    ['when' => '1998', 'title' => 'iMac'],
                    ['when' => '2001', 'title' => 'iPod'],
                    ['when' => '2007', 'title' => 'iPhone'],
                    ['when' => '2015', 'title' => 'Apple Watch', 'hrAfter' => false],
                ]" />

                <!-- Avec contenu riche (startHtml / endHtml) -->
                <x-daisy::ui.timeline :items="[
                    [
                        'startHtml' => '<time class=\'font-mono italic\'>1984</time>',
                        'endHtml' => '<div class=\'timeline-box\'>First Macintosh computer</div>',
                        'hrAfter' => true,
                    ],
                    [
                        'startHtml' => '<time class=\'font-mono italic\'>1998</time>',
                        'endHtml' => '<div class=\'md:mb-10\'><div class=\'text-lg font-black\'>iMac</div>iMac is a family of all-in-one Mac desktop computers…</div>',
                        'hrAfter' => true,
                    ],
                    [
                        'startHtml' => '<time class=\'font-mono italic\'>2001</time>',
                        'endHtml' => '<div class=\'mb-10 md:text-end\'><div class=\'text-lg font-black\'>iPod</div>The iPod is a discontinued series of portable media players…</div>',
                        'hrAfter' => true,
                    ],
                    [
                        'startHtml' => '<time class=\'font-mono italic\'>2007</time>',
                        'endHtml' => '<div class=\'md:mb-10\'><div class=\'text-lg font-black\'>iPhone</div>iPhone is a line of smartphones produced by Apple Inc.…</div>',
                        'hrAfter' => true,
                    ],
                    [
                        'startHtml' => '<time class=\'font-mono italic\'>2015</time>',
                        'endHtml' => '<div class=\'mb-10 md:text-end\'><div class=\'text-lg font-black\'>Apple Watch</div>The Apple Watch is a line of smartwatches…</div>',
                        'hrAfter' => false,
                    ],
                ]" />
            </div>
        </section>

        <!-- Steps -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Steps</h2>
            <div class="space-y-6">
                <!-- Horizontal de base (complétées en primary) -->
            <x-daisy::ui.steps :items="['Préparation','Commande','Livraison','Fini']" :current="2" />

                <!-- Vertical forcé -->
                <x-daisy::ui.steps :items="['Register','Choose plan','Purchase','Receive Product']" :current="2" :vertical="true" />

                <!-- Responsive: vertical puis horizontal en lg -->
                <x-daisy::ui.steps :items="['Étape 1','Étape 2','Étape 3','Étape 4']" :current="3" horizontalAt="lg" />

                <!-- Icônes personnalisées + couleurs par étape -->
                <x-daisy::ui.steps :current="2" :items="[
                    ['label' => 'Step 1', 'icon' => '😕', 'color' => 'neutral'],
                    ['label' => 'Step 2', 'icon' => '😃', 'color' => 'neutral'],
                    ['label' => 'Step 3', 'icon' => '😍'],
                ]" />
            </div>
        </section>

        <!-- Tooltip -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Tooltip</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-daisy::ui.tooltip text="hello"><x-daisy::ui.button>Hover me</x-daisy::ui.button></x-daisy::ui.tooltip>
                <x-daisy::ui.tooltip :open="true" position="top" text="Top"><x-daisy::ui.button>Top</x-daisy::ui.button></x-daisy::ui.tooltip>
                <x-daisy::ui.tooltip :open="true" position="bottom" text="Bottom"><x-daisy::ui.button>Bottom</x-daisy::ui.button></x-daisy::ui.tooltip>
                <x-daisy::ui.tooltip :open="true" position="left" text="Left"><x-daisy::ui.button>Left</x-daisy::ui.button></x-daisy::ui.tooltip>
                <x-daisy::ui.tooltip :open="true" position="right" text="Right"><x-daisy::ui.button>Right</x-daisy::ui.button></x-daisy::ui.tooltip>
                <x-daisy::ui.tooltip :open="true" color="neutral" text="neutral"><x-daisy::ui.button class="btn-neutral">neutral</x-daisy::ui.button></x-daisy::ui.tooltip>
                <x-daisy::ui.tooltip :open="true" color="primary" text="primary"><x-daisy::ui.button class="btn-primary">primary</x-daisy::ui.button></x-daisy::ui.tooltip>
                <x-daisy::ui.tooltip :open="true" color="secondary" text="secondary"><x-daisy::ui.button class="btn-secondary">secondary</x-daisy::ui.button></x-daisy::ui.tooltip>
                <x-daisy::ui.tooltip :open="true" color="accent" text="accent"><x-daisy::ui.button class="btn-accent">accent</x-daisy::ui.button></x-daisy::ui.tooltip>
                <x-daisy::ui.tooltip :open="true" color="info" text="info"><x-daisy::ui.button class="btn-info">info</x-daisy::ui.button></x-daisy::ui.tooltip>
                <x-daisy::ui.tooltip :open="true" color="success" text="success"><x-daisy::ui.button class="btn-success">success</x-daisy::ui.button></x-daisy::ui.tooltip>
                <x-daisy::ui.tooltip :open="true" color="warning" text="warning"><x-daisy::ui.button class="btn-warning">warning</x-daisy::ui.button></x-daisy::ui.tooltip>
                <x-daisy::ui.tooltip :open="true" color="error" text="error"><x-daisy::ui.button class="btn-error">error</x-daisy::ui.button></x-daisy::ui.tooltip>
                <x-daisy::ui.tooltip>
                    <x-slot:contentSlot>
                        <div class="animate-bounce text-orange-400 -rotate-10 text-2xl font-black">Wow!</div>
                    </x-slot:contentSlot>
                    <x-daisy::ui.button>Hover me</x-daisy::ui.button>
                </x-daisy::ui.tooltip>
            </div>
        </section>

        <!-- Theme Controller -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Theme Controller</h2>
            <div class="space-y-4">
                <!-- Boutons (join) -->
                <x-daisy::ui.theme-controller class="flex flex-wrap gap-2" :themes="['light','dark','cupcake','emerald','retro','cyberpunk']" value="light" size="sm" />

                <!-- Dropdown -->
                <x-daisy::ui.theme-controller variant="dropdown" :themes="['light','dark','cupcake','emerald','retro','cyberpunk']" value="dark" label="Theme" />

                <!-- Toggle unique (synthwave) -->
                <label class="swap swap-rotate">
                    <input type="checkbox" class="theme-controller" value="synthwave" />
                    <x-heroicon-o-sun class="swap-off h-8 w-8" />
                    <x-heroicon-o-moon class="swap-on h-8 w-8" />
                </label>
            </div>
        </section>

        <!-- Collapse -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Collapse</h2>
            <div class="space-y-4">
            <div class="grid md:grid-cols-2 gap-6">
                    <!-- Checkbox method -->
                    <x-daisy::ui.collapse title="Checkbox (arrow)" :open="true" :bordered="true" :bg="true">
                    Contenu du collapse
                </x-daisy::ui.collapse>
                    <x-daisy::ui.collapse :arrow="false" title="Checkbox (plus)" :bordered="true" :bg="true">
                    Contenu avec +
                </x-daisy::ui.collapse>
                </div>

                <!-- Focus method -->
                <div class="grid md:grid-cols-2 gap-6">
                    <x-daisy::ui.collapse title="Focus (arrow)" method="focus" :bordered="true" :bg="true" />
                    <x-daisy::ui.collapse title="Focus (plus)" method="focus" :arrow="false" :bordered="true" :bg="true" />
                </div>

                <!-- Details method -->
                <div class="grid md:grid-cols-2 gap-6">
                    <x-daisy::ui.collapse title="Details (arrow)" method="details" :bordered="true" :bg="true" />
                    <x-daisy::ui.collapse title="Details (plus)" method="details" :arrow="false" :bordered="true" :bg="true" />
                </div>

                <!-- Forced state (checkbox only) -->
                <div class="grid md:grid-cols-2 gap-6">
                    <x-daisy::ui.collapse title="Forced open" :bordered="true" :bg="true" force="open" />
                    <x-daisy::ui.collapse title="Forced close" :bordered="true" :bg="true" force="close" />
                </div>
            </div>
        </section>

        <!-- Accordion -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Accordion</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Arrow style -->
            <x-daisy::ui.accordion :items="[
                    ['title' => 'Comment créer un compte ?', 'content' => 'Cliquez sur S\'inscrire...','checked' => true],
                    ['title' => 'Mot de passe oublié ?', 'content' => 'Utilisez le lien mot de passe oublié.'],
                    ['title' => 'Mettre à jour le profil ?', 'content' => 'Allez dans Mon compte > Éditer'],
                ]" :arrow="true" />

                <!-- Plus style + états forcés -->
                <x-daisy::ui.accordion :items="[
                    ['title' => 'Section ouverte forcée', 'content' => 'Toujours ouverte', 'open' => true],
                    ['title' => 'Section fermée forcée', 'content' => 'Toujours fermée', 'close' => true],
                    ['title' => 'Section normale', 'content' => 'S\'ouvre via radio'],
                ]" :arrow="false" />
            </div>
        </section>

        <!-- Tabs -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Tabs</h2>
            <div class="space-y-6">
                <!-- Basique boxed -->
            <x-daisy::ui.tabs :items="[
                ['label' => 'Tab 1', 'active' => true],
                ['label' => 'Tab 2'],
                ['label' => 'Tab 3']
            ]" variant="boxed" />

                <!-- Border (bottom) + tailles -->
                <x-daisy::ui.tabs size="sm" variant="border" :items="[
                    ['label' => 'Small 1', 'active' => true],
                    ['label' => 'Small 2'],
                    ['label' => 'Small 3', 'disabled' => true]
                ]" />
                <x-daisy::ui.tabs size="lg" variant="lifted" :items="[
                    ['label' => 'Large 1', 'active' => true],
                    ['label' => 'Large 2'],
                    ['label' => 'Large 3']
                ]" />

                <!-- Placement bottom -->
                <x-daisy::ui.tabs placement="bottom" variant="boxed" :items="[
                    ['label' => 'Bottom 1', 'active' => true],
                    ['label' => 'Bottom 2'],
                ]" />

                <!-- Radio + contenus -->
                <x-daisy::ui.tabs variant="lifted" radioName="my_tabs_demo" :items="[
                    ['label' => 'Tab A', 'active' => true, 'content' => 'Contenu A'],
                    ['label' => 'Tab B', 'content' => 'Contenu B'],
                    ['label' => 'Tab C', 'content' => 'Contenu C']
                ]" />
            </div>
        </section>

        <!-- Swap -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Swap</h2>
            <div class="flex flex-wrap items-center gap-6">
                <!-- Icônes avec rotation -->
            <x-daisy::ui.swap :rotate="true">
                <x-slot:on>
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </x-slot:on>
                <x-slot:off>
                    <x-heroicon-o-heart class="w-6 h-6" />
                </x-slot:off>
            </x-daisy::ui.swap>

                <!-- Texte ON/OFF -->
                <x-daisy::ui.swap>
                    <x-slot:on>ON</x-slot:on>
                    <x-slot:off>OFF</x-slot:off>
                </x-daisy::ui.swap>

                <!-- Flip avec emoji -->
                <x-daisy::ui.swap :flip="true" class="text-4xl">
                    <x-slot:on>😈</x-slot:on>
                    <x-slot:off>😇</x-slot:off>
                </x-daisy::ui.swap>

                <!-- Activation via classe (pas de checkbox) -->
                <x-daisy::ui.swap :active="true" :useInput="false">
                    <x-slot:on>🥳</x-slot:on>
                    <x-slot:off>😭</x-slot:off>
                </x-daisy::ui.swap>
            </div>
        </section>

        <!-- Menu -->
        <section class="space-y-6 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Menu</h2>
            <div class="space-y-6">
            <div class="grid md:grid-cols-2 gap-6">
                    <x-daisy::ui.menu class="w-56" title="Menu">
                    <li><a class="font-semibold">Item 1</a></li>
                    <li>
                        <details>
                            <summary>Parent</summary>
                                <ul class="menu-dropdown">
                                <li><a>Submenu 1</a></li>
                                <li><a>Submenu 2</a></li>
                            </ul>
                        </details>
                    </li>
                        <li class="menu-disabled"><a>Disabled</a></li>
                        <li><a class="menu-active">Active</a></li>
                    <li><a>Item 3</a></li>
                </x-daisy::ui.menu>
                <x-daisy::ui.menu :vertical="false" class="bg-base-100 rounded-box">
                    <li><a>Accueil</a></li>
                    <li><a>Docs</a></li>
                    <li><a>Contact</a></li>
                    </x-daisy::ui.menu>
                </div>
                <x-daisy::ui.menu :vertical="true" horizontalAt="lg" class="bg-base-100 rounded-box">
                    <li><a>Item 1</a></li>
                    <li><a>Item 2</a></li>
                    <li><a>Item 3</a></li>
                </x-daisy::ui.menu>
            </div>
        </section>

        <!-- Navbar -->
        <section class="space-y-6 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Navbar</h2>
            <div class="space-y-4">
                <!-- Simple titre -->
                <x-daisy::ui.navbar shadow="sm">
                <x-slot:start>
                    <a class="btn btn-ghost text-xl">DaisyKit</a>
                </x-slot:start>
                </x-daisy::ui.navbar>

                <!-- Titre + icône fin -->
                <x-daisy::ui.navbar shadow="sm">
                    <x-slot:start>
                        <a class="btn btn-ghost text-xl">DaisyKit</a>
                    </x-slot:start>
                    <x-slot:end>
                        <button class="btn btn-square btn-ghost">
                            <x-heroicon-o-ellipsis-horizontal class="h-5 w-5" />
                        </button>
                    </x-slot:end>
                </x-daisy::ui.navbar>

                <!-- Icônes start/end + menu center responsive -->
                <x-daisy::ui.navbar bg="base-100" shadow="sm" centerHiddenBelow="lg">
                    <x-slot:start>
                        <button class="btn btn-square btn-ghost">
                            <x-heroicon-o-bars-3 class="h-5 w-5" />
                        </button>
                        <a class="btn btn-ghost text-xl">DaisyKit</a>
                    </x-slot:start>
                <x-slot:center>
                    <x-daisy::ui.menu :vertical="false" class="px-1">
                        <li><a>Item 1</a></li>
                            <li>
                                <details>
                                    <summary>Parent</summary>
                                    <ul class="p-2">
                                        <li><a>Submenu 1</a></li>
                                        <li><a>Submenu 2</a></li>
                                    </ul>
                                </details>
                            </li>
                            <li><a>Item 3</a></li>
                    </x-daisy::ui.menu>
                </x-slot:center>
                <x-slot:end>
                        <x-daisy::ui.button>Button</x-daisy::ui.button>
                </x-slot:end>
            </x-daisy::ui.navbar>

                <!-- Couleurs -->
                <x-daisy::ui.navbar bg="neutral" text="neutral-content">
                    <x-slot:start>
                        <button class="btn btn-ghost text-xl">DaisyKit</button>
                    </x-slot:start>
                </x-daisy::ui.navbar>
                <x-daisy::ui.navbar bg="base-300">
                    <x-slot:start>
                        <button class="btn btn-ghost text-xl">DaisyKit</button>
                    </x-slot:start>
                </x-daisy::ui.navbar>
                <x-daisy::ui.navbar bg="primary" text="primary-content">
                    <x-slot:start>
                        <button class="btn btn-ghost text-xl">DaisyKit</button>
                    </x-slot:start>
                </x-daisy::ui.navbar>
            </div>
        </section>

        <!-- Drawer -->
        <section class="space-y-6 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Drawer</h2>
            <div class="space-y-6">
                <!-- Basique -->
            <x-daisy::ui.drawer id="demo-drawer">
                <x-slot:content>
                    <label for="demo-drawer" class="btn btn-primary">Open drawer</label>
                </x-slot:content>
                <x-slot:side>
                    <li><a>Sidebar item 1</a></li>
                    <li><a>Sidebar item 2</a></li>
                </x-slot:side>
            </x-daisy::ui.drawer>

                <!-- Drawer end (droite) -->
                <x-daisy::ui.drawer id="demo-drawer-end" :end="true">
                    <x-slot:content>
                        <label for="demo-drawer-end" class="btn">Open right drawer</label>
                    </x-slot:content>
                    <x-slot:side>
                        <li><a>Right item 1</a></li>
                        <li><a>Right item 2</a></li>
                    </x-slot:side>
                </x-daisy::ui.drawer>

                <!-- Drawer open sur breakpoint (sidebar visible en lg) -->
                <x-daisy::ui.drawer id="demo-drawer-lg" responsiveOpen="lg">
                    <x-slot:content>
                        <div class="bg-base-300 w-full p-3 flex items-center justify-between">
                            <div class="font-semibold">Responsive drawer</div>
                            <label for="demo-drawer-lg" aria-label="open sidebar" class="btn btn-square btn-ghost lg:hidden">
                                <x-heroicon-o-bars-3 class="h-6 w-6" />
                            </label>
                        </div>
                        <div class="p-4 space-y-2">
                            <p>Content area. En grand écran, la sidebar reste ouverte.</p>
                            <p>Réduisez la fenêtre pour voir le bouton d’ouverture.</p>
                        </div>
                    </x-slot:content>
                    <x-slot:side>
                        <li><a>Sidebar Item 1</a></li>
                        <li><a>Sidebar Item 2</a></li>
                    </x-slot:side>
                </x-daisy::ui.drawer>
            </div>
        </section>

        <!-- Pagination -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Pagination</h2>
            <div class="space-y-6">
                <!-- Basique avec couleur -->
                <x-daisy::ui.pagination :total="7" :current="3" color="primary" />

                <!-- Petite taille + fenêtre réduite + outline -->
                <x-daisy::ui.pagination :total="12" :current="6" size="sm" :maxButtons="5" color="secondary" :outline="true" />

                <!-- Contrôles Previous/Next égaux + outline -->
                <x-daisy::ui.pagination :equalPrevNext="true" :outlinePrevNext="true" color="accent" prevLabel="Previous" nextLabel="Next" />

                <!-- XL, neutre, avec extrémités masquées -->
                <x-daisy::ui.pagination :total="15" :current="10" size="lg" :edges="false" color="neutral" />
            </div>
        </section>

        <!-- Table -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Table</h2>
            <div class="space-y-4">
                <!-- Basique -->
                <x-daisy::ui.table :headers="['','Name','Job','Favorite Color']"
                    :rows="[
                        ['1','Cy Ganderton','Quality Control Specialist','Blue'],
                        ['2','Hart Hagerty','Desktop Support Technician','Purple'],
                        ['3','Brice Swyre','Tax Accountant','Red'],
                    ]"
                    :rowHeaders="true"
                />

                <!-- Avec bordure et fond -->
                <x-daisy::ui.table containerClass="rounded-box border border-base-content/5 bg-base-100"
                    :headers="['','Name','Job','Favorite Color']"
                    :rows="[
                        ['1','Cy Ganderton','Quality Control Specialist','Blue'],
                        ['2','Hart Hagerty','Desktop Support Technician','Purple'],
                        ['3','Brice Swyre','Tax Accountant','Red'],
                    ]"
                    :rowHeaders="true"
                />

                <!-- Zebra + tailles + pin -->
                <x-daisy::ui.table zebra size="sm" :pinRows="true" :pinCols="true"
                    :headers="['','Name','Job','Company','Location','Last Login','Favorite Color','']"
                    :rows="[
                        ['1','Cy Ganderton','Quality Control Specialist','Littel, Schaden and Vandervort','Canada','12/16/2020','Blue','1'],
                        ['2','Hart Hagerty','Desktop Support Technician','Zemlak, Daniel and Leannon','United States','12/5/2020','Purple','2'],
                        ['3','Brice Swyre','Tax Accountant','Carroll Group','China','8/15/2020','Red','3'],
                    ]"
                    :footer="['','Name','Job','Company','Location','Last Login','Favorite Color','']"
                    :rowHeaders="true"
                />
            </div>
        </section>

        <!-- List -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">List</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Exemple simple -->
                <x-daisy::ui.list :bg="true" :rounded="true" shadow="md" title="Most played songs this week">
                    <x-daisy::ui.list-row>
                        <div><img class="w-10 h-10 rounded-box" src="https://i.pravatar.cc/80?img=48" alt="Avatar"/></div>
                        <div>
                            <div>Dio Lupa</div>
                            <div class="text-xs uppercase font-semibold opacity-60">Remaining Reason</div>
                        </div>
                        <button class="btn btn-square btn-ghost">▶</button>
                        <button class="btn btn-square btn-ghost">❤</button>
                    </x-daisy::ui.list-row>
                    <x-daisy::ui.list-row>
                        <div><img class="w-10 h-10 rounded-box" src="https://i.pravatar.cc/80?img=51" alt="Avatar"/></div>
                        <div>
                            <div>Ellie Beilish</div>
                            <div class="text-xs uppercase font-semibold opacity-60">Bears of a fever</div>
                        </div>
                        <button class="btn btn-square btn-ghost">▶</button>
                        <button class="btn btn-square btn-ghost">❤</button>
                    </x-daisy::ui.list-row>
                    <x-daisy::ui.list-row>
                        <div><img class="w-10 h-10 rounded-box" src="https://i.pravatar.cc/80?img=49" alt="Avatar"/></div>
                        <div>
                            <div>Sabrino Gardener</div>
                            <div class="text-xs uppercase font-semibold opacity-60">Cappuccino</div>
                        </div>
                        <button class="btn btn-square btn-ghost">▶</button>
                        <button class="btn btn-square btn-ghost">❤</button>
                    </x-daisy::ui.list-row>
                </x-daisy::ui.list>

                <!-- Exemple avec list-col-wrap (colonne qui passe à la ligne) -->
                <x-daisy::ui.list :bg="true" :rounded="true" shadow="md" title="With descriptions">
                    <x-daisy::ui.list-row>
                        <div><img class="w-10 h-10 rounded-box" src="https://i.pravatar.cc/80?img=48" alt="Avatar"/></div>
                        <div>
                            <div>Dio Lupa</div>
                            <div class="text-xs uppercase font-semibold opacity-60">Remaining Reason</div>
                        </div>
                        <p class="list-col-wrap text-xs opacity-80">
                            "Remaining Reason" blends introspective lyrics with a dynamic beat, becoming one of Dio Lupa’s most iconic tracks.
                        </p>
                        <button class="btn btn-square btn-ghost">▶</button>
                        <button class="btn btn-square btn-ghost">❤</button>
                    </x-daisy::ui.list-row>
                    <x-daisy::ui.list-row>
                        <div><img class="w-10 h-10 rounded-box" src="https://i.pravatar.cc/80?img=51" alt="Avatar"/></div>
                        <div>
                            <div>Ellie Beilish</div>
                            <div class="text-xs uppercase font-semibold opacity-60">Bears of a fever</div>
                        </div>
                        <p class="list-col-wrap text-xs opacity-80">
                            "Bears of a Fever" captivated audiences with its intense energy and mysterious lyrics.
                        </p>
                        <button class="btn btn-square btn-ghost">▶</button>
                        <button class="btn btn-square btn-ghost">❤</button>
                    </x-daisy::ui.list-row>
                </x-daisy::ui.list>
            </div>
        </section>

        <!-- Join -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Join</h2>
            <div class="space-y-8">
                <!-- Barre de recherche combinée -->
                <x-daisy::ui.join class="w-full">
                    <input class="input input-bordered join-item w-full max-w-xs" placeholder="Search" />
                    <select class="select select-bordered join-item">
                        <option disabled selected>Filter</option>
                        <option>All</option>
                        <option>Articles</option>
                        <option>Users</option>
                    </select>
                    <button class="btn btn-primary join-item">Search</button>
                </x-daisy::ui.join>

                <!-- Pagination compacte (exemple réaliste) -->
                <x-daisy::ui.join>
                    <button class="btn join-item">«</button>
                    <button class="btn join-item btn-active">1</button>
                    <button class="btn join-item">2</button>
                    <button class="btn join-item">3</button>
                    <button class="btn join-item">»</button>
                </x-daisy::ui.join>

                <!-- Groupe d’actions (vertical en mobile, horizontal en lg) -->
                <x-daisy::ui.join direction="vertical" horizontalAt="lg">
                    <button class="btn join-item">Save draft</button>
                    <button class="btn join-item">Preview</button>
                    <button class="btn btn-primary join-item">Publish</button>
                </x-daisy::ui.join>

                <!-- Call to action avec rayon custom -->
                <x-daisy::ui.join>
                    <input class="input input-bordered join-item w-full max-w-xs" placeholder="Email" />
                    <button class="btn btn-accent join-item rounded-r-full">Subscribe</button>
                </x-daisy::ui.join>

                <!-- Segmented control (radios) -->
                <x-daisy::ui.join>
                    <input class="join-item btn" type="radio" name="seg-join-demo" aria-label="Daily" />
                    <input class="join-item btn" type="radio" name="seg-join-demo" aria-label="Weekly" />
                    <input class="join-item btn" type="radio" name="seg-join-demo" aria-label="Monthly" />
                </x-daisy::ui.join>
            </div>
        </section>

        <!-- Fieldset -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Fieldset</h2>
            <div class="space-y-6">
                <!-- Basique -->
                <x-daisy::ui.fieldset legend="Page title">
                    <x-daisy::ui.input placeholder="My awesome page" />
                    <p class="label">You can edit page title later on from settings</p>
            </x-daisy::ui.fieldset>

                <!-- Background + border + rounded + width + padding -->
                <x-daisy::ui.fieldset legend="Page details" bg="base-200" :bordered="true" width="w-xs" padding="p-4">
                    <label class="label">Title</label>
                    <x-daisy::ui.input placeholder="My awesome page" />
                    <label class="label">Slug</label>
                    <x-daisy::ui.input placeholder="my-awesome-page" />
                    <label class="label">Author</label>
                    <x-daisy::ui.input placeholder="Name" />
                </x-daisy::ui.fieldset>

                <!-- Join items -->
                <x-daisy::ui.fieldset legend="Settings" bg="base-200" :bordered="true" width="w-xs" padding="p-4">
                    <div class="join">
                        <input type="text" class="input join-item" placeholder="Product name" />
                        <button class="btn join-item">save</button>
                    </div>
                </x-daisy::ui.fieldset>

                <!-- Login -->
                <x-daisy::ui.fieldset legend="Login" bg="base-200" :bordered="true" width="w-xs" padding="p-4">
                    <label class="label">Email</label>
                    <input type="email" class="input" placeholder="Email" />
                    <label class="label">Password</label>
                    <input type="password" class="input" placeholder="Password" />
                    <button class="btn btn-neutral mt-4">Login</button>
                </x-daisy::ui.fieldset>
            </div>
        </section>

        <!-- File Input -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">File Input</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <!-- Default -->
                <x-daisy::ui.file-input />
                <!-- Ghost -->
                <x-daisy::ui.file-input variant="ghost" />
                <!-- Colors -->
                <x-daisy::ui.file-input color="primary" />
                <x-daisy::ui.file-input color="secondary" />
                <x-daisy::ui.file-input color="accent" />
                <x-daisy::ui.file-input color="neutral" />
                <x-daisy::ui.file-input color="info" />
                <x-daisy::ui.file-input color="success" />
                <x-daisy::ui.file-input color="warning" />
                <x-daisy::ui.file-input color="error" />
                <!-- Sizes -->
                <x-daisy::ui.file-input size="xs" />
                <x-daisy::ui.file-input size="sm" />
                <x-daisy::ui.file-input size="md" />
                <x-daisy::ui.file-input size="lg" />
                <x-daisy::ui.file-input size="xl" />
            </div>
        </section>

        <!-- Range -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Range Slider</h2>
            <div class="space-y-6">
                <div class="grid md:grid-cols-5 gap-6">
                    <x-daisy::ui.range value="40" />
                    <x-daisy::ui.range value="40" color="primary" />
                    <x-daisy::ui.range value="40" color="secondary" />
                    <x-daisy::ui.range value="40" color="accent" />
                    <x-daisy::ui.range value="40" color="neutral" />
                    <x-daisy::ui.range value="40" color="info" />
                    <x-daisy::ui.range value="40" color="success" />
                    <x-daisy::ui.range value="40" color="warning" />
                    <x-daisy::ui.range value="40" color="error" />
                </div>
                <div class="grid md:grid-cols-5 gap-6">
                    <x-daisy::ui.range value="30" size="xs" />
                    <x-daisy::ui.range value="40" size="sm" />
                    <x-daisy::ui.range value="50" size="md" />
                    <x-daisy::ui.range value="60" size="lg" />
                    <x-daisy::ui.range value="70" size="xl" />
                </div>
                <div class="w-full max-w-xs">
                    <x-daisy::ui.range value="25" step="25" />
                    <div class="flex justify-between px-2.5 mt-2 text-xs">
                        <span>|</span><span>|</span><span>|</span><span>|</span><span>|</span>
                    </div>
                    <div class="flex justify-between px-2.5 mt-2 text-xs">
                        <span>1</span><span>2</span><span>3</span><span>4</span><span>5</span>
                    </div>
                </div>
                <x-daisy::ui.range value="40" class="text-blue-300" :noFill="true" bg="orange" thumb="blue" />
            </div>
        </section>

        <!-- Mask -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Mask</h2>
            <div class="flex flex-wrap items-center gap-6">
                <!-- Formes -->
                <x-daisy::ui.mask shape="squircle" src="https://picsum.photos/seed/1/96" class="w-24 h-24" />
                <x-daisy::ui.mask shape="heart" src="https://picsum.photos/seed/2/96" class="w-24 h-24" />
                <x-daisy::ui.mask shape="hexagon" src="https://picsum.photos/seed/3/96" class="w-24 h-24" />
                <x-daisy::ui.mask shape="hexagon-2" src="https://picsum.photos/seed/4/96" class="w-24 h-24" />
                <x-daisy::ui.mask shape="decagon" src="https://picsum.photos/seed/5/96" class="w-24 h-24" />
                <x-daisy::ui.mask shape="pentagon" src="https://picsum.photos/seed/6/96" class="w-24 h-24" />
                <x-daisy::ui.mask shape="diamond" src="https://picsum.photos/seed/7/96" class="w-24 h-24" />
                <x-daisy::ui.mask shape="square" src="https://picsum.photos/seed/8/96" class="w-24 h-24" />
                <x-daisy::ui.mask shape="circle" src="https://picsum.photos/seed/9/96" class="w-24 h-24" />
                <x-daisy::ui.mask shape="star" src="https://picsum.photos/seed/10/96" class="w-24 h-24" />
                <x-daisy::ui.mask shape="star-2" src="https://picsum.photos/seed/11/96" class="w-24 h-24" />
                <x-daisy::ui.mask shape="triangle" src="https://picsum.photos/seed/12/96" class="w-24 h-24" />
                <x-daisy::ui.mask shape="triangle-2" src="https://picsum.photos/seed/13/96" class="w-24 h-24" />
                <x-daisy::ui.mask shape="triangle-3" src="https://picsum.photos/seed/14/96" class="w-24 h-24" />
                <x-daisy::ui.mask shape="triangle-4" src="https://picsum.photos/seed/15/96" class="w-24 h-24" />

                <!-- Modificateurs half -->
                <x-daisy::ui.mask shape="star" half="first" src="https://picsum.photos/seed/16/96" class="w-24 h-24" />
                <x-daisy::ui.mask shape="star" half="second" src="https://picsum.photos/seed/17/96" class="w-24 h-24" />
            </div>
        </section>

        <!-- Hero -->
        <section class="space-y-6 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Hero</h2>
            <div class="space-y-6">
                <!-- Centered hero -->
                <x-daisy::ui.hero bg="base-200" :fullScreen="false">
                    <h1 class="text-5xl font-bold">Hello there</h1>
                    <p class="py-6">Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem quasi.</p>
                    <x-daisy::ui.button color="primary">Get Started</x-daisy::ui.button>
                </x-daisy::ui.hero>

                <!-- With figure -->
                <x-daisy::ui.hero bg="base-200" :row="true">
                    <x-slot:figure></x-slot:figure>
                    <img src="https://picsum.photos/seed/hero1/600/400" class="max-w-sm rounded-lg shadow-2xl" />
                    <h1 class="text-5xl font-bold">Box Office News!</h1>
                    <p class="py-6">Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem quasi.</p>
                    <x-daisy::ui.button color="primary">Get Started</x-daisy::ui.button>
                </x-daisy::ui.hero>

                <!-- With figure reversed -->
                <x-daisy::ui.hero bg="base-200" :row="true" :reverse="true">
                    <img src="https://picsum.photos/seed/hero2/600/400" class="max-w-sm rounded-lg shadow-2xl" />
                    <h1 class="text-5xl font-bold">Login now!</h1>
                    <p class="py-6">Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem quasi.</p>
                </x-daisy::ui.hero>

                <!-- Overlay image -->
                <x-daisy::ui.hero :overlay="true" imageUrl="https://picsum.photos/seed/overlay/1200/400" :fullScreen="true">
                    <h1 class="mb-5 text-5xl font-bold">Hello there</h1>
                    <p class="mb-5">Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem quasi.</p>
                    <x-daisy::ui.button color="primary">Get Started</x-daisy::ui.button>
                </x-daisy::ui.hero>
            </div>
        </section>

        <!-- Footer -->
        <section class="space-y-6 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Footer</h2>
            <x-daisy::ui.footer class="rounded-box">
                <nav>
                    <h6 class="footer-title">Services</h6>
                    <a class="link link-hover">Branding</a>
                    <a class="link link-hover">Design</a>
                    <a class="link link-hover">Marketing</a>
                </nav>
                <nav>
                    <h6 class="footer-title">Company</h6>
                    <a class="link link-hover">About</a>
                    <a class="link link-hover">Contact</a>
                </nav>
                <nav>
                    <h6 class="footer-title">Legal</h6>
                    <a class="link link-hover">Terms</a>
                    <a class="link link-hover">Privacy</a>
                </nav>
            </x-daisy::ui.footer>
        </section>

        <!-- Carousel -->
        <section class="space-y-6 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Carousel</h2>
            <div class="space-y-4">
                <!-- Snap start (par défaut) -->
                <x-daisy::ui.carousel class="w-full space-x-4 p-4 bg-base-100" align="start">
                    <div class="carousel-item"><img src="https://picsum.photos/seed/a/320/160" class="rounded-box" /></div>
                    <div class="carousel-item"><img src="https://picsum.photos/seed/b/320/160" class="rounded-box" /></div>
                    <div class="carousel-item"><img src="https://picsum.photos/seed/c/320/160" class="rounded-box" /></div>
                    <div class="carousel-item"><img src="https://picsum.photos/seed/d/320/160" class="rounded-box" /></div>
                    <div class="carousel-item"><img src="https://picsum.photos/seed/e/320/160" class="rounded-box" /></div>
                    <div class="carousel-item"><img src="https://picsum.photos/seed/f/320/160" class="rounded-box" /></div>
                    <div class="carousel-item"><img src="https://picsum.photos/seed/g/320/160" class="rounded-box" /></div>
            </x-daisy::ui.carousel>

                <!-- Snap center -->
                <x-daisy::ui.carousel class="w-full space-x-4 p-4 bg-base-100" align="center">
                    <div class="carousel-item"><img src="https://picsum.photos/seed/1/320/160" class="rounded-box" /></div>
                    <div class="carousel-item"><img src="https://picsum.photos/seed/2/320/160" class="rounded-box" /></div>
                    <div class="carousel-item"><img src="https://picsum.photos/seed/3/320/160" class="rounded-box" /></div>
                    <div class="carousel-item"><img src="https://picsum.photos/seed/4/320/160" class="rounded-box" /></div>
                    <div class="carousel-item"><img src="https://picsum.photos/seed/5/320/160" class="rounded-box" /></div>
                    <div class="carousel-item"><img src="https://picsum.photos/seed/6/320/160" class="rounded-box" /></div>
                    <div class="carousel-item"><img src="https://picsum.photos/seed/7/320/160" class="rounded-box" /></div>
                </x-daisy::ui.carousel>
            </div>
        </section>

        <!-- Chat -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Chat</h2>
            <div class="space-y-3">
                <!-- Simple -->
                <x-daisy::ui.chat-bubble align="start" name="Alice" time="12:45">
                    Salut !
                    <x-slot:avatar><img src="https://i.pravatar.cc/100?img=6" /></x-slot:avatar>
                </x-daisy::ui.chat-bubble>
                <x-daisy::ui.chat-bubble align="end" name="Bob" time="12:46">
                    Hello !
                    <x-slot:avatar><img src="https://i.pravatar.cc/100?img=7" /></x-slot:avatar>
                </x-daisy::ui.chat-bubble>

                <!-- Couleurs -->
                <div class="space-y-2">
                    <x-daisy::ui.chat-bubble align="start" color="primary">What kind of nonsense is this</x-daisy::ui.chat-bubble>
                    <x-daisy::ui.chat-bubble align="start" color="secondary">Put me on the Council and not make me a Master!??</x-daisy::ui.chat-bubble>
                    <x-daisy::ui.chat-bubble align="start" color="accent">That's never been done in the history of the Jedi.</x-daisy::ui.chat-bubble>
                    <x-daisy::ui.chat-bubble align="start" color="neutral">It's insulting!</x-daisy::ui.chat-bubble>
                    <x-daisy::ui.chat-bubble align="end" color="info">Calm down, Anakin.</x-daisy::ui.chat-bubble>
                    <x-daisy::ui.chat-bubble align="end" color="success">You have been given a great honor.</x-daisy::ui.chat-bubble>
                    <x-daisy::ui.chat-bubble align="end" color="warning">To be on the Council at your age.</x-daisy::ui.chat-bubble>
                    <x-daisy::ui.chat-bubble align="end" color="error">It's never happened before.</x-daisy::ui.chat-bubble>
                </div>

                <!-- Slots header/footer -->
                <x-daisy::ui.chat-bubble align="start">
                    Using header and footer slots
                    <x-slot:header>
                        Yoda <time class="text-xs opacity-50">13:00</time>
                    </x-slot:header>
                    <x-slot:footer>
                        Delivered
                    </x-slot:footer>
                </x-daisy::ui.chat-bubble>
            </div>
        </section>

        <!-- Countdown -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Countdown</h2>
            <div class="space-y-3">
                <x-daisy::ui.countdown :values="['days' => 15, 'hours' => 10, 'min' => 24, 'sec' => 39]" size="lg" />
                <x-daisy::ui.countdown :values="['h' => 10, 'm' => 24, 's' => 59]" mode="inline" size="lg" />
                <x-daisy::ui.countdown :values="['h' => 10, 'm' => 24, 's' => 59]" mode="inline-colon" size="md" />
            </div>
        </section>

        <!-- Diff -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Diff</h2>
            <div class="space-y-4">
                <!-- Exemple images (comme la doc) -->
                <x-daisy::ui.diff aspect="aspect-16/9" :resizable="true">
                <x-slot:before>
                        <img alt="daisy" src="https://picsum.photos/seed/daisy/200/200" />
                </x-slot:before>
                <x-slot:after>
                        <img alt="daisy" src="https://picsum.photos/seed/daisyblur/200/200?blur=2" />
                </x-slot:after>
            </x-daisy::ui.diff>

                <!-- Exemple texte (comme la doc) -->
                <x-daisy::ui.diff aspect="aspect-16/9" :resizable="true">
                    <x-slot:before>
                        <div class="bg-primary text-primary-content grid place-content-center text-6xl md:text-9xl font-black">
                            DAISY
                        </div>
                    </x-slot:before>
                    <x-slot:after>
                        <div class="bg-base-200 grid place-content-center text-6xl md:text-9xl font-black">
                            DAISY
                        </div>
                    </x-slot:after>
                </x-daisy::ui.diff>
            </div>
        </section>

        

        <!-- Dock (à la demande) -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Dock (à la demande)</h2>
            <div class="flex gap-2">
                <button id="showDockBtn" class="btn btn-primary btn-sm">Afficher le dock</button>
                <button id="hideDockBtn" class="btn btn-ghost btn-sm">Masquer le dock</button>
            </div>
            <x-daisy::ui.dock id="onDemandDock" as="nav" label="Bottom navigation" mobile position="bottom" size="sm" class="hidden z-50 bg-neutral text-neutral-content">
                <button class="dock-item">
                    <x-heroicon-o-home class="size-5" />
                    <span class="dock-label">Accueil</span>
                </button>
                <button class="dock-item dock-active">
                    <x-heroicon-o-inbox class="size-5" />
                    <span class="dock-label">Inbox</span>
                </button>
                <button id="closeDockBtn" class="dock-item">
                    <x-heroicon-o-x-mark class="size-5" />
                    <span class="dock-label">Fermer</span>
                </button>
            </x-daisy::ui.dock>
        </section>

        <!-- Mockup Browser -->
        <section class="space-y-6 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Mockup Browser</h2>
            <div class="space-y-6">
                <!-- Basique -->
                <x-daisy::ui.mockup-browser url="https://example.com" class="rounded-box">
                    <div class="p-6">Contenu</div>
                </x-daisy::ui.mockup-browser>

                <!-- Personnalisation couleurs + toolbar custom -->
                <x-daisy::ui.mockup-browser url="https://docs.example.com" bg="base-100" contentBg="base-200" :bordered="true" class="rounded-box">
                    <x-slot:toolbar>
                        <div class="breadcrumbs text-sm">
                          <ul>
                            <li><a>Home</a></li>
                            <li><a>Docs</a></li>
                            <li>Getting started</li>
                          </ul>
                        </div>
                    </x-slot:toolbar>
                    <div class="p-6">Page content</div>
                </x-daisy::ui.mockup-browser>

                <!-- Sans toolbar -->
                <x-daisy::ui.mockup-browser :showToolbar="false" bg="base-300" contentBg="base-100" class="rounded-box">
                    <div class="p-6">No toolbar</div>
                </x-daisy::ui.mockup-browser>
            </div>
        </section>

        <!-- Mockup Code -->
        <section class="space-y-6 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Mockup Code</h2>
            <div class="space-y-6">
                <!-- Avec préfixe -->
                <x-daisy::ui.mockup-code :lines="[
                    ['prefix' => '$', 'text' => 'npm i daisyui']
                ]" class="w-full" />

                <!-- Multi-lignes + couleurs -->
                <x-daisy::ui.mockup-code :lines="[
                    ['prefix' => '$', 'text' => 'npm i daisyui'],
                    ['prefix' => '>', 'text' => 'installing...', 'class' => 'text-warning'],
                    ['prefix' => '>', 'text' => 'Done!', 'class' => 'text-success']
                ]" class="w-full" />

                <!-- Ligne surlignée -->
                <x-daisy::ui.mockup-code :lines="[
                    ['prefix' => '1', 'text' => 'npm i daisyui'],
                    ['prefix' => '2', 'text' => 'installing...'],
                    ['prefix' => '3', 'text' => 'Error!', 'highlight' => 'warning']
                ]" class="w-full" />

                <!-- Longues lignes -->
                <x-daisy::ui.mockup-code :lines="[
                    ['prefix' => '~', 'text' => 'Magnam dolore beatae necessitatibus nemopsum itaque sit. Et porro quae qui et et dolore ratione.']
                ]" class="w-full" />

                <!-- Sans préfixe -->
                <x-daisy::ui.mockup-code :lines="[
                    ['text' => 'without prefix']
                ]" class="w-full" />

                <!-- Avec couleur de fond -->
                <x-daisy::ui.mockup-code class="bg-primary text-primary-content w-full">
                    <pre><code>can be any color!</code></pre>
                </x-daisy::ui.mockup-code>
            </div>
        </section>

        <!-- Mockup Phone -->
        <section class="space-y-6 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Mockup Phone</h2>
            <div class="grid md:grid-cols-3 gap-6 items-start">
                <!-- Basique texte centré -->
                <x-daisy::ui.mockup-phone class="text-white grid place-content-center">
                    It's Glowtime.
                </x-daisy::ui.mockup-phone>

                <!-- Avec couleur de bordure + wallpaper -->
                <x-daisy::ui.mockup-phone borderColor="primary" wallpaper="https://picsum.photos/seed/wall/300/600" />

                <!-- Sans camera + contenu custom -->
                <x-daisy::ui.mockup-phone :camera="false" displayClass="bg-base-100 grid place-content-center">
                    <div class="text-center p-4">
                        <div class="text-2xl font-bold">My App</div>
                        <div class="text-sm opacity-70">Welcome back</div>
                        <button class="btn btn-primary mt-4">Open</button>
                    </div>
                </x-daisy::ui.mockup-phone>
            </div>
        </section>

        <!-- Mockup Window -->
        <section class="space-y-6 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Mockup Window</h2>
            <div class="space-y-6">
                <!-- Avec bordures (exemple doc) -->
                <x-daisy::ui.mockup-window bordered="true" bg="base-100" borderColor="base-300" contentTopBorder="true" contentTopBorderColor="base-300" class="w-full">
                    <div class="grid place-content-center h-80">Hello!</div>
                </x-daisy::ui.mockup-window>

                <!-- Fond personnalisé sans bordure supérieure -->
                <x-daisy::ui.mockup-window bordered="true" bg="base-100" borderColor="base-300" contentBg="base-100" class="w-full">
                    <div class="grid place-content-center h-80">Hello!</div>
                </x-daisy::ui.mockup-window>
            </div>
        </section>

        <!-- Status -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Status</h2>
            <div class="space-y-3">
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2">
                        <x-daisy::ui.status color="success" label="online" />
                    <span>En ligne</span>
                </div>
                <div class="flex items-center gap-2">
                        <x-daisy::ui.status color="warning" label="busy" />
                    <span>Occupé</span>
                </div>
                <div class="flex items-center gap-2">
                        <x-daisy::ui.status color="error" label="offline" />
                    <span>Hors ligne</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <x-daisy::ui.status size="xs" />
                    <x-daisy::ui.status size="sm" />
                    <x-daisy::ui.status size="md" />
                    <x-daisy::ui.status size="lg" />
                    <x-daisy::ui.status size="xl" />
                </div>
                <div class="flex items-center gap-6">
                    <div class="inline-grid *:[grid-area:1/1]">
                        <x-daisy::ui.status color="error" class="animate-ping" as="div" />
                        <x-daisy::ui.status color="error" as="div" />
                    </div>
                    <span>Server is down</span>
                </div>
                <div class="flex items-center gap-3">
                    <x-daisy::ui.status color="info" class="animate-bounce" />
                    <span>Unread messages</span>
                </div>
            </div>
        </section>

        <!-- Calendar -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Calendar</h2>
            <div class="space-y-6">
                <!-- Cally (via npm) -->
                <div class="bg-base-100 border border-base-300 shadow-lg rounded-box p-4">
                    <x-daisy::ui.calendar provider="cally" class="cally" />
                </div>

                <!-- Native input type=date -->
                <x-daisy::ui.calendar provider="native" value="" class="w-56" />
            </div>
        </section>

        <!-- Filter -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Filter</h2>
            <div class="space-y-4">
                <!-- Avec form + bouton reset -->
                <x-daisy::ui.filter useForm="true" name="frameworks" :items="[
                    ['label' => 'Svelte', 'checked' => true],
                    'Vue',
                    'React'
                ]" />

                <!-- Sans form + radio reset -->
                <x-daisy::ui.filter :useForm="false" name="metaframeworks" allLabel="All" :items="[
                    'Sveltekit', 'Nuxt', 'Next.js'
                ]" />
            </div>
        </section>

        <!-- Validator -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Validator</h2>
            <!-- Email requis avec hint -->
            <div>
                <input class="input validator" type="email" required placeholder="[email protected]" />
                <div class="validator-hint">Enter valid email address</div>
            </div>

            <!-- Mot de passe (pattern) -->
            <div>
                <input type="password" class="input validator" required placeholder="Password" minlength="8" 
                  pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                  title="Must be more than 8 characters, including number, lowercase letter, uppercase letter" />
                <p class="validator-hint">Must be more than 8 characters, including<br/>number, lowercase, uppercase</p>
            </div>

            <!-- Username -->
            <div>
                <input type="text" class="input validator" required placeholder="Username"
                  pattern="[A-Za-z][A-Za-z0-9\-]*" minlength="3" maxlength="30" title="Only letters, numbers or dash" />
                <p class="validator-hint">3–30 chars, letters, numbers or dash</p>
            </div>

            <!-- Select requis -->
            <form class="space-y-2">
                <select class="select validator" required>
                    <option disabled selected value="">Choose:</option>
                    <option>Tabs</option>
                    <option>Spaces</option>
                </select>
                <p class="validator-hint">Required</p>
                <button class="btn" type="submit">Submit form</button>
            </form>
        </section>
    </div>
    <script>
        // Dock on demand
        (function() {
            const showBtn = document.getElementById('showDockBtn');
            const hideBtn = document.getElementById('hideDockBtn');
            const closeBtn = document.getElementById('closeDockBtn');
            const dockEl = document.getElementById('onDemandDock');
            function showDock() { if (dockEl) dockEl.classList.remove('hidden'); }
            function hideDock() { if (dockEl) dockEl.classList.add('hidden'); }
            if (showBtn) showBtn.addEventListener('click', showDock);
            if (hideBtn) hideBtn.addEventListener('click', hideDock);
            if (closeBtn) closeBtn.addEventListener('click', hideDock);
        })();

        // Toast triggers
        (function() {
            const container = document.getElementById('toastContainer');
            function triggerToast(color, title, message, timeout = 3000) {
                if (!container) return;
                const toast = document.createElement('div');
                toast.className = `alert alert-${color}`;
                toast.innerHTML = `<div class=\"flex-1\">${title ? `<h3 class=\\\"font-medium\\\">${title}</h3>` : ''}<div class=\"text-sm\">${message}</div></div>`;
                container.appendChild(toast);
                window.setTimeout(() => {
                    toast.classList.add('opacity-0','transition','duration-300');
                    window.setTimeout(() => toast.remove(), 300);
                }, timeout);
            }
            window.triggerToast = triggerToast;
        })();
    </script>
</x-daisy::layout.app>

