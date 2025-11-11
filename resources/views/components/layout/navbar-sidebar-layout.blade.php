@props([
    'title' => null,
    'theme' => null,
    // Navbar options
    'navbarBg' => 'base-100',
    'navbarText' => null,
    'navbarShadow' => 'sm',
    // Sidebar options (héritées de sidebar-layout)
    'variant' => 'wide', // slim|wide
    'sideClass' => null,
    'stickyAt' => 'lg',
    'brand' => null,
    'brandHref' => null,
    'showBrand' => true,
    'sections' => [],
    'drawerId' => 'layout-nav-sidebar',
    'responsiveOpen' => 'lg',
    'end' => false,
    // Icon options
    'menuIcon' => 'list',
    'iconPrefix' => 'bi',
    // Content container
    'container' => 'p-6',
])

<x-daisy::layout.app :title="$title" :theme="$theme" :container="false">
    <div class="min-h-screen">
        {{-- Navbar en haut --}}
        <x-daisy::ui.navigation.navbar :bg="$navbarBg" :text="$navbarText" :shadow="$navbarShadow" :fixed="false">
            <x-slot:start>
                <label for="{{ $drawerId }}" aria-label="open sidebar" class="btn btn-square btn-ghost lg:hidden">
                    <x-daisy::ui.advanced.icon :name="$menuIcon" size="lg" />
                </label>
                {{ $brand ?? '' }}
            </x-slot:start>
            <x-slot:center>
                {{ $nav ?? '' }}
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

        {{-- Utilise sidebar-layout avec hasNavbar=true --}}
        <x-daisy::layout.sidebar-layout 
            :title="$title" 
            :theme="$theme"
            :variant="$variant"
            :sideClass="$sideClass"
            :stickyAt="$stickyAt"
            :brand="$brand"
            :brandHref="$brandHref"
            :showBrand="$showBrand"
            :sections="$sections"
            :drawerId="$drawerId"
            :responsiveOpen="$responsiveOpen"
            :end="$end"
            :menuIcon="$menuIcon"
            :iconPrefix="$iconPrefix"
            :container="$container"
            :hasNavbar="true"
        >
            {{ $slot }}
        </x-daisy::layout.sidebar-layout>
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


