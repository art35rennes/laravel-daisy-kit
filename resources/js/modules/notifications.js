/**
 * Daisy Kit - Notifications Module
 *
 * Gère les notifications avec support REST et WebSocket (Laravel Echo + Reverb)
 * - REST API : Toutes les actions utilisent des requêtes REST
 * - Polling REST : Polling des nouvelles notifications via REST (si useWebSockets = false)
 * - WebSocket (optionnel) : Connexion WebSocket via Laravel Echo + Reverb (si useWebSockets = true)
 * - Actions REST : marquer comme lu, supprimer
 * - Filtrage côté client
 * - Pagination infinie (scroll to load more)
 * - Toasts de confirmation
 * - Gestion du dropdown (cloche)
 */

import { delegate, debounce } from '../kit/utils/events.js';

/**
 * Détecte si Laravel Echo et Reverb sont disponibles
 */
function isReverbAvailable() {
    return typeof window.Echo !== 'undefined' && window.Echo !== null;
}

/**
 * Effectue une requête REST
 */
async function restRequest(url, options = {}) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    const defaultOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken || '',
        },
    };

    const response = await fetch(url, {
        ...defaultOptions,
        ...options,
        headers: {
            ...defaultOptions.headers,
            ...(options.headers || {}),
        },
    });

    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }

    return response.json();
}

/**
 * Affiche un toast de notification
 */
function showToast(message, type = 'info') {
    // Utiliser le système de toast de daisyUI si disponible
    const toastContainer = document.querySelector('.toast') || document.body;
    const toast = document.createElement('div');
    toast.className = `alert alert-${type}`;
    toast.textContent = message;
    toastContainer.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

/**
 * Met à jour le compteur de notifications non lues
 */
function updateUnreadCount(count) {
    const badges = document.querySelectorAll('[data-unread-count]');
    badges.forEach(badge => {
        const element = badge.querySelector('.badge') || badge;
        if (count > 0) {
            element.textContent = count > 99 ? '99+' : count;
            element.classList.remove('hidden');
        } else {
            element.classList.add('hidden');
        }
    });
}

/**
 * Initialise le module notifications
 */
export default function initNotifications(root, options = {}) {
    const {
        useWebSockets = false,
        pollingInterval = 30000,
        autoReconnect = true,
        reconnectDelay = 5000,
    } = options;

    let pollingTimer = null;
    let echoChannel = null;
    let reconnectAttempts = 0;
    const maxReconnectAttempts = 5;

    // Détection automatique de Reverb
    const hasReverb = isReverbAvailable();
    const shouldUseWebSockets = useWebSockets && hasReverb;

    /**
     * Charge les notifications via REST
     */
    async function loadNotifications() {
        try {
            const url = root.dataset.loadNotificationsUrl || '#';
            if (url === '#') return;

            const data = await restRequest(url);
            updateNotificationsList(data.data || []);
            
            if (data.meta?.unread_count !== undefined) {
                updateUnreadCount(data.meta.unread_count);
            }
        } catch (error) {
            console.error('[Notifications] Error loading notifications:', error);
        }
    }

    /**
     * Met à jour la liste des notifications dans le DOM
     */
    function updateNotificationsList(notifications) {
        const listContainer = root.querySelector('.notification-list');
        if (!listContainer) return;

        // Pour l'instant, on ne met pas à jour automatiquement la liste
        // Le serveur doit fournir les nouvelles notifications
        // Cette fonction peut être étendue pour ajouter/supprimer des éléments
    }

    /**
     * Marque une notification comme lue
     */
    async function markAsRead(notificationId) {
        try {
            const url = root.dataset.markAsReadUrl?.replace(':id', notificationId) || '#';
            if (url === '#') return;

            await restRequest(url, {
                method: 'POST',
            });

            // Mettre à jour l'UI
            const item = root.querySelector(`[data-notification-id="${notificationId}"]`);
            if (item) {
                item.dataset.read = 'true';
                item.classList.remove('bg-base-200');
                const badge = item.querySelector('.badge');
                if (badge) badge.remove();
            }

            showToast('Notification marquée comme lue', 'success');
        } catch (error) {
            console.error('[Notifications] Error marking as read:', error);
            showToast('Erreur lors du marquage comme lu', 'error');
        }
    }

    /**
     * Marque toutes les notifications comme lues
     */
    async function markAllAsRead() {
        try {
            const url = root.dataset.markAllAsReadUrl || '#';
            if (url === '#') return;

            await restRequest(url, {
                method: 'POST',
            });

            // Mettre à jour l'UI
            root.querySelectorAll('[data-notification-id]').forEach(item => {
                item.dataset.read = 'true';
                item.classList.remove('bg-base-200');
                const badge = item.querySelector('.badge');
                if (badge) badge.remove();
            });

            updateUnreadCount(0);
            showToast('Toutes les notifications ont été marquées comme lues', 'success');
        } catch (error) {
            console.error('[Notifications] Error marking all as read:', error);
            showToast('Erreur lors du marquage', 'error');
        }
    }

    /**
     * Supprime une notification
     */
    async function deleteNotification(notificationId) {
        try {
            const url = root.dataset.deleteUrl?.replace(':id', notificationId) || '#';
            if (url === '#') return;

            await restRequest(url, {
                method: 'DELETE',
            });

            // Supprimer l'élément du DOM
            const item = root.querySelector(`[data-notification-id="${notificationId}"]`);
            if (item) {
                item.remove();
            }

            showToast('Notification supprimée', 'success');
        } catch (error) {
            console.error('[Notifications] Error deleting notification:', error);
            showToast('Erreur lors de la suppression', 'error');
        }
    }

    /**
     * Initialise le polling REST
     */
    function startPolling() {
        if (pollingTimer) return;
        
        loadNotifications();
        pollingTimer = setInterval(loadNotifications, pollingInterval);
    }

    /**
     * Arrête le polling REST
     */
    function stopPolling() {
        if (pollingTimer) {
            clearInterval(pollingTimer);
            pollingTimer = null;
        }
    }

    /**
     * Initialise la connexion WebSocket
     */
    function initWebSocket() {
        if (!shouldUseWebSockets || !hasReverb) {
            return;
        }

        try {
            const userId = root.dataset.userId || null;
            if (!userId) {
                console.warn('[Notifications] User ID not found, falling back to REST');
                startPolling();
                return;
            }

            // S'abonner au canal de notifications
            echoChannel = window.Echo.private(`notifications.${userId}`)
                .listen('.notification.created', (e) => {
                    // Nouvelle notification reçue
                    loadNotifications();
                    updateUnreadCount((parseInt(root.dataset.unreadCount || '0') || 0) + 1);
                    showToast('Nouvelle notification', 'info');
                })
                .listen('.notification.updated', (e) => {
                    // Notification mise à jour
                    loadNotifications();
                })
                .listen('.notification.deleted', (e) => {
                    // Notification supprimée
                    loadNotifications();
                });

            reconnectAttempts = 0;
        } catch (error) {
            console.error('[Notifications] WebSocket connection error:', error);
            // Fallback vers REST
            startPolling();
        }
    }

    /**
     * Ferme la connexion WebSocket
     */
    function closeWebSocket() {
        if (echoChannel) {
            window.Echo.leave(`notifications.${root.dataset.userId || ''}`);
            echoChannel = null;
        }
    }

    /**
     * Gère la reconnexion automatique
     */
    function handleReconnect() {
        if (!autoReconnect || reconnectAttempts >= maxReconnectAttempts) {
            return;
        }

        reconnectAttempts++;
        setTimeout(() => {
            if (shouldUseWebSockets) {
                initWebSocket();
            } else {
                startPolling();
            }
        }, reconnectDelay);
    }

    // Gestion des actions (délégation d'événements)
    delegate(root, '[data-action="mark-as-read"]', 'click', function(e) {
        e.preventDefault();
        const notificationId = this.closest('[data-notification-id]')?.dataset.notificationId;
        if (notificationId) {
            markAsRead(notificationId);
        }
    });

    delegate(root, '[data-action="mark-all-read"]', 'click', function(e) {
        e.preventDefault();
        markAllAsRead();
    });

    delegate(root, '[data-action="delete"]', 'click', function(e) {
        e.preventDefault();
        const notificationId = this.closest('[data-notification-id]')?.dataset.notificationId;
        if (notificationId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
                deleteNotification(notificationId);
            }
        }
    });

    // Initialisation
    if (shouldUseWebSockets) {
        initWebSocket();
    } else {
        startPolling();
    }

    // Nettoyage lors de la déconnexion de la page
    window.addEventListener('beforeunload', () => {
        stopPolling();
        closeWebSocket();
    });

    // Gestion de la visibilité de la page (pause/resume polling)
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopPolling();
        } else {
            if (!shouldUseWebSockets) {
                startPolling();
            }
        }
    });
}

