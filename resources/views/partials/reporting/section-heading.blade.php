@props(['section', 'toneClasses'])

<div class="flex flex-wrap items-start justify-between gap-3">
    <div class="flex items-start gap-3">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-box {{ $toneClasses[$section['tone']]['soft'] }} {{ $toneClasses[$section['tone']]['text'] }} text-sm font-bold">
            {{ $section['marker'] }}
        </span>
        <div>
            <h2 class="text-base font-bold uppercase tracking-wide {{ $toneClasses[$section['tone']]['text'] }}">{{ $section['title'] }}</h2>
            <p class="text-sm text-base-content/65">{{ $section['subtitle'] }}</p>
        </div>
    </div>
    <a href="{{ $section['url'] }}" class="btn btn-ghost btn-sm {{ $toneClasses[$section['tone']]['text'] }}">
        {{ $section['link'] }}
        <x-daisy::ui.advanced.icon name="bi-arrow-right" class="h-4 w-4" />
    </a>
</div>
