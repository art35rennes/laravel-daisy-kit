/**
 * Daisy Kit - CSRF Keeper
 *
 * Module pour rafraîchir automatiquement le token CSRF à intervalles réguliers
 * ou à la demande, afin d'éviter les échecs de soumission après mise en veille.
 */

export default function init(element, options) {
    const refreshInterval = parseInt(element.dataset.refreshInterval, 10) || 5760000; // 96 minutes par défaut
    const endpoint = element.dataset.endpoint || '/daisy-kit/csrf-token.json';
    
    let intervalId = null;
    let isRefreshing = false;

    /**
     * Rafraîchit le token CSRF.
     */
    async function refreshToken() {
        if (isRefreshing) {
            return;
        }

        isRefreshing = true;

        try {
            const response = await fetch(endpoint, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            const token = data.token;

            if (!token) {
                throw new Error('Token not found in response');
            }

            // Mettre à jour le meta tag CSRF
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                metaTag.setAttribute('content', token);
            }

            // Mettre à jour le cookie _token si présent
            const cookies = document.cookie.split(';');
            const tokenCookie = cookies.find(cookie => cookie.trim().startsWith('_token='));
            if (tokenCookie) {
                document.cookie = `_token=${token}; path=/; SameSite=Lax`;
            }

            // Mettre à jour tous les inputs cachés _token
            const hiddenInputs = document.querySelectorAll('input[name="_token"]');
            hiddenInputs.forEach(input => {
                input.value = token;
            });

            // Dispatcher un event pour notifier les listeners
            const event = new CustomEvent('csrf-keeper:updated', {
                detail: { token },
            });
            document.dispatchEvent(event);
        } catch (error) {
            console.warn('[CSRF Keeper] Failed to refresh token:', error);
            
            // Dispatcher un event d'erreur
            const errorEvent = new CustomEvent('csrf-keeper:error', {
                detail: { error },
            });
            document.dispatchEvent(errorEvent);
        } finally {
            isRefreshing = false;
        }
    }

    /**
     * Démarre le rafraîchissement automatique.
     */
    function startAutoRefresh() {
        if (intervalId !== null) {
            return;
        }

        intervalId = setInterval(() => {
            refreshToken();
        }, refreshInterval);
    }

    /**
     * Arrête le rafraîchissement automatique.
     */
    function stopAutoRefresh() {
        if (intervalId !== null) {
            clearInterval(intervalId);
            intervalId = null;
        }
    }

    // Écouter les events de rafraîchissement manuel
    element.addEventListener('csrf-keeper:refresh', () => {
        refreshToken();
    });

    // Démarrer le rafraîchissement automatique
    startAutoRefresh();

    // Rafraîchir immédiatement au chargement
    refreshToken();

    // Nettoyer à la destruction
    return () => {
        stopAutoRefresh();
    };
}

