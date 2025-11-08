@props([
    'sections' => [],  // [{ id, label }]
    'scrollSpy' => true,
])

<div class="hidden lg:block">
    <div class="text-sm font-semibold mb-2 opacity-70">Sur cette page</div>
    <ul class="menu menu-xs">
        @foreach($sections as $s)
            @php
                $id = (string)($s['id'] ?? '');
                $label = (string)($s['label'] ?? $id);
            @endphp
            @if($id !== '')
                <li><a href="#{{ $id }}">{{ $label }}</a></li>
            @endif
        @endforeach
    </ul>
</div>

@if($scrollSpy)
    @push('scripts')
        <script>
            (function () {
                const links = Array.from(document.querySelectorAll('.menu a[href^="#"]'));
                if (!('IntersectionObserver' in window) || links.length === 0) return;
                const map = new Map();
                links.forEach(a => {
                    const id = a.getAttribute('href').slice(1);
                    const el = document.getElementById(id);
                    if (el) map.set(el, a);
                });
                const obs = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        const a = map.get(entry.target);
                        if (!a) return;
                        if (entry.isIntersecting) {
                            links.forEach(l => l.classList.remove('menu-active', 'font-semibold'));
                            a.classList.add('menu-active', 'font-semibold');
                        }
                    });
                }, { rootMargin: '0px 0px -70% 0px', threshold: 0.1 });
                map.forEach((_, el) => obs.observe(el));
            })();
        </script>
    @endpush
@endif


