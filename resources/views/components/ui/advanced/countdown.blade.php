@props([
    'values' => [], // e.g. ['days' => 15, 'hours' => 10, 'min' => 24, 'sec' => 39]
    // Affichage: stacked (par défaut) | inline | inline-colon
    'mode' => 'stacked',
    // Taille de police: sm|md|lg|xl
    'size' => 'md',
    // Afficher les libellés sous chaque valeur (stacked uniquement)
    'labels' => true,
])

@php
    $sizeMap = [
        'sm' => 'text-2xl',
        'md' => 'text-4xl',
        'lg' => 'text-5xl',
        'xl' => 'text-6xl',
    ];
    $textSize = $sizeMap[$size] ?? 'text-4xl';

    $normalized = [];
    foreach ($values as $k => $v) {
        $normalized[] = ['label' => (string)$k, 'value' => (int)$v];
    }
@endphp

@if($mode === 'inline' || $mode === 'inline-colon')
    <span class="countdown font-mono {{ $textSize }}">
        @foreach($normalized as $i => $item)
            <span style="--value: {{ $item['value'] }};" aria-live="polite" aria-label="{{ $item['value'] }}">{{ $item['value'] }}</span>
            @if($i < count($normalized) - 1)
                @if($mode === 'inline-colon')
                    <span class="mx-1">:</span>
                @else
                    <span class="mx-1">{{ str($item['label'])->substr(0,1) }}</span>
                @endif
            @elseif($mode === 'inline')
                <span class="ml-1">{{ str($item['label'])->substr(0,1) }}</span>
            @endif
        @endforeach
    </span>
@else
    <div class="grid grid-flow-col gap-5 text-center auto-cols-max">
        @foreach($normalized as $item)
            <div class="flex flex-col">
                <span class="countdown font-mono {{ $textSize }}">
                    <span style="--value: {{ $item['value'] }};" aria-live="polite" aria-label="{{ $item['value'] }}">{{ $item['value'] }}</span>
                </span>
                @if($labels)
                    <span class="text-xs uppercase">{{ $item['label'] }}</span>
                @endif
            </div>
        @endforeach
    </div>
@endif
