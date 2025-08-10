@props([
    'items' => [], // [['when' => '2024', 'title' => 'Titre', 'content' => '...']]
    'orientation' => 'vertical', // vertical|horizontal
])

@php
    $classes = 'timeline';
    $classes .= $orientation === 'horizontal' ? ' timeline-horizontal' : ' timeline-vertical';
@endphp

<ul {{ $attributes->merge(['class' => $classes]) }}>
    @foreach($items as $item)
        <li>
            <div class="timeline-start">{{ $item['when'] ?? '' }}</div>
            <div class="timeline-middle">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="text-primary"><circle cx="12" cy="12" r="6"/></svg>
            </div>
            <div class="timeline-end timeline-box">
                @if(!empty($item['title']))
                    <div class="font-medium mb-1">{{ $item['title'] }}</div>
                @endif
                <div>{{ $item['content'] ?? '' }}</div>
            </div>
        </li>
    @endforeach
</ul>
