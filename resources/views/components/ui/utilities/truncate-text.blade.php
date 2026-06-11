@props([
    'text' => null,
    'maxWidth' => 'max-w-full',
    'tag' => 'span',
    'tooltip' => true,
    'tooltipPosition' => 'top',
    'tooltipOnlyWhenTruncated' => true,
    'lines' => 1,
    'title' => null,
])

@php
    $content = (string) $text;
    $tooltipText = (string) ($title ?? $content);
    $lineCount = max(1, (int) $lines);
    $validTags = ['span', 'p', 'div', 'strong', 'em', 'small', 'code', 'time'];
    $elementTag = in_array($tag, $validTags, true) ? $tag : 'span';
    $validPositions = ['top', 'right', 'bottom', 'left'];
    $position = in_array($tooltipPosition, $validPositions, true) ? $tooltipPosition : 'top';
    $truncateClass = $lineCount === 1 ? 'truncate' : "line-clamp-{$lineCount}";
    $customClasses = $attributes->get('class');
    $contentAttributes = $attributes->except('class')->merge([
        'class' => trim("min-w-0 {$maxWidth} {$truncateClass} ".($customClasses ?? '')),
        'aria-label' => $content,
    ]);
    $usesMeasuredTooltip = filter_var($tooltip, FILTER_VALIDATE_BOOLEAN) && filter_var($tooltipOnlyWhenTruncated, FILTER_VALIDATE_BOOLEAN);
    $usesStaticTooltip = filter_var($tooltip, FILTER_VALIDATE_BOOLEAN) && ! $usesMeasuredTooltip;
@endphp

@if($usesMeasuredTooltip)
    <x-daisy::ui.overlay.tooltip :position="$position">
        <{{ $elementTag }}
            {{ $contentAttributes->merge([
                'data-module' => 'truncate-text',
                'data-truncate-text-title' => $tooltipText,
                'data-truncate-text-position' => $position,
            ]) }}
        >{{ $content }}</{{ $elementTag }}>
    </x-daisy::ui.overlay.tooltip>
@elseif($usesStaticTooltip)
    <x-daisy::ui.overlay.tooltip :text="$tooltipText" :position="$position">
        <{{ $elementTag }} {{ $contentAttributes->merge(['tabindex' => '0']) }}>{{ $content }}</{{ $elementTag }}>
    </x-daisy::ui.overlay.tooltip>
@else
    <{{ $elementTag }} {{ $contentAttributes->merge(['title' => $tooltipText]) }}>{{ $content }}</{{ $elementTag }}>
@endif
