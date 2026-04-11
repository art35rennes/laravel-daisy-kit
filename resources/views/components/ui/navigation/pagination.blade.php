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
    'responsive' => true,
    'mobileLabel' => 'Page :current',
    'color' => null, // primary|secondary|accent|neutral|info|success|warning|error
    'outline' => false,
])

@php
    $total = max(1, (int) $total);
    $current = max(1, min((int) $current, $total));

    $sizeSuffix = in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'], true) ? ' btn-'.$size : '';
    $colorSuffix = in_array($color, ['primary', 'secondary', 'accent', 'neutral', 'info', 'success', 'warning', 'error'], true) ? ' btn-'.$color : '';
    $outlineSuffix = $outline ? ' btn-outline' : '';

    // Shared segment classes for join items (DaisyUI: join + btn + join-item on each control).
    $segmentClass = 'btn join-item'.$sizeSuffix.$colorSuffix.$outlineSuffix;

    $mobileInfo = str_replace([':current', ':total'], [$current, $total], (string) $mobileLabel);

    $maxButtons = max(3, (int) $maxButtons);
    $pages = [];
    if ($total <= $maxButtons) {
        for ($i = 1; $i <= $total; $i++) {
            $pages[] = $i;
        }
    } else {
        $pages[] = 1;
        $window = $maxButtons - 2;
        $half = (int) floor($window / 2);
        $start = max(2, $current - $half);
        $end = min($total - 1, $start + $window - 1);
        $start = max(2, $end - $window + 1);
        if ($start > 2) {
            $pages[] = null;
        }
        for ($i = $start; $i <= $end; $i++) {
            $pages[] = $i;
        }
        if ($end < $total - 1) {
            $pages[] = null;
        }
        $pages[] = $total;
    }
@endphp

<nav aria-label="{{ __('daisy::components.pagination') }}" {{ $attributes->class('inline-block w-full max-w-full') }}>
    @if($equalPrevNext)
        <div class="join join-horizontal w-full">
            <button
                type="button"
                class="btn join-item flex-1{{ $sizeSuffix }}{{ $colorSuffix }}{{ $outlinePrevNext ? ' btn-outline' : '' }}"
                @disabled($current === 1)
            >{{ $prevLabel }}</button>
            <button
                type="button"
                class="btn join-item flex-1{{ $sizeSuffix }}{{ $colorSuffix }}{{ $outlinePrevNext ? ' btn-outline' : '' }}"
                @disabled($current === $total)
            >{{ $nextLabel }}</button>
        </div>
    @else
        <div class="overflow-x-auto max-w-full">
            <div class="join join-horizontal min-w-0">
                @if($edges)
                    <button
                        type="button"
                        class="{{ $segmentClass }}"
                        @disabled($current === 1)
                        aria-label="Previous"
                    >{{ $prevLabel }}</button>
                @endif
                @if($responsive)
                    <span class="btn join-item{{ $sizeSuffix }} btn-ghost pointer-events-none cursor-default sm:hidden" role="status">{{ $mobileInfo }}</span>
                @endif
                @foreach($pages as $pageNumber)
                    @if($pageNumber === null)
                        <span
                            class="{{ $segmentClass }} btn-disabled pointer-events-none select-none hidden sm:inline-flex"
                            aria-hidden="true"
                        >&hellip;</span>
                    @else
                        <button
                            type="button"
                            class="{{ $segmentClass }} {{ $pageNumber === $current ? 'btn-active' : '' }} hidden sm:inline-flex"
                        >{{ $pageNumber }}</button>
                    @endif
                @endforeach
                @if($edges)
                    <button
                        type="button"
                        class="{{ $segmentClass }}"
                        @disabled($current === $total)
                        aria-label="Next"
                    >{{ $nextLabel }}</button>
                @endif
            </div>
        </div>
    @endif
</nav>
