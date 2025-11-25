@props([
    // Style général
    'bg' => 'base-200',
    'text' => 'base-content',
    'padding' => 'p-10',
    // Layout
    'center' => false,
    'horizontal' => false,
    'horizontalAt' => null, // sm|md|lg|xl → sm:footer-horizontal
    // Grid layout
    'gap' => 6, // gap pour grid-layout
    // Colonnes de navigation
    'columns' => [], // [{ title: string, links: [{ label: string, href: string, external?: bool }] }]
    // Branding
    'logo' => null, // string (URL) ou slot 'logo'
    'brandText' => null, // string
    'brandDescription' => null, // string
    // Copyright
    'copyright' => null, // string ou slot 'copyright'
    'copyrightYear' => null, // int (auto: année actuelle)
    'copyrightText' => null, // string
    // Réseaux sociaux
    'socialLinks' => [], // [{ icon: string, href: string, label?: string, external?: bool }]
    // Newsletter
    'newsletter' => false,
    'newsletterTitle' => null,
    'newsletterDescription' => null,
    'newsletterAction' => null, // string (URL)
    'newsletterMethod' => 'POST',
    // Divider
    'showDivider' => true,
    'dividerColor' => null, // null = auto selon bg
])

@php
    // Classes du footer
    $footerClasses = 'footer';
    if ($center) {
        $footerClasses .= ' footer-center';
    }
    if ($horizontal) {
        $footerClasses .= ' footer-horizontal';
    }
    if ($horizontalAt) {
        $footerClasses .= ' '.$horizontalAt.':footer-horizontal';
    }
    if ($bg) {
        $footerClasses .= ' bg-'.$bg;
    }
    if ($text) {
        $footerClasses .= ' text-'.$text;
    }
    if ($padding) {
        $footerClasses .= ' '.$padding;
    }

    // Année du copyright
    $year = $copyrightYear ?? date('Y');

    // Divider color auto
    if ($showDivider && $dividerColor === null) {
        $dividerColor = match($bg) {
            'base-100' => 'base-300',
            'base-200' => 'base-300',
            'base-300' => 'base-content',
            default => 'base-300',
        };
    }
@endphp

<footer {{ $attributes->merge(['class' => $footerClasses]) }}>
    {{-- Contenu principal avec grid-layout --}}
    <x-daisy::ui.layout.grid-layout :gap="$gap" align="start">
        {{-- Logo et branding --}}
        @if($logo || $brandText || $brandDescription)
            <nav class="col-12 col-md-6 col-lg-4">
                @if($logo)
                    @if($logo instanceof \Illuminate\View\ComponentSlot)
                        {{ $logo }}
                    @else
                        <img src="{{ $logo }}" alt="{{ $brandText ?? 'Logo' }}" class="h-8 w-auto" />
                    @endif
                @endif
                @if($brandText)
                    <h6 class="footer-title">{{ $brandText }}</h6>
                @endif
                @if($brandDescription)
                    <p class="text-sm opacity-70">{{ $brandDescription }}</p>
                @endif
            </nav>
        @endif

        {{-- Colonnes de navigation --}}
        @foreach($columns as $column)
            <nav class="col-12 col-md-6 col-lg-4">
                @if(isset($column['title']))
                    <h6 class="footer-title">{{ $column['title'] }}</h6>
                @endif
                @if(isset($column['links']) && is_array($column['links']))
                    @foreach($column['links'] as $link)
                        <a 
                            href="{{ $link['href'] ?? '#' }}" 
                            class="link link-hover"
                            @if(isset($link['external']) && $link['external'])
                                target="_blank" rel="noopener noreferrer"
                            @endif
                        >
                            {{ $link['label'] ?? '' }}
                        </a>
                    @endforeach
                @endif
            </nav>
        @endforeach

        {{-- Newsletter --}}
        @if($newsletter)
            <nav class="col-12 col-md-6 col-lg-4">
                @if($newsletterTitle)
                    <h6 class="footer-title">{{ $newsletterTitle }}</h6>
                @endif
                @if($newsletterDescription)
                    <p class="text-sm opacity-70 mb-2">{{ $newsletterDescription }}</p>
                @endif
                @if($newsletterAction)
                    <form action="{{ $newsletterAction }}" method="{{ $newsletterMethod }}" class="flex gap-2">
                        <input 
                            type="email" 
                            name="email" 
                            placeholder="{{ __('common.email') }}" 
                            class="input input-sm input-bordered flex-1" 
                            required 
                        />
                        <button type="submit" class="btn btn-sm btn-primary">
                            {{ __('common.subscribe') }}
                        </button>
                    </form>
                @elseif(isset($newsletter) && $newsletter instanceof \Illuminate\View\ComponentSlot)
                    {{ $newsletter }}
                @endif
            </nav>
        @endif

        {{-- Slot personnalisé pour colonnes supplémentaires --}}
        @if(isset($columns) && $columns instanceof \Illuminate\View\ComponentSlot)
            <div class="col-12">
                {{ $columns }}
            </div>
        @endif
    </x-daisy::ui.layout.grid-layout>

    {{-- Divider --}}
    @if($showDivider)
        <x-daisy::ui.layout.divider 
            :color="$dividerColor" 
            class="my-4"
        />
    @endif

    {{-- Footer bottom: copyright et réseaux sociaux --}}
    <div class="footer-bottom flex flex-col sm:flex-row gap-4 items-center justify-between">
        {{-- Copyright --}}
        <aside class="text-sm opacity-70">
            @if(isset($copyright) && $copyright instanceof \Illuminate\View\ComponentSlot)
                {{ $copyright }}
            @elseif($copyright)
                {!! $copyright !!}
            @elseif($copyrightText)
                <p>© {{ $year }} {{ $copyrightText }}</p>
            @else
                <p>© {{ $year }}</p>
            @endif
        </aside>

        {{-- Réseaux sociaux --}}
        @if(!empty($socialLinks))
            <nav class="flex gap-2">
                @foreach($socialLinks as $social)
                    <a 
                        href="{{ $social['href'] ?? '#' }}" 
                        class="btn btn-circle btn-sm btn-ghost"
                        @if(isset($social['external']) && $social['external'])
                            target="_blank" rel="noopener noreferrer"
                        @endif
                        aria-label="{{ $social['label'] ?? 'Social link' }}"
                    >
                        @if(isset($social['icon']))
                            <x-daisy::ui.advanced.icon :name="$social['icon']" />
                        @else
                            {{ $social['label'] ?? '' }}
                        @endif
                    </a>
                @endforeach
            </nav>
        @endif

        {{-- Slot personnalisé pour footer bottom --}}
        @if(isset($footerBottom) && $footerBottom instanceof \Illuminate\View\ComponentSlot)
            {{ $footerBottom }}
        @endif
    </div>
</footer>

