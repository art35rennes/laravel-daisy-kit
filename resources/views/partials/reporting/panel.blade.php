@props(['panel', 'toneClasses'])

<article class="rounded-box border border-base-300 bg-base-100 p-4">
    <div class="mb-4">
        <h3 class="text-sm font-bold">{{ $panel['title'] }}</h3>
        @if(! empty($panel['caption']))
            <p class="mt-1 text-xs text-base-content/60">{{ $panel['caption'] }}</p>
        @endif
    </div>

    @if($panel['type'] === 'donut')
        @include('daisy::partials.reporting.static-donut', ['panel' => $panel])
    @elseif($panel['type'] === 'bars')
        @include('daisy::partials.reporting.static-bars', ['panel' => $panel, 'toneClasses' => $toneClasses])
    @elseif($panel['type'] === 'line')
        @include('daisy::partials.reporting.static-line', ['panel' => $panel])
    @else
        @include('daisy::partials.reporting.progress-list', ['panel' => $panel, 'toneClasses' => $toneClasses])
    @endif
</article>
