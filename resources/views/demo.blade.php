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

            function applyTheme(theme) {
                htmlEl.setAttribute('data-theme', theme);
                try { localStorage.setItem(THEME_KEY, theme); } catch (_) {}
            }

            function init() {
                let saved = null;
                try { saved = localStorage.getItem(THEME_KEY); } catch (_) {}
                const current = saved || htmlEl.getAttribute('data-theme') || 'light';
                applyTheme(current);
                if (themeSelect) themeSelect.value = current;
            }

            if (themeSelect) {
                themeSelect.addEventListener('change', (e) => {
                    applyTheme(e.target.value);
                });
            }

            document.addEventListener('DOMContentLoaded', init);
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
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-daisy::ui.button size="xs" color="primary">XS</x-daisy::ui.button>
                    <x-daisy::ui.button size="sm" color="primary">SM</x-daisy::ui.button>
                    <x-daisy::ui.button size="md" color="primary">MD</x-daisy::ui.button>
                    <x-daisy::ui.button size="lg" color="primary">LG</x-daisy::ui.button>
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
                    <x-daisy::ui.input size="sm" placeholder="Small" />
                    <x-daisy::ui.input placeholder="Medium" />
                    <x-daisy::ui.input size="lg" placeholder="Large" />
                </div>
                <div class="grid md:grid-cols-3 gap-3">
                    <x-daisy::ui.input placeholder="Disabled" disabled />
                    <x-daisy::ui.input placeholder="Error" color="error" />
                    <x-daisy::ui.input placeholder="Success" color="success" />
                </div>
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
                <div class="grid grid-cols-3 gap-3">
                    <x-daisy::ui.textarea size="sm" rows="2" placeholder="Small" />
                    <x-daisy::ui.textarea rows="3" placeholder="Medium" />
                    <x-daisy::ui.textarea size="lg" rows="4" placeholder="Large" />
                </div>
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
                <x-daisy::ui.checkbox size="sm" />
                <x-daisy::ui.checkbox size="lg" />
            </div>
        </section>

        <!-- Radio -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Radio</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-daisy::ui.radio name="demo-radio" value="a" :checked="true" />
                <x-daisy::ui.radio name="demo-radio" value="b" color="primary" />
                <x-daisy::ui.radio name="demo-radio" value="c" :disabled="true" />
                <x-daisy::ui.radio name="demo-radio2" value="d" size="sm" />
                <x-daisy::ui.radio name="demo-radio2" value="e" size="lg" />
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
                <x-daisy::ui.toggle size="sm" />
                <x-daisy::ui.toggle size="lg" />
            </div>
        </section>

        <!-- Loading -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Loading</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-daisy::ui.loading />
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
                <x-daisy::ui.link href="#">Default</x-daisy::ui.link>
                <x-daisy::ui.link href="#" color="primary">Primary</x-daisy::ui.link>
                <x-daisy::ui.link href="#" color="error">Error</x-daisy::ui.link>
                <x-daisy::ui.link href="https://daisyui.com" external>
                    Externe
                </x-daisy::ui.link>
            </div>
        </section>

        <!-- Badges -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Badges</h2>
            <div class="space-y-2">
                <div class="flex flex-wrap gap-2 items-center">
                    <x-daisy::ui.badge color="neutral">Neutral</x-daisy::ui.badge>
                    <x-daisy::ui.badge color="primary">Primary</x-daisy::ui.badge>
                    <x-daisy::ui.badge color="success">Success</x-daisy::ui.badge>
                    <x-daisy::ui.badge color="warning">Warning</x-daisy::ui.badge>
                    <x-daisy::ui.badge color="error">Error</x-daisy::ui.badge>
                </div>
                <div class="flex flex-wrap gap-2 items-center">
                    <x-daisy::ui.badge color="primary" variant="outline">Outline</x-daisy::ui.badge>
                    <x-daisy::ui.badge color="primary" variant="ghost">Ghost</x-daisy::ui.badge>
                    <x-daisy::ui.badge color="success" variant="soft">Soft</x-daisy::ui.badge>
                </div>
                <div class="flex flex-wrap gap-2 items-center">
                    <x-daisy::ui.badge size="xs">XS</x-daisy::ui.badge>
                    <x-daisy::ui.badge size="sm">SM</x-daisy::ui.badge>
                    <x-daisy::ui.badge size="md">MD</x-daisy::ui.badge>
                    <x-daisy::ui.badge size="lg">LG</x-daisy::ui.badge>
                </div>
            </div>
        </section>

        <!-- Avatars -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Avatars</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-daisy::ui.avatar src="https://i.pravatar.cc/100?img=3" alt="Avatar" />
                <x-daisy::ui.avatar placeholder="JS" />
                <x-daisy::ui.avatar size="sm" placeholder="SM" />
                <x-daisy::ui.avatar size="lg" placeholder="LG" />
                <x-daisy::ui.avatar rounded="md" placeholder="MD" />
                <x-daisy::ui.avatar rounded="none" placeholder="--" />
            </div>
        </section>

        <!-- Divider -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Divider</h2>
            <div class="space-y-3">
                <x-daisy::ui.divider text="Ou" />
                <div class="flex gap-6">
                    <div class="h-24 w-full grid place-items-center bg-base-100 rounded-box">
                        <x-daisy::ui.divider :vertical="true">Vertical</x-daisy::ui.divider>
                    </div>
                </div>
            </div>
        </section>

        <!-- Label -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Label</h2>
            <div class="flex items-center gap-4">
                <x-daisy::ui.label for="demo-lbl" value="Label" alt="optionnel" />
            </div>
        </section>

        <!-- Kbd -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Kbd</h2>
            <div class="flex items-center gap-2">
                <x-daisy::ui.kbd>⌘</x-daisy::ui.kbd>
                <x-daisy::ui.kbd>K</x-daisy::ui.kbd>
            </div>
        </section>

        <!-- Breadcrumbs -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Breadcrumbs</h2>
            <x-daisy::ui.breadcrumbs :items="[
                ['label' => 'Home', 'href' => '/'],
                ['label' => 'Library', 'href' => '#'],
                ['label' => 'Data']
            ]" />
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
            <div class="space-y-3">
                <x-daisy::ui.alert color="info" title="Information">
                    Ceci est une alerte d'information.
                    <x-slot:actions>
                        <x-daisy::ui.button size="sm" variant="outline" color="info">OK</x-daisy::ui.button>
                    </x-slot:actions>
                </x-daisy::ui.alert>
                <x-daisy::ui.alert color="success" title="Succès">
                    Opération réussie
                </x-daisy::ui.alert>
                <x-daisy::ui.alert color="warning" title="Attention" />
                <x-daisy::ui.alert color="error" title="Erreur" variant="soft">
                    Quelque chose s'est mal passé.
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
            <x-daisy::ui.toast id="toastContainer" position="end" class="z-50"></x-daisy::ui.toast>
        </section>

        <!-- Modal -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Modal</h2>
            <div class="space-y-3">
                <x-daisy::ui.button onclick="document.getElementById('demo-modal').showModal()">Ouvrir la modal</x-daisy::ui.button>
                <x-daisy::ui.modal id="demo-modal" title="Exemple de modal">
                    Contenu de la modal.
                    <x-slot:actions>
                        <form method="dialog">
                            <x-daisy::ui.button>Fermer</x-daisy::ui.button>
                        </form>
                    </x-slot:actions>
                </x-daisy::ui.modal>
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
                <x-daisy::ui.card title="Titre" color="base-100">
                    Contenu simple
                    <x-slot:actions>
                        <x-daisy::ui.button size="sm">Action</x-daisy::ui.button>
                    </x-slot:actions>
                </x-daisy::ui.card>
                <x-daisy::ui.card title="Bordered" :bordered="true">
                    Carte avec bordure
                </x-daisy::ui.card>
                <x-daisy::ui.card title="Compact" :compact="true">
                    Moins d'espacement
                </x-daisy::ui.card>
            </div>
            <div class="grid md:grid-cols-2 gap-6">
                <x-daisy::ui.card :side="true" title="Side">
                    <x-slot:figure>
                        <img src="https://picsum.photos/seed/picsum/200/200" alt="" />
                    </x-slot:figure>
                    Carte avec image latérale
                </x-daisy::ui.card>
                <x-daisy::ui.card :imageFull="true" title="Image Full" imageUrl="https://picsum.photos/seed/daisy/600/300">
                    Texte sur image full
                </x-daisy::ui.card>
            </div>
        </section>

        <!-- Indicator -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Indicator</h2>
            <div class="flex flex-wrap items-center gap-6">
                <x-daisy::ui.indicator label="99+" position="top-end">
                    <x-daisy::ui.button>Inbox</x-daisy::ui.button>
                </x-daisy::ui.indicator>
                <x-daisy::ui.indicator position="bottom-start">
                    <x-slot:indicator>
                        <span class="badge badge-success"></span>
                    </x-slot:indicator>
                    <x-daisy::ui.avatar src="https://i.pravatar.cc/100?img=5" />
                </x-daisy::ui.indicator>
            </div>
        </section>

        <!-- Progress -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Progress</h2>
            <div class="space-y-3">
                <x-daisy::ui.progress value="25" class="max-w-md" />
                <x-daisy::ui.progress value="50" color="primary" size="sm" class="max-w-md" />
                <x-daisy::ui.progress value="75" color="success" size="lg" class="max-w-md" />
            </div>
        </section>

        <!-- Radial Progress -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Radial Progress</h2>
            <div class="flex flex-wrap items-center gap-6">
                <x-daisy::ui.radial-progress value="30" color="primary" />
                <x-daisy::ui.radial-progress value="70" color="success" size="6rem" thickness="6px" />
                <x-daisy::ui.radial-progress value="100" color="error">Done</x-daisy::ui.radial-progress>
            </div>
        </section>

        <!-- Rating -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Rating</h2>
            <div class="flex flex-wrap items-center gap-6">
                <x-daisy::ui.rating name="r1" :value="3" />
                <x-daisy::ui.rating name="r2" :value="4.5" :half="true" />
            </div>
        </section>

        <!-- Stat -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Stat</h2>
            <div class="stats bg-base-100 shadow">
                <x-daisy::ui.stat title="Téléchargements" value="31K" desc="↗︎ 400 (22%)" />
                <x-daisy::ui.stat title="Nouveaux utilisateurs" value="4,200" desc="↘︎ 90 (14%)" />
                <x-daisy::ui.stat>
                    <x-slot:figure>
                        <x-heroicon-o-heart class="w-8 h-8 text-primary" />
                    </x-slot:figure>
                    <x-slot:title>Likes</x-slot:title>
                    <x-slot:value>1.2K</x-slot:value>
                    <x-slot:desc>Augmente</x-slot:desc>
                </x-daisy::ui.stat>
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
        </section>

        <!-- Stack -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Stack</h2>
            <x-daisy::ui.stack class="h-24 w-full">
                <div class="grid h-20 w-20 place-items-center bg-primary text-primary-content">1</div>
                <div class="grid h-20 w-20 place-items-center bg-secondary text-secondary-content">2</div>
                <div class="grid h-20 w-20 place-items-center bg-accent text-accent-content">3</div>
            </x-daisy::ui.stack>
        </section>

        <!-- Timeline -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Timeline</h2>
            <x-daisy::ui.timeline :items="[
                ['when' => '2023', 'title' => 'Lancement', 'content' => 'Phase 1'],
                ['when' => '2024', 'title' => 'Améliorations', 'content' => 'Phase 2'],
                ['when' => '2025', 'title' => 'Scale', 'content' => 'Phase 3'],
            ]" />
        </section>

        <!-- Steps -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Steps</h2>
            <x-daisy::ui.steps :items="['Préparation','Commande','Livraison','Fini']" :current="2" />
        </section>

        <!-- Tooltip -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Tooltip</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-daisy::ui.tooltip text="Haut"><x-daisy::ui.button>Top</x-daisy::ui.button></x-daisy::ui.tooltip>
                <x-daisy::ui.tooltip text="Bas" position="bottom"><x-daisy::ui.button>Bottom</x-daisy::ui.button></x-daisy::ui.tooltip>
                <x-daisy::ui.tooltip text="Info" color="info" :open="true"><x-daisy::ui.button>Ouvert</x-daisy::ui.button></x-daisy::ui.tooltip>
            </div>
        </section>

        <!-- Collapse -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Collapse</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <x-daisy::ui.collapse title="Collapse (arrow)" :open="true">
                    Contenu du collapse
                </x-daisy::ui.collapse>
                <x-daisy::ui.collapse :arrow="false" title="Collapse (plus)">
                    Contenu avec +
                </x-daisy::ui.collapse>
            </div>
        </section>

        <!-- Accordion -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Accordion</h2>
            <x-daisy::ui.accordion :items="[
                ['title' => 'Item 1', 'content' => 'Texte 1'],
                ['title' => 'Item 2', 'content' => 'Texte 2'],
                ['title' => 'Item 3', 'content' => 'Texte 3'],
            ]" />
        </section>

        <!-- Tabs -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Tabs</h2>
            <x-daisy::ui.tabs :items="[
                ['label' => 'Tab 1', 'active' => true],
                ['label' => 'Tab 2'],
                ['label' => 'Tab 3']
            ]" variant="boxed" />
        </section>

        <!-- Swap -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Swap</h2>
            <x-daisy::ui.swap :rotate="true">
                <x-slot:on>
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </x-slot:on>
                <x-slot:off>
                    <x-heroicon-o-heart class="w-6 h-6" />
                </x-slot:off>
            </x-daisy::ui.swap>
        </section>

        <!-- Menu -->
        <section class="space-y-6 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Menu</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <x-daisy::ui.menu class="w-56">
                    <li><a class="font-semibold">Item 1</a></li>
                    <li>
                        <details>
                            <summary>Parent</summary>
                            <ul>
                                <li><a>Submenu 1</a></li>
                                <li><a>Submenu 2</a></li>
                            </ul>
                        </details>
                    </li>
                    <li><a>Item 3</a></li>
                </x-daisy::ui.menu>
                <x-daisy::ui.menu :vertical="false" class="bg-base-100 rounded-box">
                    <li><a>Accueil</a></li>
                    <li><a>Docs</a></li>
                    <li><a>Contact</a></li>
                </x-daisy::ui.menu>
            </div>
        </section>

        <!-- Navbar -->
        <section class="space-y-6 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Navbar</h2>
            <x-daisy::ui.navbar>
                <x-slot:start>
                    <a class="btn btn-ghost text-xl">DaisyKit</a>
                </x-slot:start>
                <x-slot:center>
                    <x-daisy::ui.menu :vertical="false" class="px-1">
                        <li><a>Item 1</a></li>
                        <li><a>Item 2</a></li>
                    </x-daisy::ui.menu>
                </x-slot:center>
                <x-slot:end>
                    <x-daisy::ui.button>Action</x-daisy::ui.button>
                </x-slot:end>
            </x-daisy::ui.navbar>
        </section>

        <!-- Drawer -->
        <section class="space-y-6 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Drawer</h2>
            <x-daisy::ui.drawer id="demo-drawer">
                <x-slot:content>
                    <label for="demo-drawer" class="btn btn-primary">Open drawer</label>
                </x-slot:content>
                <x-slot:side>
                    <li><a>Sidebar item 1</a></li>
                    <li><a>Sidebar item 2</a></li>
                </x-slot:side>
            </x-daisy::ui.drawer>
        </section>

        <!-- Pagination -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Pagination</h2>
            <x-daisy::ui.pagination :total="5" :current="2" />
        </section>

        <!-- Table -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Table</h2>
            <x-daisy::ui.table :headers="['Nom','Rôle','Statut']" :rows="[
                ['Jane','Admin','Actif'],
                ['John','User','Inactif'],
                ['Alice','Editor','Actif'],
            ]" zebra size="sm" :rowHeaders="true" />
        </section>

        <!-- List -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">List</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <x-daisy::ui.list>
                    <li class="list-row">Premier</li>
                    <li class="list-row">Deuxième</li>
                    <li class="list-row">Troisième</li>
                </x-daisy::ui.list>
                <x-daisy::ui.list>
                    <li class="list-row">Étape 1</li>
                    <li class="list-row">Étape 2</li>
                    <li class="list-row">Étape 3</li>
                </x-daisy::ui.list>
            </div>
        </section>

        <!-- Join -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Join</h2>
            <x-daisy::ui.join>
                <button class="btn join-item">Prev</button>
                <button class="btn join-item btn-active">Page 1</button>
                <button class="btn join-item">Next</button>
            </x-daisy::ui.join>
        </section>

        <!-- Fieldset -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Fieldset</h2>
            <x-daisy::ui.fieldset legend="Informations">
                <x-daisy::ui.input placeholder="Nom" class="mb-2" />
                <x-daisy::ui.input placeholder="Email" />
            </x-daisy::ui.fieldset>
        </section>

        <!-- File Input -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">File Input</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <x-daisy::ui.file-input />
                <x-daisy::ui.file-input size="sm" variant="ghost" />
                <x-daisy::ui.file-input color="primary" />
            </div>
        </section>

        <!-- Range -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Range Slider</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <x-daisy::ui.range />
                <x-daisy::ui.range color="primary" />
                <x-daisy::ui.range color="success" />
            </div>
        </section>

        <!-- Mask -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Mask</h2>
            <div class="flex items-center gap-6">
                <x-daisy::ui.mask shape="squircle" src="https://picsum.photos/seed/1/96" class="w-24 h-24" />
                <x-daisy::ui.mask shape="hexagon" src="https://picsum.photos/seed/2/96" class="w-24 h-24" />
                <x-daisy::ui.mask shape="star-2" src="https://picsum.photos/seed/3/96" class="w-24 h-24" />
            </div>
        </section>

        <!-- Hero -->
        <section class="space-y-6 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Hero</h2>
            <x-daisy::ui.hero :overlay="true" imageUrl="https://picsum.photos/seed/hero/1200/400" class="rounded-box">
                <h1 class="mb-5 text-5xl font-bold">Hello</h1>
                <p class="mb-5">Hero avec overlay</p>
                <x-daisy::ui.button color="primary">Get Started</x-daisy::ui.button>
            </x-daisy::ui.hero>
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
            <x-daisy::ui.carousel class="w-full space-x-4 p-4 bg-base-100">
                <div class="carousel-item">
                    <img src="https://picsum.photos/seed/a/320/160" class="rounded-box" />
                </div>
                <div class="carousel-item">
                    <img src="https://picsum.photos/seed/b/320/160" class="rounded-box" />
                </div>
                <div class="carousel-item">
                    <img src="https://picsum.photos/seed/c/320/160" class="rounded-box" />
                </div>
            </x-daisy::ui.carousel>
        </section>

        <!-- Chat -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Chat</h2>
            <div class="space-y-2">
                <x-daisy::ui.chat-bubble align="start" name="Alice" time="12:45">
                    Salut !
                    <x-slot:avatar><img src="https://i.pravatar.cc/100?img=6" /></x-slot:avatar>
                </x-daisy::ui.chat-bubble>
                <x-daisy::ui.chat-bubble align="end" name="Bob" time="12:46">
                    Hello !
                    <x-slot:avatar><img src="https://i.pravatar.cc/100?img=7" /></x-slot:avatar>
                </x-daisy::ui.chat-bubble>
            </div>
        </section>

        <!-- Countdown -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Countdown</h2>
            <x-daisy::ui.countdown :values="['days' => 15, 'hours' => 10, 'min' => 24, 'sec' => 39]" />
        </section>

        <!-- Diff -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Diff</h2>
            <x-daisy::ui.diff>
                <x-slot:before>
                    <div class="bg-base-200 p-4">Avant</div>
                </x-slot:before>
                <x-slot:after>
                    <div class="bg-base-200 p-4">Après</div>
                </x-slot:after>
            </x-daisy::ui.diff>
        </section>

        

        <!-- Dock (à la demande) -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Dock (à la demande)</h2>
            <div class="flex gap-2">
                <button id="showDockBtn" class="btn btn-primary btn-sm">Afficher le dock</button>
                <button id="hideDockBtn" class="btn btn-ghost btn-sm">Masquer le dock</button>
            </div>
            <x-daisy::ui.dock id="onDemandDock" mobile position="bottom" class="hidden z-50">
                <a class="dock-item btn btn-sm btn-ghost">
                    <x-heroicon-o-home class="w-5 h-5" />
                    <span class="dock-label">Accueil</span>
                </a>
                <a class="dock-item btn btn-sm btn-ghost">
                    <x-heroicon-o-bell class="w-5 h-5" />
                    <span class="dock-label">Notifications</span>
                </a>
                <button id="closeDockBtn" class="dock-item btn btn-sm btn-ghost">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                    <span class="dock-label">Fermer</span>
                </button>
            </x-daisy::ui.dock>
        </section>

        <!-- Mockup Browser -->
        <section class="space-y-6 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Mockup Browser</h2>
            <x-daisy::ui.mockup-browser url="https://example.com" class="rounded-box">
                <div class="p-6">Contenu</div>
            </x-daisy::ui.mockup-browser>
        </section>

        <!-- Mockup Code -->
        <section class="space-y-6 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Mockup Code</h2>
            <x-daisy::ui.mockup-code>
<code>npm i</code>
<code>npm run dev</code>
            </x-daisy::ui.mockup-code>
        </section>

        <!-- Mockup Phone -->
        <section class="space-y-6 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Mockup Phone</h2>
            <x-daisy::ui.mockup-phone>
                <div class="p-6">App</div>
            </x-daisy::ui.mockup-phone>
        </section>

        <!-- Mockup Window -->
        <section class="space-y-6 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Mockup Window</h2>
            <x-daisy::ui.mockup-window>
                <div class="p-6">Fenêtre</div>
            </x-daisy::ui.mockup-window>
        </section>

        <!-- Status -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Status</h2>
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2">
                    <x-daisy::ui.status color="success" />
                    <span>En ligne</span>
                </div>
                <div class="flex items-center gap-2">
                    <x-daisy::ui.status color="warning" />
                    <span>Occupé</span>
                </div>
                <div class="flex items-center gap-2">
                    <x-daisy::ui.status color="error" />
                    <span>Hors ligne</span>
                </div>
            </div>
        </section>

        <!-- Calendar -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Calendar</h2>
            <x-daisy::ui.calendar :days="[
                ['label' => 30, 'muted' => true], ['label' => 31, 'muted' => true],
                ['label' => 1], ['label' => 2], ['label' => 3, 'active' => true], ['label' => 4], ['label' => 5],
                ['label' => 6], ['label' => 7], ['label' => 8], ['label' => 9], ['label' => 10], ['label' => 11], ['label' => 12],
            ]" />
        </section>

        <!-- Filter -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Filter</h2>
            <x-daisy::ui.filter :items="['Tous', 'Nouveaux', 'Populaires']" />
        </section>

        <!-- Validator -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Validator</h2>
            <x-daisy::ui.validator state="error" message="Ce champ est requis">
                <x-daisy::ui.input placeholder="Nom" />
            </x-daisy::ui.validator>
            <x-daisy::ui.validator state="success" message="Valide">
                <x-daisy::ui.input placeholder="Email" />
            </x-daisy::ui.validator>
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

