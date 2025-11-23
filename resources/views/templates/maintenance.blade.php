@props([
    'title' => __('maintenance.maintenance'),
    'theme' => null,
    'message' => null, // Custom message or use Laravel's
    'retryAfter' => null, // Retry-After header value
    'allowedIps' => [], // IPs allowed during maintenance
])

@php
    // Utilisation du message Laravel par défaut si non fourni
    if ($message === null) {
        $message = __('maintenance.message');
    }
    
    // Formatage de la date de retour estimée si disponible
    $estimatedReturn = null;
    if ($retryAfter) {
        try {
            $carbon = \Carbon\Carbon::now()->addSeconds($retryAfter);
            $estimatedReturn = $carbon->format('d/m/Y H:i');
        } catch (\Exception $e) {
            // Ignore si Carbon n'est pas disponible ou erreur de formatage
        }
    }
@endphp

<x-daisy::layout.app :title="$title" :theme="$theme" :container="true">
    <div class="min-h-screen flex items-center justify-center py-8">
        <div class="w-full max-w-2xl space-y-6">
            <x-daisy::ui.layout.hero
                :minH="'min-h-[16rem]'"
                :fullScreen="false"
            >
                <div class="text-center space-y-4">
                    <x-daisy::ui.feedback.loading shape="spinner" size="xl" />
                    <h1 class="text-3xl font-bold text-base-content">
                        {{ $title }}
                    </h1>
                </div>
            </x-daisy::ui.layout.hero>
            
            <x-daisy::ui.feedback.alert
                color="warning"
                variant="soft"
                title="{{ __('maintenance.maintenance') }}"
            >
                <p>{{ $message }}</p>
                
                @if($estimatedReturn)
                    <p class="mt-2 text-sm">
                        {{ __('maintenance.estimated_return', ['time' => $estimatedReturn]) }}
                    </p>
                @endif
            </x-daisy::ui.feedback.alert>
            
            @if(!empty($allowedIps))
                <x-daisy::ui.feedback.alert
                    color="info"
                    variant="outline"
                    title="{{ __('maintenance.allowed_ips') }}"
                >
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach($allowedIps as $ip)
                            <li>{{ $ip }}</li>
                        @endforeach
                    </ul>
                </x-daisy::ui.feedback.alert>
            @endif
        </div>
    </div>
</x-daisy::layout.app>

