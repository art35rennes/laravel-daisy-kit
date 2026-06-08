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
    'mobileLabel' => null,
    'color' => null, // primary|secondary|accent|neutral|info|success|warning|error
    'outline' => false,
    'paginator' => null,
])

@php
    if ($paginator instanceof \Illuminate\Contracts\Pagination\Paginator) {
        $current = method_exists($paginator, 'currentPage') ? $paginator->currentPage() : $current;
        $total = method_exists($paginator, 'lastPage') ? $paginator->lastPage() : $total;
    }

    $total = max(1, (int) $total);
    $current = max(1, min((int) $current, $total));

    $sizeSuffix = in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'], true) ? ' btn-'.$size : '';
    $colorSuffix = in_array($color, ['primary', 'secondary', 'accent', 'neutral', 'info', 'success', 'warning', 'error'], true) ? ' btn-'.$color : '';
    $outlineSuffix = $outline ? ' btn-outline' : '';

    // Shared segment classes for join items (DaisyUI: join + btn + join-item on each control).
    $segmentClass = 'btn join-item'.$sizeSuffix.$colorSuffix.$outlineSuffix;

    $resolvedMobileLabel = $mobileLabel ?: __('daisy::components.pagination_page', ['current' => $current, 'total' => $total]);
    $mobileInfo = str_replace([':current', ':total'], [$current, $total], (string) $resolvedMobileLabel);
    $prevUrl = $paginator instanceof \Illuminate\Contracts\Pagination\Paginator && method_exists($paginator, 'previousPageUrl') ? $paginator->previousPageUrl() : null;
    $nextUrl = $paginator instanceof \Illuminate\Contracts\Pagination\Paginator && method_exists($paginator, 'nextPageUrl') ? $paginator->nextPageUrl() : null;
    $previousAriaLabel = __('daisy::common.previous');
    $nextAriaLabel = __('daisy::common.next');

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
            @if($prevUrl)
                <a
                    href="{{ $prevUrl }}"
                    class="btn join-item flex-1{{ $sizeSuffix }}{{ $colorSuffix }}{{ $outlinePrevNext ? ' btn-outline' : '' }}"
                    aria-label="{{ $previousAriaLabel }}"
                >{{ $prevLabel }}</a>
            @else
                <button
                    type="button"
                    class="btn join-item flex-1{{ $sizeSuffix }}{{ $colorSuffix }}{{ $outlinePrevNext ? ' btn-outline' : '' }}"
                    @disabled($current === 1)
                >{{ $prevLabel }}</button>
            @endif
            @if($nextUrl)
                <a
                    href="{{ $nextUrl }}"
                    class="btn join-item flex-1{{ $sizeSuffix }}{{ $colorSuffix }}{{ $outlinePrevNext ? ' btn-outline' : '' }}"
                    aria-label="{{ $nextAriaLabel }}"
                >{{ $nextLabel }}</a>
            @else
                <button
                    type="button"
                    class="btn join-item flex-1{{ $sizeSuffix }}{{ $colorSuffix }}{{ $outlinePrevNext ? ' btn-outline' : '' }}"
                    @disabled($current === $total)
                >{{ $nextLabel }}</button>
            @endif
        </div>
    @else
        <div class="overflow-x-auto max-w-full">
            <div class="join join-horizontal min-w-0">
                @if($edges)
                    @if($prevUrl)
                        <a
                            href="{{ $prevUrl }}"
                            class="{{ $segmentClass }}"
                            aria-label="{{ $previousAriaLabel }}"
                        >{{ $prevLabel }}</a>
                    @else
                        <button
                            type="button"
                            class="{{ $segmentClass }}"
                            @disabled($current === 1)
                            aria-label="{{ $previousAriaLabel }}"
                        >{{ $prevLabel }}</button>
                    @endif
                @endif
                @if($responsive)
                    <span class="btn join-item{{ $sizeSuffix }} btn-ghost pointer-events-none cursor-default sm:hidden" role="status">{{ $mobileInfo }}</span>
                @endif
                @foreach($pages as $pageNumber)
                    @php
                        $resolvedUrl = $paginator instanceof \Illuminate\Contracts\Pagination\Paginator && method_exists($paginator, 'url') && is_int($pageNumber) ? $paginator->url($pageNumber) : null;
                    @endphp

                    @if($pageNumber === null)
                        <span
                            class="{{ $segmentClass }} btn-disabled pointer-events-none select-none hidden sm:inline-flex"
                            aria-hidden="true"
                        >&hellip;</span>
                    @elseif($resolvedUrl)
                        <a
                            href="{{ $resolvedUrl }}"
                            class="{{ $segmentClass }} {{ $pageNumber === $current ? 'btn-active' : '' }} hidden sm:inline-flex"
                            @if($pageNumber === $current) aria-current="page" @endif
                        >{{ $pageNumber }}</a>
                    @else
                        <button
                            type="button"
                            class="{{ $segmentClass }} {{ $pageNumber === $current ? 'btn-active' : '' }} hidden sm:inline-flex"
                            @if($pageNumber === $current) aria-current="page" @endif
                        >{{ $pageNumber }}</button>
                    @endif
                @endforeach
                @if($edges)
                    @if($nextUrl)
                        <a
                            href="{{ $nextUrl }}"
                            class="{{ $segmentClass }}"
                            aria-label="{{ $nextAriaLabel }}"
                        >{{ $nextLabel }}</a>
                    @else
                        <button
                            type="button"
                            class="{{ $segmentClass }}"
                            @disabled($current === $total)
                            aria-label="{{ $nextAriaLabel }}"
                        >{{ $nextLabel }}</button>
                    @endif
                @endif
            </div>
        </div>
    @endif
</nav>
