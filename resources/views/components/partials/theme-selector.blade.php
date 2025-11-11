@props([
    'position' => 'fixed', // fixed | relative
    'placement' => 'top-right', // top-right | top-left | bottom-right | bottom-left
    'themes' => ['light', 'dark', 'cupcake', 'bumblebee', 'emerald', 'corporate', 'synthwave', 'retro', 'cyberpunk', 'valentine', 'halloween', 'garden', 'forest', 'aqua', 'lofi', 'pastel', 'fantasy', 'wireframe', 'black', 'luxury', 'dracula', 'cmyk', 'autumn', 'business', 'acid', 'lemonade', 'night', 'coffee', 'winter'],
])

@php
    $positionClasses = match($position) {
        'fixed' => 'fixed z-50',
        'relative' => 'relative',
        default => 'fixed z-50',
    };
    
    $placementClasses = match($placement) {
        'top-right' => 'top-4 right-4',
        'top-left' => 'top-4 left-4',
        'bottom-right' => 'bottom-4 right-4',
        'bottom-left' => 'bottom-4 left-4',
        default => 'top-4 right-4',
    };
@endphp

<div class="{{ $positionClasses }} {{ $placementClasses }}">
    <x-daisy::ui.advanced.theme-controller 
        variant="dropdown" 
        :themes="$themes"
        label="Theme"
        size="sm"
    />
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

