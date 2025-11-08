@props([
    'title' => null,
    'value' => null,
    'desc' => null,
    // Classes personnalisables
    'titleClass' => null,
    'valueClass' => null,
    'descClass' => null,
])

<div {{ $attributes->merge(['class' => 'stat min-w-0']) }}>
    @isset($figure)
        <div class="stat-figure">{{ $figure }}</div>
    @endisset
    @if($title)
        <div class="stat-title {{ $titleClass }}">{{ $title }}</div>
    @endif
    @if($value)
        <div class="stat-value {{ $valueClass }}">{{ $value }}</div>
    @endif
    @if(!is_null($desc))
        <div class="stat-desc {{ $descClass }}">{{ $desc }}</div>
    @endif
    @isset($actions)
        <div class="stat-actions">{{ $actions }}</div>
    @endisset
</div>
