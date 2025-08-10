<x-daisy::layout.app title="DaisyUI Kit - Demo">
    <h1 class="text-2xl font-semibold mb-6">DaisyUI Kit - Demo</h1>

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

        <!-- Checkbox / Radio / Toggle -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Checkbox, Radio & Toggle</h2>
            <div class="space-y-4">
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
                <div class="flex flex-wrap items-center gap-4">
                    <x-daisy::ui.radio name="demo-radio" value="a" :checked="true" />
                    <x-daisy::ui.radio name="demo-radio" value="b" color="primary" />
                    <x-daisy::ui.radio name="demo-radio" value="c" :disabled="true" />
                    <x-daisy::ui.radio name="demo-radio2" value="d" size="sm" />
                    <x-daisy::ui.radio name="demo-radio2" value="e" size="lg" />
                </div>
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

        <!-- Divider / Label / Kbd -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Divider, Label & Kbd</h2>
            <div class="space-y-3">
                <x-daisy::ui.divider text="Ou" />
                <div class="flex items-center gap-4">
                    <x-daisy::ui.label for="demo-lbl" value="Label" alt="optionnel" />
                    <div class="flex items-center gap-2">
                        <x-daisy::ui.kbd>⌘</x-daisy::ui.kbd>
                        <x-daisy::ui.kbd>K</x-daisy::ui.kbd>
                    </div>
                </div>
                <div class="flex gap-6">
                    <div class="h-24 w-full grid place-items-center bg-base-100 rounded-box">
                        <x-daisy::ui.divider :vertical="true">Vertical</x-daisy::ui.divider>
                    </div>
                </div>
            </div>
        </section>

        <!-- Breadcrumbs / Dropdown -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Breadcrumbs & Dropdown</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <x-daisy::ui.breadcrumbs :items="[
                    ['label' => 'Home', 'href' => '/'],
                    ['label' => 'Library', 'href' => '#'],
                    ['label' => 'Data']
                ]" />

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
            </div>
        </section>

        <!-- Alert / Toast / Modal -->
        <section class="space-y-4 bg-base-200 p-6 rounded-box">
            <h2 class="text-lg font-medium">Alert, Toast & Modal</h2>
            <div class="grid md:grid-cols-2 gap-6">
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
                    <x-daisy::ui.toast>
                        <x-daisy::ui.alert color="success">Sauvegardé avec succès</x-daisy::ui.alert>
                    </x-daisy::ui.toast>
                </div>
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
    </div>
</x-daisy::layout.app>

