@props(['panel', 'toneClasses'])

<div class="flex h-40 items-end gap-2" data-reporting-chart="bars" role="img" aria-label="{{ $panel['title'] }}">
    @foreach($panel['items'] as $item)
        <div class="flex min-w-0 flex-1 flex-col items-center gap-2">
            <span class="text-xs font-bold">{{ $item['value'] }}</span>
            <span class="w-full rounded-t-box {{ $toneClasses[$panel['tone']]['bg'] }} {{ $item['height'] }}"></span>
            <span class="max-w-full truncate text-xs text-base-content/60">{{ $item['label'] }}</span>
        </div>
    @endforeach
</div>
