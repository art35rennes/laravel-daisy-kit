@props([
    'title' => null,
    'theme' => null,
    // Sidebar options
    'variant' => 'wide', // slim|wide
    'sideClass' => null,
    'stickyAt' => 'lg',
    'brand' => null,
    'brandHref' => null,
    'showBrand' => true,
    'sections' => [],
    // Responsive drawer behavior
    'drawerId' => 'layout-sidebar',
    'responsiveOpen' => 'lg',
    'end' => false, // sidebar on right
    // Icon options
    'menuIcon' => 'list',
    'iconPrefix' => 'bi',
    // Content container
    'container' => 'p-6',
    // Layout options
    'hasNavbar' => false,
])

<x-daisy::layout.app :title="$title" :theme="$theme" :container="false">
    <div {{ $attributes->merge(['class' => 'min-h-screen']) }}>
        <x-daisy::ui.overlay.drawer :id="$drawerId" :end="$end" :responsiveOpen="$responsiveOpen" :sideIsMenu="false" sideClass="w-auto" class="">
            <x-slot:content>
                @if(!$hasNavbar)
                    <div class="bg-base-100 px-4 h-14 flex items-center justify-between lg:justify-end">
                        <div class="flex items-center gap-2 lg:hidden">
                            <label for="{{ $drawerId }}" aria-label="open sidebar" class="btn btn-square btn-ghost">
                                <x-daisy::ui.advanced.icon :name="$menuIcon" size="lg" />
                            </label>
                            @if($title)
                                <div class="font-semibold">{{ __($title) }}</div>
                            @endif
                        </div>
                        <div class="hidden lg:flex items-center gap-2">
                            <x-daisy::ui.advanced.theme-controller 
                                variant="dropdown" 
                                :themes="['light', 'dark', 'cupcake', 'bumblebee', 'emerald', 'corporate', 'synthwave', 'retro', 'cyberpunk', 'valentine', 'halloween', 'garden', 'forest', 'aqua', 'lofi', 'pastel', 'fantasy', 'wireframe', 'black', 'luxury', 'dracula', 'cmyk', 'autumn', 'business', 'acid', 'lemonade', 'night', 'coffee', 'winter']"
                                label="Theme"
                                size="sm"
                            />
                            {{ $topbarRight ?? '' }}
                        </div>
                    </div>
                @endif
                <div class="{{ $container }} {{ $hasNavbar ? 'pt-16' : '' }}">
                    {{ $slot }}
                </div>
            </x-slot:content>
            <x-slot:side>
                <x-daisy::ui.navigation.sidebar 
                    :variant="$variant" 
                    :sideClass="$sideClass" 
                    :stickyAt="$stickyAt" 
                    :brand="$brand" 
                    :brandHref="$brandHref" 
                    :showBrand="$showBrand" 
                    :sections="$sections" 
                    :iconPrefix="$iconPrefix" 
                    class="h-full {{ $hasNavbar ? 'lg:h-[calc(100vh-4rem)]' : '' }}"
                />
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


