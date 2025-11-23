@props([
    'statusCode' => 500,
    'title' => null,
    'message' => null,
    'homeUrl' => Route::has('home') ? route('home') : '/',
    'backUrl' => url()->previous(),
    'showActions' => true,
    'showDetails' => null, // Auto-détecté depuis config('app.debug'), ne peut pas être forcé à true en production
    'exception' => null, // $exception from Laravel
])

@php
    // SÉCURITÉ : Force showDetails à false si on n'est pas en mode debug
    // Même si showDetails=true est passé manuellement, on ne l'accepte que si app.debug=true
    $isDebugMode = config('app.debug', false);
    $showDetails = $isDebugMode && ($showDetails !== false);
    
    // Génération automatique du message si non fourni
    if ($message === null) {
        $message = __('errors.'.$statusCode.'_message', ['default' => __('errors.error_message', ['code' => $statusCode])]);
    }
    
    // Extraction des détails de l'exception si disponible (uniquement en mode debug)
    $exceptionMessage = null;
    $exceptionTrace = null;
    if ($showDetails && $exception) {
        $exceptionMessage = $exception->getMessage();
        if (method_exists($exception, 'getTraceAsString')) {
            $exceptionTrace = $exception->getTraceAsString();
        }
    }
@endphp

<x-daisy::ui.layout.card class="max-w-2xl mx-auto">
    <div class="space-y-6">
        {{-- Error header --}}
        <x-daisy::ui.errors.error-header :statusCode="$statusCode" :title="$title" />
        
        {{-- Message --}}
        @if($message)
            <p class="text-center text-base text-base-content opacity-80">
                {{ $message }}
            </p>
        @endif
        
        {{-- Actions --}}
        @if($showActions)
            <x-daisy::ui.errors.error-actions
                :homeUrl="$homeUrl"
                :backUrl="$backUrl"
            />
        @endif
        
        {{-- Debug details --}}
        @if($showDetails && ($exceptionMessage || $exceptionTrace))
            <x-daisy::ui.feedback.alert
                color="error"
                variant="outline"
                title="{{ __('errors.debug_details') }}"
            >
                @if($exceptionMessage)
                    <div class="mb-2">
                        <strong>{{ __('errors.exception_message') }}:</strong>
                        <pre class="mt-1 text-xs overflow-x-auto bg-base-200 p-2 rounded">{{ $exceptionMessage }}</pre>
                    </div>
                @endif
                
                @if($exceptionTrace)
                    <details class="mt-2">
                        <summary class="cursor-pointer text-sm font-medium mb-2">{{ __('errors.stack_trace') }}</summary>
                        <pre class="text-xs overflow-x-auto bg-base-200 p-2 rounded max-h-64 overflow-y-auto">{{ $exceptionTrace }}</pre>
                    </details>
                @endif
            </x-daisy::ui.feedback.alert>
        @endif
    </div>
</x-daisy::ui.layout.card>

