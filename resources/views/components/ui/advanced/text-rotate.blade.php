@props([
    /**
     * @var array<int, string> Words or phrases to cycle (up to 6 recommended by daisyUI).
     */
    'words' => [],
    /** @var string Classes for the inner wrapper span (e.g. justify-items-center). */
    'innerClass' => '',
])

<span {{ $attributes->merge(['class' => 'text-rotate']) }}>
    @if(count($words) > 0)
        <span class="{{ $innerClass }}">
            @foreach($words as $segment)
                <span>{{ $segment }}</span>
            @endforeach
        </span>
    @else
        {{ $slot }}
    @endif
</span>
