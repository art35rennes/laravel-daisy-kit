@props([
    'title' => null,
    'theme' => null,
    // Navbar options
    'navbarBg' => 'base-100',
    'navbarText' => null,
    'navbarShadow' => 'sm',
    'navbarFixed' => true,
    'navbarFixedPosition' => 'top',
    // Content container
    'container' => 'container mx-auto p-6',
    // Footer options
    'footerBg' => 'base-200',
    'footerText' => 'base-content',
    'footerPadding' => 'p-10',
    'footerCenter' => false,
    'footerHorizontal' => false,
    'footerHorizontalAt' => null,
    'footerColumns' => [],
    'footerLogo' => null,
    'footerBrandText' => null,
    'footerBrandDescription' => null,
    'footerCopyright' => null,
    'footerCopyrightYear' => null,
    'footerCopyrightText' => null,
    'footerSocialLinks' => [],
    'footerNewsletter' => false,
    'footerNewsletterTitle' => null,
    'footerNewsletterDescription' => null,
    'footerNewsletterAction' => null,
    'footerNewsletterMethod' => 'POST',
    'footerShowDivider' => true,
    'footerDividerColor' => null,
])

<x-daisy::layout.app :title="$title" :theme="$theme" :container="false">
    {{-- Navbar --}}
    <x-daisy::ui.navigation.navbar 
        :bg="$navbarBg" 
        :text="$navbarText" 
        :shadow="$navbarShadow" 
        :fixed="$navbarFixed" 
        :fixedPosition="$navbarFixedPosition"
    >
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

    {{-- Main content --}}
    <main class="{{ $container }} {{ $navbarFixed ? 'pt-24' : '' }} min-h-screen">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <x-daisy::ui.layout.footer-layout
        :bg="$footerBg"
        :text="$footerText"
        :padding="$footerPadding"
        :center="$footerCenter"
        :horizontal="$footerHorizontal"
        :horizontalAt="$footerHorizontalAt"
        :columns="$footerColumns"
        :logo="$footerLogo"
        :brandText="$footerBrandText"
        :brandDescription="$footerBrandDescription"
        :copyright="$footerCopyright"
        :copyrightYear="$footerCopyrightYear"
        :copyrightText="$footerCopyrightText"
        :socialLinks="$footerSocialLinks"
        :newsletter="$footerNewsletter"
        :newsletterTitle="$footerNewsletterTitle"
        :newsletterDescription="$footerNewsletterDescription"
        :newsletterAction="$footerNewsletterAction"
        :newsletterMethod="$footerNewsletterMethod"
        :showDivider="$footerShowDivider"
        :dividerColor="$footerDividerColor"
    >
        @if(isset($columns) && $columns instanceof \Illuminate\View\ComponentSlot)
            <x-slot:columns>{{ $columns }}</x-slot:columns>
        @endif
        @if(isset($copyright) && $copyright instanceof \Illuminate\View\ComponentSlot)
            <x-slot:copyright>{{ $copyright }}</x-slot:copyright>
        @endif
        @if(isset($footerBottom) && $footerBottom instanceof \Illuminate\View\ComponentSlot)
            <x-slot:footerBottom>{{ $footerBottom }}</x-slot:footerBottom>
        @endif
    </x-daisy::ui.layout.footer-layout>

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

