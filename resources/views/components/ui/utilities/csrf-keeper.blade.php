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
        try {
            $endpoint = route('daisy-kit.csrf-token');
        } catch (\Exception $e) {
            $endpoint = '/daisy-kit/csrf-token.json';
        }
    }
@endphp

<div 
    data-module="{{ $module }}"
    data-refresh-interval="{{ $refreshInterval }}"
    data-endpoint="{{ $endpoint }}"
    {{ $attributes }}
></div>

