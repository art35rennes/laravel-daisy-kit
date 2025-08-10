@props([
    'title' => null,
    'value' => null,
    'desc' => null,
])

<div {{ $attributes->merge(['class' => 'stat']) }}>
    @isset($figure)
        <div class="stat-figure">{{ $figure }}</div>
    @endisset
    @if($title)
        <div class="stat-title">{{ $title }}</div>
    @endif
    @if($value)
        <div class="stat-value">{{ $value }}</div>
    @endif
    @if(!is_null($desc))
        <div class="stat-desc">{{ $desc }}</div>
    @endif
    @isset($actions)
        <div class="stat-actions">{{ $actions }}</div>
    @endisset
</div>
