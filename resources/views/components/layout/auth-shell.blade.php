@props([
    'backgroundImage' => null,
    'backgroundClass' => null,
    'overlay' => null,
    'overlayClass' => 'bg-black/50',
    'padding' => 'px-4 py-10 sm:px-6',
    'cardClass' => 'w-full max-w-md space-y-6 rounded-box border border-base-300/70 bg-base-100/90 p-6 shadow-xl backdrop-blur',
])

@php
    $normalizeImageUrl = function ($url) {
        if (! is_string($url) && ! $url instanceof \Stringable) {
            return null;
        }

        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if (str_starts_with($url, '/')) {
            return $url;
        }

        return preg_match('/^https?:\/\//i', $url) === 1 ? $url : null;
    };

    $backgroundImage = $normalizeImageUrl($backgroundImage);
    $backgroundClass = is_string($backgroundClass) ? trim($backgroundClass) : null;
    $hasBackground = $backgroundImage !== null || ($backgroundClass !== null && $backgroundClass !== '');
    $showOverlay = $overlay ?? $hasBackground;
@endphp

<section {{ $attributes->class(['relative min-h-screen min-h-dvh w-full overflow-hidden bg-base-200', $padding]) }}>
    @if ($hasBackground)
        @if ($backgroundImage)
            <img src="{{ $backgroundImage }}" alt="" aria-hidden="true" class="pointer-events-none absolute inset-0 h-full w-full object-cover {{ $backgroundClass }}">
        @else
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-cover bg-center bg-no-repeat {{ $backgroundClass }}"></div>
        @endif
    @endif

    @if ($showOverlay)
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 {{ $overlayClass }}"></div>
    @endif

    <div class="relative z-10 flex min-h-[calc(100dvh-5rem)] min-h-[calc(100vh-5rem)] items-center justify-center">
        <div class="{{ $cardClass }}">
            {{ $slot }}
        </div>
    </div>
</section>
