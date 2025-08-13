@pushOnce('styles')
    @if(config('daisy-kit.auto_assets'))
        @php
            $cssEntry = 'resources/css/app.css';
            $vendorCss = resource_path('vendor/daisy-kit/css/app.css');
            if (is_file($vendorCss)) { $cssEntry = 'resources/vendor/daisy-kit/css/app.css'; }
        @endphp
        @if(config('daisy-kit.use_vite'))
            @if(config('daisy-kit.vite_build_directory'))
                @vite($cssEntry, config('daisy-kit.vite_build_directory'))
            @else
                @vite($cssEntry)
            @endif
        @else
            <link rel="stylesheet" href="{{ asset(config('daisy-kit.bundle.css')) }}">
        @endif
    @endif
@endPushOnce

@pushOnce('scripts')
    @if(config('daisy-kit.auto_assets'))
        @php
            $jsEntry = 'resources/js/app.js';
            $vendorJs = resource_path('vendor/daisy-kit/js/app.js');
            if (is_file($vendorJs)) { $jsEntry = 'resources/vendor/daisy-kit/js/app.js'; }
        @endphp
        @if(config('daisy-kit.use_vite'))
            @if(config('daisy-kit.vite_build_directory'))
                @vite($jsEntry, config('daisy-kit.vite_build_directory'))
            @else
                @vite($jsEntry)
            @endif
        @else
            <script src="{{ asset(config('daisy-kit.bundle.js')) }}" defer></script>
        @endif
    @endif
@endPushOnce


