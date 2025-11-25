@props([
    'statusCode' => 500, // 404, 403, 500, 503, etc.
    'title' => null, // Auto-generated if null
    'message' => null, // Auto-generated if null
    'theme' => null,
    // Routes
    'homeUrl' => Route::has('home') ? route('home') : '/',
    'backUrl' => url()->previous(),
    // Options
    'showIllustration' => true,
    'showActions' => true,
    'showDetails' => null, // Auto-détecté depuis config('app.debug'), ne peut pas être forcé à true en production
    'exception' => null, // $exception from Laravel (injected automatically)
])

@php
    // SÉCURITÉ : Force showDetails à false si on n'est pas en mode debug
    // Même si showDetails=true est passé manuellement, on ne l'accepte que si app.debug=true
    $isDebugMode = config('app.debug', false);
    $showDetails = $isDebugMode && ($showDetails !== false);
@endphp

@php
    // Génération automatique du titre si non fourni
    if ($title === null) {
        $title = __('errors.'.$statusCode.'_title', ['default' => __('errors.error_title', ['code' => $statusCode])]);
    }
    
    // Génération automatique du message si non fourni
    if ($message === null) {
        $message = __('errors.'.$statusCode.'_message', ['default' => __('errors.error_message', ['code' => $statusCode])]);
    }
@endphp

<x-daisy::layout.app :title="$title" :theme="$theme" :container="true">
    <div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-8">
        <div class="w-full space-y-8">
            @if($showIllustration)
                <x-daisy::ui.layout.hero
                    :minH="'min-h-[12rem]'"
                    :fullScreen="false"
                    class="mb-8"
                >
                    <div class="text-center">
                        <div class="text-6xl mb-4">⚠️</div>
                        <p class="text-base-content/70">{{ __('errors.error_occurred') }}</p>
                    </div>
                </x-daisy::ui.layout.hero>
            @endif
            
            <x-daisy::ui.errors.error-content
                :statusCode="$statusCode"
                :title="$title"
                :message="$message"
                :homeUrl="$homeUrl"
                :backUrl="$backUrl"
                :showActions="$showActions"
                :showDetails="$showDetails"
                :exception="$exception"
            />
        </div>
    </div>
</x-daisy::layout.app>

