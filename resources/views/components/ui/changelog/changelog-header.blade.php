@props([
    'title' => __('daisy::changelog.changelog'),
    'currentVersion' => null,
    'rssUrl' => null,
    'atomUrl' => null,
    'showVersionBadge' => true,
])

@php
    $normalizeUrl = function($url) {
        if (!is_string($url) && !$url instanceof \Stringable) {
            return null;
        }

        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if (str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return $url;
        }

        return preg_match('/^https?:\/\//i', $url) === 1 ? $url : null;
    };

    $rssUrl = $normalizeUrl($rssUrl);
    $atomUrl = $normalizeUrl($atomUrl);
@endphp

<header class="changelog-header mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <h1 class="text-3xl font-bold">{{ $title }}</h1>
            @if($showVersionBadge && $currentVersion)
                <x-daisy::ui.data-display.badge color="primary" size="lg">
                    {{ __('daisy::changelog.current_version') }}: {{ $currentVersion }}
                </x-daisy::ui.data-display.badge>
            @endif
        </div>

        @if($rssUrl || $atomUrl)
            <div class="flex items-center gap-3">
                @if($rssUrl)
                    <a href="{{ $rssUrl }}" target="_blank" rel="noopener noreferrer" class="link link-primary text-sm">
                        <span class="flex items-center gap-1">
                            <x-daisy::ui.advanced.icon name="rss" size="sm" />
                            {{ __('daisy::changelog.rss_feed') }}
                        </span>
                    </a>
                @endif

                @if($atomUrl)
                    <a href="{{ $atomUrl }}" target="_blank" rel="noopener noreferrer" class="link link-primary text-sm">
                        <span class="flex items-center gap-1">
                            <x-daisy::ui.advanced.icon name="rss" size="sm" />
                            {{ __('daisy::changelog.atom_feed') }}
                        </span>
                    </a>
                @endif
            </div>
        @endif
    </div>
</header>
