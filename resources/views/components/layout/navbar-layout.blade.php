@props([
    'title' => null,
    'theme' => null,
    // Couleurs et styles de la navbar
    'navbarBg' => 'base-100',
    'navbarText' => null,
    'navbarShadow' => 'sm',
    'navbarFixed' => true,
    'navbarFixedPosition' => 'top', // top|bottom
    // Classe container du contenu principal
    'container' => 'container mx-auto p-6',
])

<x-daisy::layout.app :title="$title" :theme="$theme" :container="false">
    <x-daisy::ui.navigation.navbar :bg="$navbarBg" :text="$navbarText" :shadow="$navbarShadow" :fixed="$navbarFixed" :fixedPosition="$navbarFixedPosition">
        <x-slot:start>
            {{ $navbarStart ?? ($brand ?? null) }}
        </x-slot:start>
        <x-slot:center>
            {{ $navbarCenter ?? ($nav ?? null) }}
        </x-slot:center>
        <x-slot:end>
            <x-daisy::ui.advanced.theme-controller 
                variant="dropdown" 
                :themes="['light', 'dark', 'cupcake', 'bumblebee', 'emerald', 'corporate', 'synthwave', 'retro', 'cyberpunk', 'valentine', 'halloween', 'garden', 'forest', 'aqua', 'lofi', 'pastel', 'fantasy', 'wireframe', 'black', 'luxury', 'dracula', 'cmyk', 'autumn', 'business', 'acid', 'lemonade', 'night', 'coffee', 'winter']"
                label="Theme"
                size="sm"
            />
            {{ $navbarEnd ?? ($actions ?? null) }}
        </x-slot:end>
    </x-daisy::ui.navigation.navbar>

    <main class="{{ $container }} pt-24">
        {{ $slot }}
    </main>

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


