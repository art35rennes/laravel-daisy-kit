@props(['panel', 'toneClasses'])

<div class="space-y-3" data-reporting-chart="progress-list">
    @foreach($panel['items'] as $item)
        <div class="grid grid-cols-1 gap-1 text-xs sm:grid-cols-3 sm:items-center sm:gap-3">
            <span class="truncate font-medium">{{ $item['label'] }}</span>
            <span class="h-3 rounded-full {{ $toneClasses[$panel['tone']]['soft'] }} sm:col-span-1">
                <span class="block h-full rounded-full {{ $toneClasses[$panel['tone']]['bg'] }} {{ $item['width'] }}"></span>
            </span>
            <span class="font-semibold sm:text-right">{{ $item['value'] }}</span>
        </div>
    @endforeach
</div>
