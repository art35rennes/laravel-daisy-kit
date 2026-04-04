@pushOnce('styles')
    @if(config('daisy-kit.auto_assets'))
        @php
            $assetManager = \Art35rennes\DaisyKit\Support\PackageAsset::class;
            $cssEntry = $assetManager::sourceEntry('css');
            $hasManifest = $assetManager::hasManifest();
            $hasPublishedSource = $assetManager::hasPublishedSource('css');
            $buildDirectory = $assetManager::buildDirectory();
        @endphp
        @if(config('daisy-kit.use_vite'))
            @if($hasManifest)
                @vite($cssEntry, $buildDirectory)
            @elseif($hasPublishedSource)
                @vite($cssEntry)
            @else
                {!! $assetManager::stylesheetTags($cssEntry) !!}
            @endif
        @else
            {!! $assetManager::stylesheetTags($cssEntry) !!}
        @endif
    @endif
@endPushOnce

@pushOnce('scripts')
    @if(config('daisy-kit.auto_assets'))
        @php
            $assetManager = \Art35rennes\DaisyKit\Support\PackageAsset::class;
            $jsEntry = $assetManager::sourceEntry('js');
            $hasManifest = $assetManager::hasManifest();
            $hasPublishedSource = $assetManager::hasPublishedSource('js');
            $buildDirectory = $assetManager::buildDirectory();
        @endphp
        @if(config('daisy-kit.use_vite'))
            @if($hasManifest)
                @vite($jsEntry, $buildDirectory)
            @elseif($hasPublishedSource)
                @vite($jsEntry)
            @else
                {!! $assetManager::scriptTags($jsEntry) !!}
            @endif
        @else
            {!! $assetManager::scriptTags($jsEntry) !!}
        @endif
    @endif
@endPushOnce

