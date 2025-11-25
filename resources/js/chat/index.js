/**
 * Daisy Kit - Chat Module
 *
 * Gère le chat avec support REST et WebSocket (Laravel Echo + Reverb)
 * - REST API : Toutes les actions utilisent des requêtes REST
 * - Polling REST : Polling des messages via REST (si useWebSockets = false)
 * - WebSocket (optionnel) : Connexion WebSocket via Laravel Echo + Reverb (si useWebSockets = true)
 * - Envoi de messages (AJAX REST)
 * - Indicateur de frappe
 * - Scroll automatique vers le bas
 * - Upload de fichiers via REST (si activé)
 * - Gestion du widget (minimiser/maximiser)
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
 * Scroll automatique vers le bas d'un conteneur
 */
function scrollToBottom(container) {
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}

/**
 * Initialise le module chat-messages
 */
export function initChatMessages(root, options = {}) {
    const {
        useWebSockets = false,
        pollingInterval = 3000,
        autoReconnect = true,
        reconnectDelay = 5000,
        loadMessagesUrl,
        currentUserId,
        messageIdKey = 'id',
        messageUserIdKey = 'user_id',
        messageContentKey = 'content',
        messageCreatedAtKey = 'created_at',
        messageUserNameKey = 'user_name',
        messageUserAvatarKey = 'user_avatar',
    } = options;

    let pollingTimer = null;
    let echoChannel = null;
    let reconnectAttempts = 0;
    const maxReconnectAttempts = 5;
    const messagesContainer = root;

    // Détection automatique de Reverb
    const hasReverb = isReverbAvailable();
    const shouldUseWebSockets = useWebSockets && hasReverb;

    /**
     * Charge les messages via REST
     */
    async function loadMessages() {
        try {
            const conversationId = root.dataset.conversationId || root.closest('[data-conversation-id]')?.dataset.conversationId;
            if (!conversationId || !loadMessagesUrl || loadMessagesUrl === '#') return;

            const url = loadMessagesUrl.replace(':conversationId', conversationId);
            const data = await restRequest(url);
            updateMessagesList(data.data || []);
            scrollToBottom(messagesContainer);
        } catch (error) {
            console.error('[Chat] Error loading messages:', error);
        }
    }

    /**
     * Met à jour la liste des messages dans le DOM
     */
    function updateMessagesList(messages) {
        // Pour l'instant, on ne met pas à jour automatiquement la liste
        // Le serveur doit fournir les nouveaux messages
        // Cette fonction peut être étendue pour ajouter/supprimer des éléments
    }

    /**
     * Initialise le polling REST
     */
    function startPolling() {
        if (pollingTimer) return;
        
        loadMessages();
        pollingTimer = setInterval(loadMessages, pollingInterval);
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
            const conversationId = root.dataset.conversationId || root.closest('[data-conversation-id]')?.dataset.conversationId;
            if (!conversationId) {
                console.warn('[Chat] Conversation ID not found, falling back to REST');
                startPolling();
                return;
            }

            // S'abonner au canal de chat
            echoChannel = window.Echo.private(`chat.${conversationId}`)
                .listen('.message.sent', (e) => {
                    // Nouveau message reçu
                    loadMessages();
                })
                .listen('.message.updated', (e) => {
                    // Message mis à jour
                    loadMessages();
                })
                .listen('.typing', (e) => {
                    // Indicateur de frappe
                    showTypingIndicator(e.user);
                });

            reconnectAttempts = 0;
        } catch (error) {
            console.error('[Chat] WebSocket connection error:', error);
            // Fallback vers REST
            startPolling();
        }
    }

    /**
     * Affiche l'indicateur de frappe
     */
    function showTypingIndicator(user) {
        const indicator = messagesContainer.querySelector('.chat-typing-indicator');
        if (indicator) {
            indicator.classList.remove('hidden');
            setTimeout(() => {
                indicator.classList.add('hidden');
            }, 3000);
        }
    }

    /**
     * Ferme la connexion WebSocket
     */
    function closeWebSocket() {
        if (echoChannel) {
            const conversationId = root.dataset.conversationId || root.closest('[data-conversation-id]')?.dataset.conversationId;
            if (conversationId) {
                window.Echo.leave(`chat.${conversationId}`);
            }
            echoChannel = null;
        }
    }

    // Initialisation
    scrollToBottom(messagesContainer);
    
    if (shouldUseWebSockets) {
        initWebSocket();
    } else {
        startPolling();
    }

    // Nettoyage
    window.addEventListener('beforeunload', () => {
        stopPolling();
        closeWebSocket();
    });

    // Gestion de la visibilité de la page
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

/**
 * Initialise le module chat-input
 */
export function initChatInput(root, options = {}) {
    const {
        sendMessageUrl,
        typingUrl,
        enableFileUpload = false,
        maxFileSize = 5120,
        multipleFiles = false,
        showFilePreview = true,
        useWebSockets = false,
        autoReconnect = true,
    } = options;

    const messageInput = root.querySelector('[data-message-input]');
    const sendButton = root.querySelector('[data-send-button]');
    const fileInput = root.querySelector('[data-file-input]');
    const filePreviews = root.querySelector('[data-file-previews]');
    let typingTimeout = null;
    let selectedFiles = [];

    /**
     * Met à jour l'indicateur de fichiers attachés
     */
    function updateFileIndicator() {
        const countBadge = root.querySelector('[data-file-count-badge]');
        
        if (countBadge) {
            if (selectedFiles.length > 0) {
                countBadge.textContent = selectedFiles.length;
                countBadge.classList.remove('hidden');
            } else {
                countBadge.classList.add('hidden');
            }
        }
        
        // Afficher/masquer la zone de preview
        if (filePreviews) {
            if (selectedFiles.length > 0) {
                filePreviews.classList.remove('hidden');
            } else {
                filePreviews.classList.add('hidden');
            }
        }
    }

    /**
     * Crée une preview de fichier
     */
    function createFilePreview(file) {
        const preview = document.createElement('div');
        preview.className = 'relative inline-block rounded-box overflow-hidden border border-base-300 bg-base-100 shadow-sm';
        preview.dataset.fileName = file.name;
        
        // Conteneur pour le contenu
        const content = document.createElement('div');
        content.className = 'flex items-center gap-2 p-2';
        
        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'h-16 w-16 object-cover rounded';
            img.alt = file.name;
            content.appendChild(img);
        } else {
            const iconWrapper = document.createElement('div');
            iconWrapper.className = 'flex items-center justify-center h-16 w-16 bg-base-200 rounded';
            iconWrapper.innerHTML = '<svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 16 16"><path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/></svg>';
            content.appendChild(iconWrapper);
        }
        
        // Informations du fichier
        const fileInfo = document.createElement('div');
        fileInfo.className = 'flex-1 min-w-0';
        
        const fileName = document.createElement('div');
        fileName.className = 'text-xs font-medium truncate text-base-content';
        fileName.textContent = file.name;
        fileInfo.appendChild(fileName);
        
        const fileSize = document.createElement('div');
        fileSize.className = 'text-xs text-base-content/70';
        const sizeKB = (file.size / 1024).toFixed(1);
        fileSize.textContent = sizeKB > 1024 ? `${(sizeKB / 1024).toFixed(1)} MB` : `${sizeKB} KB`;
        fileInfo.appendChild(fileSize);
        
        content.appendChild(fileInfo);
        
        // Bouton de suppression
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-ghost btn-xs btn-circle btn-error flex-shrink-0';
        removeBtn.innerHTML = '×';
        removeBtn.title = 'Remove file';
        removeBtn.onclick = () => {
            selectedFiles = selectedFiles.filter(f => f !== file);
            preview.remove();
            updateFileIndicator();
            if (fileInput) {
                const dt = new DataTransfer();
                selectedFiles.forEach(f => dt.items.add(f));
                fileInput.files = dt.files;
            }
        };
        content.appendChild(removeBtn);
        
        preview.appendChild(content);
        
        return preview;
    }

    /**
     * Envoie un message
     */
    async function sendMessage(content, files = []) {
        if (!content.trim() && files.length === 0) return;

        try {
            const conversationId = root.dataset.conversationId || root.closest('[data-conversation-id]')?.dataset.conversationId;
            if (!conversationId || !sendMessageUrl || sendMessageUrl === '#') return;

            const formData = new FormData();
            formData.append('conversation_id', conversationId);
            formData.append('content', content);
            
            files.forEach((file, index) => {
                if (multipleFiles) {
                    formData.append(`files[${index}]`, file);
                } else {
                    formData.append('file', file);
                }
            });

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const response = await fetch(sendMessageUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: formData,
            });

            if (response.ok) {
                const data = await response.json();
                
                // Ajouter le message à la liste des messages (optionnel, pour feedback immédiat)
                if (data.message) {
                    const messagesContainer = root.closest('[data-module="chat-messages"]') || 
                                             document.querySelector('[data-module="chat-messages"]');
                    if (messagesContainer) {
                        // Le message sera ajouté par le polling ou WebSocket
                        // On peut juste scroller vers le bas
                        setTimeout(() => {
                            messagesContainer.scrollTop = messagesContainer.scrollHeight;
                        }, 100);
                    }
                }
            }

            // Réinitialiser l'input
            if (messageInput) {
                messageInput.value = '';
            }
            if (fileInput) {
                fileInput.value = '';
            }
            selectedFiles = [];
            if (filePreviews) {
                filePreviews.innerHTML = '';
            }
            updateFileIndicator();
        } catch (error) {
            console.error('[Chat] Error sending message:', error);
        }
    }

    /**
     * Envoie l'indicateur de frappe
     */
    const sendTypingIndicator = debounce(async () => {
        if (!typingUrl || typingUrl === '#') return;

        try {
            const conversationId = root.dataset.conversationId || root.closest('[data-conversation-id]')?.dataset.conversationId;
            if (!conversationId) return;

            await restRequest(typingUrl, {
                method: 'POST',
                body: JSON.stringify({ conversation_id: conversationId }),
            });
        } catch (error) {
            // Ignorer les erreurs de typing
        }
    }, 1000);

    // Gestion de l'envoi de message
    if (sendButton && messageInput) {
        sendButton.addEventListener('click', () => {
            const content = messageInput.value.trim();
            const files = selectedFiles.length > 0 ? selectedFiles : (fileInput?.files ? Array.from(fileInput.files) : []);
            sendMessage(content, files);
        });

        messageInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                const content = messageInput.value.trim();
                const files = selectedFiles.length > 0 ? selectedFiles : (fileInput?.files ? Array.from(fileInput.files) : []);
                sendMessage(content, files);
            }
        });

        // Indicateur de frappe
        messageInput.addEventListener('input', () => {
            sendTypingIndicator();
        });
    }

    // Gestion de l'upload de fichier
    if (fileInput && enableFileUpload) {
        fileInput.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            
            files.forEach(file => {
                const fileSizeKB = file.size / 1024;
                if (fileSizeKB > maxFileSize) {
                    alert(`Le fichier "${file.name}" est trop volumineux (max: ${maxFileSize} KB)`);
                    return;
                }
                
                if (!multipleFiles && selectedFiles.length > 0) {
                    selectedFiles = [];
                    if (filePreviews) {
                        filePreviews.innerHTML = '';
                    }
                    updateFileIndicator();
                }
                
                if (!selectedFiles.includes(file)) {
                    selectedFiles.push(file);
                    
                    if (showFilePreview && filePreviews) {
                        const emptyMessage = filePreviews.querySelector('[data-file-previews-empty]');
                        if (emptyMessage) {
                            emptyMessage.classList.add('hidden');
                        }
                        const preview = createFilePreview(file);
                        filePreviews.appendChild(preview);
                    }
                }
                
                updateFileIndicator();
            });
            
            // Mettre à jour l'input file avec les fichiers sélectionnés
            if (multipleFiles) {
                const dt = new DataTransfer();
                selectedFiles.forEach(f => dt.items.add(f));
                fileInput.files = dt.files;
            }
        });
    }
}

/**
 * Initialise le module chat-widget
 */
export function initChatWidget(root, options = {}) {
    const toggleButton = root.querySelector('[data-widget-toggle]');
    const panel = root.querySelector('[data-widget-panel]');
    const minimizeButton = root.querySelector('[data-widget-minimize]');
    const isMinimized = root.dataset.minimized === 'true';

    if (!toggleButton || !panel) return;

    function open() {
        toggleButton.classList.add('hidden');
        panel.classList.remove('hidden');
        root.dataset.minimized = 'false';
    }

    function close() {
        toggleButton.classList.remove('hidden');
        panel.classList.add('hidden');
        root.dataset.minimized = 'true';
    }

    // État initial
    if (isMinimized) {
        close();
    } else {
        open();
    }

    toggleButton.addEventListener('click', open);
    if (minimizeButton) {
        minimizeButton.addEventListener('click', close);
    }
}

/**
 * Initialise le module chat (principal)
 * Peut être appelé directement ou via le système d'auto-initialisation
 */
export default function initChat(root, options = {}) {
    const moduleName = root.dataset.module;
    
    // Si c'est un sous-module spécifique, initialiser directement
    if (moduleName === 'chat-messages') {
        initChatMessages(root, options);
        return;
    }
    
    if (moduleName === 'chat-input') {
        initChatInput(root, options);
        return;
    }
    
    if (moduleName === 'chat-widget') {
        initChatWidget(root, options);
        return;
    }
    
    // Sinon, chercher les sous-modules dans le conteneur
    const messagesContainer = root.querySelector('[data-module="chat-messages"]');
    const inputContainer = root.querySelector('[data-module="chat-input"]');
    const widgetContainer = root.querySelector('[data-module="chat-widget"]');

    if (messagesContainer) {
        initChatMessages(messagesContainer, options);
    }

    if (inputContainer) {
        initChatInput(inputContainer, options);
    }

    if (widgetContainer) {
        initChatWidget(widgetContainer, options);
    }
}

