<div class="daisy-kit-map__feedback">
    <div class="alert" data-daisy-kit-map-empty hidden role="status">
        @isset($empty)
            {{ $empty }}
        @else
            <span>{{ $mapView['labels']['empty'] }}</span>
        @endisset
    </div>

    <div class="alert alert-error" data-daisy-kit-map-error hidden role="alert">
        <div>
            @isset($error)
                {{ $error }}
            @else
                <p data-daisy-kit-map-error-message>{{ $mapView['labels']['error'] }}</p>
            @endisset
        </div>
        <button class="btn btn-sm" data-daisy-kit-map-retry type="button">{{ $mapView['labels']['retry'] }}</button>
    </div>

    <aside class="daisy-kit-map__selection border border-primary/30 bg-primary/10" data-daisy-kit-map-selection hidden aria-live="polite">
        <p data-daisy-kit-map-selection-summary></p>
        <button class="btn btn-ghost btn-sm" data-daisy-kit-map-clear-selection type="button">{{ $mapView['labels']['clearSelection'] }}</button>
    </aside>
</div>
