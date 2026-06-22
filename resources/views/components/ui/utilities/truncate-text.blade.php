@props([
    'text' => null,
    'maxWidth' => 'max-w-full',
    'tag' => 'span',
    'tooltip' => true,
    'reveal' => null,
    'position' => null,
    'tooltipPosition' => 'top',
    'popoverPosition' => null,
    'onlyWhenTruncated' => null,
    'tooltipOnlyWhenTruncated' => true,
    'actionHint' => false,
    'lines' => 1,
    'title' => null,
])

@php
    $content = (string) $text;
    $tooltipText = (string) ($title ?? $content);
    $validRevealModes = ['tooltip', 'popover', 'both', 'none'];
    $legacyRevealMode = filter_var($tooltip, FILTER_VALIDATE_BOOLEAN) ? 'tooltip' : 'none';
    $revealMode = in_array($reveal, $validRevealModes, true) ? $reveal : $legacyRevealMode;
    $hasTooltip = in_array($revealMode, ['tooltip', 'both'], true);
    $hasPopover = in_array($revealMode, ['popover', 'both'], true);
    $overflowOnly = is_null($onlyWhenTruncated)
        ? filter_var($tooltipOnlyWhenTruncated, FILTER_VALIDATE_BOOLEAN)
        : filter_var($onlyWhenTruncated, FILTER_VALIDATE_BOOLEAN);
    $lineCount = max(1, (int) $lines);
    $validTags = ['span', 'p', 'div', 'strong', 'em', 'small', 'code', 'time'];
    $elementTag = in_array($tag, $validTags, true) ? $tag : 'span';
    $validPositions = ['top', 'right', 'bottom', 'left'];
    $resolvedPosition = $position ?? $tooltipPosition;
    $tooltipPlacement = in_array($resolvedPosition, $validPositions, true) ? $resolvedPosition : 'top';
    $resolvedPopoverPosition = $popoverPosition ?? $tooltipPlacement;
    $popoverPlacement = in_array($resolvedPopoverPosition, $validPositions, true) ? $resolvedPopoverPosition : 'top';
    $truncateClass = $lineCount === 1 ? 'truncate' : "line-clamp-{$lineCount}";
    $customClasses = $attributes->get('class');
    $actionHintClasses = filter_var($actionHint, FILTER_VALIDATE_BOOLEAN)
        ? 'cursor-pointer decoration-dotted underline underline-offset-2 decoration-base-content/40 hover:decoration-base-content focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary'
        : '';
    $contentClass = implode(' ', array_filter(['min-w-0', $maxWidth, $truncateClass, $actionHintClasses, $customClasses ?? null]));
    $contentAttributes = $attributes->except('class')->merge([
        'class' => $contentClass,
        'aria-label' => $content,
    ]);
    $usesMeasuredTooltip = $hasTooltip && $overflowOnly;
    $usesStaticTooltip = $hasTooltip && ! $usesMeasuredTooltip;
    $usesTruncateModule = $usesMeasuredTooltip || $hasPopover;
    $popoverClasses = [
        'top' => 'bottom-full left-1/2 mb-2 -translate-x-1/2',
        'right' => 'left-full top-1/2 ml-2 -translate-y-1/2',
        'bottom' => 'top-full left-1/2 mt-2 -translate-x-1/2',
        'left' => 'right-full top-1/2 mr-2 -translate-y-1/2',
    ][$popoverPlacement];
@endphp

@if($usesTruncateModule)
    <span
        @if($hasTooltip) data-tip="{{ $tooltipText }}" @endif
        class="{{ trim('relative inline-block max-w-full align-middle '.($hasTooltip ? "tooltip tooltip-{$tooltipPlacement} before:!delay-0 after:!delay-0 before:!duration-75 after:!duration-75" : '')) }}"
    >
        <{{ $elementTag }}
            {{ $contentAttributes->merge([
                'data-module' => 'truncate-text',
                'data-truncate-text-title' => $tooltipText,
                'data-truncate-text-position' => $tooltipPlacement,
                'data-truncate-text-reveal' => $revealMode,
                'data-truncate-text-only-when-truncated' => $overflowOnly ? 'true' : 'false',
            ]) }}
        >{{ $content }}</{{ $elementTag }}>
        @if($hasPopover)
            <span
                class="daisy-truncate-popover pointer-events-auto absolute z-50 hidden {{ $popoverClasses }}"
                role="dialog"
                aria-hidden="true"
            >
                <span class="block max-w-xs select-text whitespace-normal break-words rounded-box bg-base-100 px-3 py-2 text-sm leading-relaxed text-base-content shadow-lg ring-1 ring-base-content/10">
                    {{ $tooltipText }}
                </span>
            </span>
        @endif
    </span>
    @include('daisy::components.partials.assets')
@elseif($usesStaticTooltip)
    <x-daisy::ui.overlay.tooltip :text="$tooltipText" :position="$tooltipPlacement">
        <{{ $elementTag }} {{ $contentAttributes->merge(['tabindex' => '0']) }}>{{ $content }}</{{ $elementTag }}>
    </x-daisy::ui.overlay.tooltip>
@else
    <{{ $elementTag }} {{ $contentAttributes->merge(['title' => $tooltipText]) }}>{{ $content }}</{{ $elementTag }}>
@endif
