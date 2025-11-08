@props([
    // ID ou sélecteur du conteneur à espionner (par défaut: window/document)
    'container' => null,
    // Items explicites: [['id' => 'section-1', 'label' => 'Section 1'], ...]
    'items' => null,
    // Auto-génération depuis les headings du conteneur
    'autogen' => false,
    // Options comportement
    'smoothScroll' => true,
    'offset' => 0,
    'rootMargin' => '0px 0px -25%',
    'threshold' => [0.1, 0.5, 1],
    // Personnalisation
    'sticky' => true,
    'stickyTop' => 'top-6',
    'navClass' => 'menu menu-sm bg-base-100 rounded-box p-2',
    'activeClass' => 'active',
    'track' => 'section', // sélecteur des sections à observer
    // Surcharge du nom de module JS (optionnel)
    'module' => null,
])

@php
    $id = $attributes->get('id') ?? 'scrollspy-'.uniqid();
    $stickyClasses = $sticky ? ('sticky '.$stickyTop) : '';
    $items = is_array($items) ? $items : null;
    $thresholdJson = is_string($threshold) ? $threshold : json_encode($threshold);
@endphp

<nav id="{{ $id }}"
     data-module="{{ $module ?? 'scrollspy' }}"
     data-scrollspy="1"
     @if($container) data-container="{{ $container }}" @endif
     data-track="{{ $track }}"
     data-root-margin="{{ $rootMargin }}"
     data-threshold='{{ $thresholdJson }}'
     data-smooth="{{ $smoothScroll ? 'true' : 'false' }}"
     data-offset="{{ (int)$offset }}"
     data-active-class="{{ $activeClass }}"
     data-autogen="{{ $autogen ? 'true' : 'false' }}"
     aria-label="Scrollspy navigation"
     class="{{ $stickyClasses }}">
    <ul class="{{ $navClass }}" role="navigation">
        @if($items)
            @foreach($items as $it)
                @php $sid = is_array($it) ? ($it['id'] ?? null) : null; $label = is_array($it) ? ($it['label'] ?? $sid) : $it; @endphp
                @if($sid)
                <li><a href="#{{ $sid }}" class="truncate">{{ $label }}</a></li>
                @endif
            @endforeach
        @else
            {{ $slot ?? '' }}
        @endif
    </ul>
</nav>

@include('daisy::components.partials.assets')


