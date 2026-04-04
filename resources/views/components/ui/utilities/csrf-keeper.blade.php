@props([
    // Intervalle de rafraîchissement en millisecondes (optionnel, calculé automatiquement si non fourni)
    'refreshInterval' => null,
    // Ratio de sécurité pour le calcul automatique (0.8 = 80% du lifetime de session)
    'refreshRatio' => 0.8,
    // Endpoint pour rafraîchir le token (route du package)
    'endpoint' => null,
    // Override du nom du module JS
    'module' => 'csrf-keeper',
])

@php
    // Calculer l'intervalle si non fourni
    if ($refreshInterval === null) {
        $sessionLifetime = (int) config('session.lifetime', 120); // minutes
        $refreshInterval = (int) ($sessionLifetime * 60 * 1000 * $refreshRatio); // convertit en ms et applique le ratio
    }
    
    // Déterminer l'endpoint
    if ($endpoint === null) {
        $routeEnabled = (bool) config('daisy-kit.csrf_refresh.enabled', true);

        if ($routeEnabled) {
            $routeName = (string) config('daisy-kit.csrf_refresh.name', 'daisy-kit.csrf-token');

            try {
                $endpoint = route($routeName);
            } catch (\Exception $e) {
                $endpoint = '/'.ltrim((string) config('daisy-kit.csrf_refresh.path', 'daisy-kit/csrf-token.json'), '/');
            }
        }
    }
@endphp

<div 
    data-module="{{ $module }}"
    data-refresh-interval="{{ $refreshInterval }}"
    @if(filled($endpoint)) data-endpoint="{{ $endpoint }}" @endif
    {{ $attributes }}
></div>
