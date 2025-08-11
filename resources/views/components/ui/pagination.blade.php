@props([
    'total' => 1,
    'current' => 1,
    'size' => null, // xs|sm|md|lg|xl
    'edges' => true,
    'maxButtons' => 7,
    'prevLabel' => '«',
    'nextLabel' => '»',
    'equalPrevNext' => false,
    'outlinePrevNext' => false,
    // Améliorations de style
    'color' => null, // primary|secondary|accent|neutral|info|success|warning|error
    'outline' => false, // applique btn-outline aux boutons numérotés
])

@php
    $total = max(1, (int) $total);
    $current = max(1, min($current, $total));

    $btnSize = '';
    if (in_array($size, ['xs','sm','md','lg','xl'], true)) {
        $btnSize = ' btn-'.$size;
    }
    $btnColor = '';
    if (in_array($color, ['primary','secondary','accent','neutral','info','success','warning','error'], true)) {
        $btnColor = ' btn-'.$color;
    }
    $btnOutline = $outline ? ' btn-outline' : '';

    // Build pages with ellipsis (null)
    $maxButtons = max(3, (int) $maxButtons);
    $pages = [];
    if ($total <= $maxButtons) {
        for ($i = 1; $i <= $total; $i++) $pages[] = $i;
    } else {
        $pages[] = 1;
        $window = $maxButtons - 2;
        $half = (int) floor($window / 2);
        $start = max(2, $current - $half);
        $end = min($total - 1, $start + $window - 1);
        $start = max(2, $end - $window + 1);
        if ($start > 2) $pages[] = null;
        for ($i = $start; $i <= $end; $i++) $pages[] = $i;
        if ($end < $total - 1) $pages[] = null;
        $pages[] = $total;
    }
@endphp

@if($equalPrevNext)
    <div class="join grid grid-cols-2">
        <button class="join-item btn{{ $btnSize }}{{ $btnColor }} {{ $outlinePrevNext ? 'btn-outline' : '' }}" @disabled($current === 1)>{{ $prevLabel }}</button>
        <button class="join-item btn{{ $btnSize }}{{ $btnColor }} {{ $outlinePrevNext ? 'btn-outline' : '' }}" @disabled($current === $total)>{{ $nextLabel }}</button>
    </div>
@else
    <div class="join">
        @if($edges)
            <button class="btn join-item{{ $btnSize }}{{ $btnColor }}{{ $btnOutline }}" @disabled($current === 1) aria-label="Previous">{{ $prevLabel }}</button>
        @endif
        @foreach($pages as $p)
            @if(is_null($p))
                <button class="btn join-item{{ $btnSize }} btn-disabled">…</button>
            @else
                <button class="btn join-item{{ $btnSize }}{{ $btnColor }}{{ $btnOutline }} {{ $p === $current ? 'btn-active' : '' }}">{{ $p }}</button>
            @endif
        @endforeach
        @if($edges)
            <button class="btn join-item{{ $btnSize }}{{ $btnColor }}{{ $btnOutline }}" @disabled($current === $total) aria-label="Next">{{ $nextLabel }}</button>
        @endif
    </div>
@endif
