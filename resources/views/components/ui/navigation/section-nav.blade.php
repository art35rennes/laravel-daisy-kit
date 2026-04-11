@props([
    'title' => 'Sections',
    'targetSelector' => 'div.space-y-10 > section',
    'headingSelector' => 'h2',
    'position' => 'bottom-right', // bottom-right | bottom-left | top-right | top-left
    'breakpointClass' => 'hidden md:block',
    'buttonColor' => 'primary',
    'buttonClass' => '',
    'panelWidth' => 'w-72 sm:w-80',
    'searchPlaceholder' => 'Rechercher...',
    'emptyLabel' => 'Aucune section',
])

@php
    $instanceId = 'section-nav-'.\Illuminate\Support\Str::uuid();

    $positionClasses = match($position) {
        'bottom-left' => 'bottom-6 left-6',
        'top-right' => 'top-6 right-6',
        'top-left' => 'top-6 left-6',
        default => 'bottom-6 right-6',
    };

    $panelAnchorClasses = match($position) {
        'bottom-left' => 'absolute bottom-16 left-0',
        'top-right' => 'absolute top-16 right-0',
        'top-left' => 'absolute top-16 left-0',
        default => 'absolute bottom-16 right-0',
    };
@endphp

<div
    id="{{ $instanceId }}"
    class="fixed {{ $positionClasses }} z-50 {{ $breakpointClass }}"
    data-section-nav
    data-target-selector="{{ $targetSelector }}"
    data-heading-selector="{{ $headingSelector }}"
>
    <div class="{{ $panelAnchorClasses }} hidden" data-section-nav-panel>
        <div class="bg-base-200 rounded-box shadow-lg p-3 {{ $panelWidth }} max-w-[calc(100vw-2rem)]" data-section-nav-box>
            <div class="font-semibold mb-2">{{ $title }}</div>
            <div class="mb-2">
                <label class="input input-bordered flex items-center gap-2 w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4 opacity-70">
                        <path fill-rule="evenodd" d="M10.5 3.75a6.75 6.75 0 1 0 3.897 12.303l3.775 3.775a.75.75 0 1 0 1.06-1.06l-3.775-3.776A6.75 6.75 0 0 0 10.5 3.75ZM5.25 10.5a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Z" clip-rule="evenodd" />
                    </svg>
                    <input type="text" placeholder="{{ $searchPlaceholder }}" class="grow" autocomplete="off" data-section-nav-search />
                </label>
            </div>
            <ul class="menu" data-section-nav-list></ul>
            <div class="hidden px-2 py-3 text-sm text-base-content/70" data-section-nav-empty>{{ $emptyLabel }}</div>
        </div>
    </div>

    <button
        type="button"
        class="btn btn-{{ $buttonColor }} btn-circle shadow-lg {{ $buttonClass }}"
        aria-label="{{ $title }}"
        data-section-nav-button
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" data-section-nav-icon-open>
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 hidden" data-section-nav-icon-close>
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>

@once
    @push('scripts')
    <script>
        (function() {
            const normalizeText = (value) => (value || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/\p{Diacritic}/gu, '');

            const slugify = (value) => (value || '')
                .toLowerCase()
                .trim()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-');

            function initSectionNav(root) {
                if (!root || root.dataset.sectionNavReady === '1') return;
                root.dataset.sectionNavReady = '1';

                const panel = root.querySelector('[data-section-nav-panel]');
                const box = root.querySelector('[data-section-nav-box]');
                const list = root.querySelector('[data-section-nav-list]');
                const empty = root.querySelector('[data-section-nav-empty]');
                const search = root.querySelector('[data-section-nav-search]');
                const button = root.querySelector('[data-section-nav-button]');
                const iconOpen = root.querySelector('[data-section-nav-icon-open]');
                const iconClose = root.querySelector('[data-section-nav-icon-close]');

                if (!panel || !box || !list || !button) return;

                const targetSelector = root.dataset.targetSelector || 'div.space-y-10 > section';
                const headingSelector = root.dataset.headingSelector || 'h2';
                let cachedData = [];

                function collectSections() {
                    const sections = Array.from(document.querySelectorAll(targetSelector));
                    const seen = new Set();

                    return sections.reduce((acc, section) => {
                        const heading = section.querySelector(headingSelector);
                        if (!heading) return acc;

                        const label = heading.textContent.trim();
                        let id = section.id || slugify(label);
                        let suffix = 2;

                        while (seen.has(id)) {
                            id = `${section.id || slugify(label)}-${suffix++}`;
                        }

                        seen.add(id);
                        if (!section.id) section.id = id;

                        acc.push({
                            id,
                            label,
                            key: normalizeText(label),
                        });

                        return acc;
                    }, []);
                }

                function adjustOverflow() {
                    const viewportH = window.innerHeight;
                    const viewportW = window.innerWidth;
                    box.style.maxHeight = Math.max(240, Math.floor(viewportH * 0.7)) + 'px';
                    box.style.maxWidth = Math.max(240, viewportW - 32) + 'px';

                    const overflowY = box.scrollHeight > box.clientHeight;
                    box.style.overflowY = overflowY ? 'auto' : 'visible';

                    const rect = panel.getBoundingClientRect();
                    const shift = Math.max(0, rect.right - viewportW + 16);
                    panel.style.transform = shift ? `translateX(-${shift}px)` : '';
                }

                function render(filter = '') {
                    if (!cachedData.length) cachedData = collectSections();

                    const key = normalizeText(filter);
                    const items = cachedData.filter((item) => !key || item.key.includes(key));

                    list.innerHTML = '';

                    if (!items.length) {
                        empty?.classList.remove('hidden');
                        return;
                    }

                    empty?.classList.add('hidden');

                    items.forEach((item) => {
                        const li = document.createElement('li');
                        const link = document.createElement('a');
                        link.href = '#' + item.id;
                        link.textContent = item.label;
                        li.appendChild(link);
                        list.appendChild(li);
                    });
                }

                function toggle(forceOpen) {
                    const open = forceOpen ?? panel.classList.contains('hidden');
                    panel.classList.toggle('hidden', !open);
                    iconOpen?.classList.toggle('hidden', open);
                    iconClose?.classList.toggle('hidden', !open);

                    if (!open) return;

                    cachedData = [];
                    if (search) search.value = '';
                    render();
                    adjustOverflow();
                    if (search) setTimeout(() => search.focus(), 0);
                }

                button.addEventListener('click', () => toggle());
                panel.addEventListener('click', (event) => {
                    if (event.target instanceof HTMLAnchorElement) toggle(false);
                });

                document.addEventListener('click', (event) => {
                    if (!root.contains(event.target)) toggle(false);
                });

                window.addEventListener('resize', adjustOverflow);

                search?.addEventListener('input', () => render(search.value));

                document.addEventListener('keydown', (event) => {
                    if (event.key === '/' && !event.ctrlKey && !event.metaKey && !event.altKey) {
                        if (panel.classList.contains('hidden')) toggle(true);
                        if (search) {
                            event.preventDefault();
                            search.focus();
                        }
                    }
                });

                const targetRoot = document.querySelector(targetSelector)?.parentElement;
                if (targetRoot) {
                    const observer = new MutationObserver(() => {
                        cachedData = [];
                        if (!panel.classList.contains('hidden')) render(search?.value || '');
                    });

                    observer.observe(targetRoot, { childList: true, subtree: true });
                }
            }

            function bootSectionNav() {
                document.querySelectorAll('[data-section-nav]').forEach(initSectionNav);
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', bootSectionNav, { once: true });
            } else {
                bootSectionNav();
            }
        })();
    </script>
    @endpush
@endonce
