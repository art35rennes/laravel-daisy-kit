@props([
    'src',
    'title' => null,
    'poster' => null,
    'autoplay' => false,
    'muted' => false,
    'controls' => true,
    'live' => false,
    'status' => 'source',
])

@php
    $safeSrc = is_string($src) || $src instanceof Stringable ? trim((string) $src) : '';
    $safePoster = is_string($poster) || $poster instanceof Stringable ? trim((string) $poster) : null;
@endphp

<section {{ $attributes->merge(['class' => 'rounded-box border border-base-300 bg-base-100 overflow-hidden']) }}>
    @if($title || $live)
        <header class="flex items-center justify-between gap-3 border-b border-base-300 px-4 py-3">
            <div class="min-w-0">
                @if($title)
                    <h2 class="truncate text-sm font-semibold">{{ $title }}</h2>
                @endif
                <p class="text-xs text-base-content/60">{{ $status }}</p>
            </div>
            @if($live)
                <span class="badge badge-error badge-sm">Live</span>
            @endif
        </header>
    @endif

    <video
        class="aspect-video w-full bg-black"
        src="{{ $safeSrc }}"
        @if($safePoster) poster="{{ $safePoster }}" @endif
        @if($controls) controls @endif
        @if($autoplay) autoplay @endif
        @if($muted) muted @endif
        playsinline
        preload="metadata"
    ></video>
</section>
