@props([
    'sections' => [],
    'items' => [],
    'emptyLabel' => '—',
    'columns' => 2,
])

@php
    $normalizedSections = $sections;

    if ($normalizedSections === [] && $items !== []) {
        $normalizedSections = [['items' => $items]];
    }

    $columnClass = match ((int) $columns) {
        1 => 'md:grid-cols-1',
        3 => 'md:grid-cols-3',
        default => 'md:grid-cols-2',
    };

    $normalizeUrl = function($url) {
        if (!is_string($url) && !$url instanceof \Stringable) {
            return null;
        }

        $url = trim((string) $url);

        if ($url === '' || $url === '#' || str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return $url === '' ? null : $url;
        }

        return preg_match('/^(https?:|mailto:|tel:)/i', $url) === 1 ? $url : null;
    };
@endphp

<div {{ $attributes->class('space-y-4') }}>
    @foreach($normalizedSections as $section)
        @php
            $sectionItems = $section['items'] ?? [];
            $sectionTitle = $section['title'] ?? null;
            $sectionIcon = $section['icon'] ?? null;
        @endphp

        <section class="rounded-box border border-base-300 bg-base-100">
            @if($sectionTitle || $sectionIcon)
                <div class="flex items-center gap-2 border-b border-base-300 px-4 py-3">
                    @if($sectionIcon)
                        <x-icon :name="$sectionIcon" class="size-4 shrink-0 text-base-content/70" />
                    @endif
                    @if($sectionTitle)
                        <h3 class="text-sm font-semibold">{{ $sectionTitle }}</h3>
                    @endif
                </div>
            @endif

            <dl class="grid {{ $columnClass }} divide-y divide-base-200 md:divide-x md:divide-y-0">
                @forelse($sectionItems as $item)
                    @php
                        $label = $item['label'] ?? '';
                        $value = $item['value'] ?? null;
                        $href = ($item['link'] ?? false) ? $normalizeUrl($item['href'] ?? $value) : null;
                        $copyable = filter_var($item['copyable'] ?? false, FILTER_VALIDATE_BOOLEAN);
                        $wide = filter_var($item['wide'] ?? false, FILTER_VALIDATE_BOOLEAN);
                        $help = $item['help'] ?? $item['tooltip'] ?? null;
                        $icon = $item['icon'] ?? null;
                        $display = filled($value) ? $value : $emptyLabel;
                    @endphp

                    <div class="{{ $wide ? 'md:col-span-full' : '' }} px-4 py-3">
                        <dt class="flex items-center gap-1.5 text-xs font-medium uppercase tracking-wide text-base-content/60">
                            @if($icon)
                                <x-icon :name="$icon" class="size-3.5 shrink-0" />
                            @endif
                            <span>{{ $label }}</span>
                            @if($help)
                                <span class="tooltip tooltip-top" data-tip="{{ $help }}">
                                    <x-icon name="bi-question-circle" class="size-3.5" />
                                </span>
                            @endif
                        </dt>
                        <dd class="mt-1 min-w-0 text-sm text-base-content">
                            @if($href)
                                <a href="{{ $href }}" class="link link-hover break-all">{{ $display }}</a>
                            @elseif($copyable && filled($value))
                                <x-daisy::ui.utilities.copyable :value="$value" icon-position="inline" class="font-mono break-all" :underline="false" :success-message="$item['successMessage'] ?? null">
                                    {{ $display }}
                                </x-daisy::ui.utilities.copyable>
                            @else
                                <span class="break-words">{{ $display }}</span>
                            @endif
                        </dd>
                    </div>
                @empty
                    <div class="px-4 py-3 text-sm text-base-content/60">{{ $emptyLabel }}</div>
                @endforelse
            </dl>
        </section>
    @endforeach
</div>
