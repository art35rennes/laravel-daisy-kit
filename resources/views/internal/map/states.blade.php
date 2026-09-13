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
</div>
