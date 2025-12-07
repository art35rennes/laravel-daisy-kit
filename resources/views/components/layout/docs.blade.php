@props([
    'title' => null,
    'theme' => null,
    // Navigation (gauche)
    'sidebarItems' => [],         // [{ label, href?, children: [] }]
    'currentRoute' => null,       // string (pour le highlight)
    'drawerId' => 'docs-drawer',  // id du drawer pour mobile
    // Table des matières (droite)
    'sections' => [],             // [{ id, label }]
])

<x-daisy::layout.app :title="$title" :theme="$theme" :container="false">
    <div class="min-h-screen">
        {{-- Barre de navigation supérieure : menu mobile (drawer toggle) + brand + actions + sélecteur de thème --}}
        <x-daisy::ui.navigation.navbar :bg="'base-100'" :shadow="true" :fixed="false" class="border-b">
            <x-slot:start>
                {{-- Bouton pour ouvrir le drawer sur mobile (masqué sur desktop où le drawer est toujours visible) --}}
                <label for="{{ $drawerId }}" aria-label="open sidebar" class="btn btn-square btn-ghost lg:hidden">
                    <x-daisy::ui.advanced.icon name="list" size="lg" />
                </label>
                {{ $brand ?? '' }}
            </x-slot:start>
            <x-slot:center>
                {{ $navbar ?? '' }}
            </x-slot:center>
            <x-slot:end>
                {{-- Sélecteur de thème daisyUI (dropdown avec tous les thèmes disponibles) --}}
                <x-daisy::ui.advanced.theme-controller 
                    variant="dropdown" 
                    :themes="['light', 'dark', 'cupcake', 'bumblebee', 'emerald', 'corporate', 'synthwave', 'retro', 'cyberpunk', 'valentine', 'halloween', 'garden', 'forest', 'aqua', 'lofi', 'pastel', 'fantasy', 'wireframe', 'black', 'luxury', 'dracula', 'cmyk', 'autumn', 'business', 'acid', 'lemonade', 'night', 'coffee', 'winter']"
                    label="Theme"
                    size="sm"
                />
                {{ $actions ?? '' }}
            </x-slot:end>
        </x-daisy::ui.navigation.navbar>

        {{-- Layout principal : Drawer (sidebar gauche responsive) + contenu central + table des matières --}}
        <x-daisy::ui.overlay.drawer :id="$drawerId" :responsiveOpen="'lg'">
            <x-slot:content>
                <div class="container mx-auto px-4 sm:px-6 pt-6 lg:pt-8 pb-12">
                    <div class="grid grid-cols-12 gap-6">
                        {{-- Colonne principale : contenu de la documentation (article prose) --}}
                        <div class="col-span-12 lg:col-span-8 xl:col-span-9">
                            <article class="prose max-w-none">
                                {{ $content ?? $slot }}
                            </article>
                        </div>
                        {{-- Colonne droite : table des matières (sticky pour rester visible au scroll) --}}
                        <aside class="col-span-12 lg:col-span-4 xl:col-span-3 lg:block">
                            <div class="lg:sticky lg:top-20">
                                <x-daisy::ui.navigation.table-of-contents :sections="$sections" />
                            </div>
                        </aside>
                    </div>
                </div>
            </x-slot:content>
            <x-slot:side>
                {{-- Sidebar gauche : navigation principale (menu de documentation) --}}
                <div class="p-4 w-56 max-w-[90vw]">
                    @if(!empty($sidebarItems))
                        {{-- Navigation structurée depuis un array (recommandé) --}}
                        <x-daisy::ui.navigation.sidebar-navigation :items="$sidebarItems" :current="$currentRoute ?? request()->path()" :searchable="true" />
                    @else
                        {{-- Slot personnalisé pour une navigation custom --}}
                        {{ $sidebar ?? '' }}
                    @endif
                </div>
            </x-slot:side>
        </x-daisy::ui.overlay.drawer>

        {{-- Footer simple avec le nom de l'app --}}
        <x-daisy::ui.layout.footer :center="true" :bg="'base-200'">
            <p class="text-sm">{{ config('app.name') }}</p>
        </x-daisy::ui.layout.footer>
    </div>

    {{-- Script d'initialisation du thème : synchronise localStorage, attribut data-theme et contrôles UI --}}
    @push('scripts')
    <script>
        (function() {
            const THEME_KEY = 'daisy-theme';
            const htmlEl = document.documentElement;
            const controllers = () => Array.from(document.querySelectorAll('.theme-controller'));

            // Application du thème : met à jour l'attribut HTML, localStorage et les contrôles radio.
            function applyTheme(theme) {
                if (!theme) return;
                htmlEl.setAttribute('data-theme', theme);
                try { localStorage.setItem(THEME_KEY, theme); } catch (_) {}
                controllers().forEach((el) => {
                    if (el.type === 'radio') {
                        el.checked = (el.value === theme);
                    }
                });
            }

            // Lecture du thème sauvegardé depuis localStorage (avec fallback silencieux si erreur).
            function readSavedTheme() {
                try { return localStorage.getItem(THEME_KEY); } catch (_) { return null; }
            }

            // Initialisation : restaure le thème sauvegardé ou utilise le thème par défaut.
            function init() {
                const saved = readSavedTheme();
                const current = saved || htmlEl.getAttribute('data-theme') || 'light';
                applyTheme(current);
            }

            // Écoute des changements sur les contrôles de thème (dropdown, radio, etc.).
            document.addEventListener('change', (e) => {
                const t = e.target;
                if (t && t.classList && t.classList.contains('theme-controller')) {
                    applyTheme(t.value);
                }
            });

            // Initialisation au chargement (support du DOM déjà chargé ou en cours de chargement).
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>
    @endpush
</x-daisy::layout.app>


