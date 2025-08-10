@props([
    'resizable' => false,
])

<div {{ $attributes->merge(['class' => 'diff']) }}>
  <div class="diff-item-1">{{ $before ?? '' }}</div>
  <div class="diff-item-2">{{ $after ?? '' }}</div>
  @if($resizable)
    <div class="diff-resizer"></div>
  @endif
</div>
