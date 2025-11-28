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
        {{-- Barre du haut (slot navbar) --}}
        <x-daisy::ui.navigation.navbar :bg="'base-100'" :shadow="true" :fixed="false" class="border-b">
            <x-slot:start>
                <label for="{{ $drawerId }}" aria-label="open sidebar" class="btn btn-square btn-ghost lg:hidden">
                    <x-daisy::ui.advanced.icon name="list" size="lg" />
                </label>
                {{ $brand ?? '' }}
            </x-slot:start>
            <x-slot:center>
                {{ $navbar ?? '' }}
            </x-slot:center>
            <x-slot:end>
                <x-daisy::ui.advanced.theme-controller 
                    variant="dropdown" 
                    :themes="['light', 'dark', 'cupcake', 'bumblebee', 'emerald', 'corporate', 'synthwave', 'retro', 'cyberpunk', 'valentine', 'halloween', 'garden', 'forest', 'aqua', 'lofi', 'pastel', 'fantasy', 'wireframe', 'black', 'luxury', 'dracula', 'cmyk', 'autumn', 'business', 'acid', 'lemonade', 'night', 'coffee', 'winter']"
                    label="Theme"
                    size="sm"
                />
                {{ $actions ?? '' }}
            </x-slot:end>
        </x-daisy::ui.navigation.navbar>

        {{-- Layout 3 colonnes avec Drawer (sidebar gauche responsive) --}}
        <x-daisy::ui.overlay.drawer :id="$drawerId" :responsiveOpen="'lg'">
            <x-slot:content>
                <div class="container mx-auto px-4 sm:px-6 pt-6 lg:pt-8">
                    <div class="grid grid-cols-12 gap-6">
                        {{-- Colonne principale --}}
                        <div class="col-span-12 lg:col-span-8 xl:col-span-9">
                            <article class="prose max-w-none">
                                {{ $content ?? $slot }}
                            </article>
                        </div>
                        {{-- Table des matières à droite --}}
                        <aside class="col-span-12 lg:col-span-4 xl:col-span-3 lg:block">
                            <div class="lg:sticky lg:top-20">
                                <x-daisy::ui.navigation.table-of-contents :sections="$sections" />
                            </div>
                        </aside>
                    </div>
                </div>
            </x-slot:content>
            <x-slot:side>
                <div class="p-4 w-56 max-w-[90vw]">
                    @if(!empty($sidebarItems))
                        <x-daisy::ui.navigation.sidebar-navigation :items="$sidebarItems" :current="$currentRoute ?? request()->path()" :searchable="true" />
                    @else
                        {{ $sidebar ?? '' }}
                    @endif
                </div>
            </x-slot:side>
        </x-daisy::ui.overlay.drawer>
    </div>

    @push('scripts')
    <script>
        (function() {
            const THEME_KEY = 'daisy-theme';
            const htmlEl = document.documentElement;
            const controllers = () => Array.from(document.querySelectorAll('.theme-controller'));

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

            function readSavedTheme() {
                try { return localStorage.getItem(THEME_KEY); } catch (_) { return null; }
            }

            function init() {
                const saved = readSavedTheme();
                const current = saved || htmlEl.getAttribute('data-theme') || 'light';
                applyTheme(current);
            }

            document.addEventListener('change', (e) => {
                const t = e.target;
                if (t && t.classList && t.classList.contains('theme-controller')) {
                    applyTheme(t.value);
                }
            });

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>
    @endpush
</x-daisy::layout.app>


