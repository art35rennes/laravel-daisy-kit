    <!doctype html>
    <html lang="en">
        <head>
            <meta http-equiv="Content-Security-Policy" content="script-src 'self' 'nonce-smoke-nonce'; style-src 'self' 'nonce-smoke-nonce'">
            @include('daisy::components.partials.assets')
            @stack('styles')
        </head>
        <body>
            <x-daisy::ui.layout.hero image-url="/img/example.jpg">
                <x-daisy::ui.data-display.radial-progress :value="92" size="7rem" thickness="0.7rem" color="primary" />
                <x-daisy::ui.inputs.range :no-fill="true" />
                <x-daisy::ui.media.embed src="/frame" />
            </x-daisy::ui.layout.hero>

            <x-daisy::ui.navigation.breadcrumbs :json-ld="true" :items="[
                ['label' => 'Home', 'url' => '/'],
                ['label' => 'Current'],
            ]" />

            @stack('scripts')
        </body>
    </html>