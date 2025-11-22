# Lot 4 : Templates de communication

## Vue d'ensemble
Créer deux templates pour les fonctionnalités de communication : chat en temps réel et centre de notifications. Ces templates sont **agnostiques des modèles** : ils acceptent des données sous forme de tableaux ou d'objets génériques, permettant leur utilisation avec n'importe quel modèle (Conversation, Message, Notification, etc.) ou même sans modèle.

**Architecture modulaire** : Les templates sont décomposés en composants réutilisables permettant différentes utilisations :
- **Chat** : Peut être utilisé en page complète, en widget flottant (bubble), ou sans sidebar
- **Notifications** : Peut être utilisé en page complète, en dropdown (cloche), ou en widget flottant

## Architecture des composants

### Chat - Composants réutilisables

Les composants de chat sont organisés dans `resources/views/components/ui/communication/` :

1. **chat-sidebar.blade.php** : Liste des conversations avec recherche
2. **chat-header.blade.php** : En-tête avec info du destinataire/conversation
3. **chat-messages.blade.php** : Zone de messages scrollable
4. **chat-input.blade.php** : Zone de saisie avec actions
5. **chat-widget.blade.php** : Widget flottant (bubble) réutilisant les composants ci-dessus
6. **chat.blade.php** : Template complet assemblant tous les composants

### Notifications - Composants réutilisables

Les composants de notifications sont organisés dans `resources/views/components/ui/communication/` :

1. **notification-item.blade.php** : Un élément de notification individuel
2. **notification-list.blade.php** : Liste des notifications avec groupement par date
3. **notification-filters.blade.php** : Filtres par type et statut
4. **notification-bell.blade.php** : Widget cloche avec dropdown
5. **notification-center.blade.php** : Template complet assemblant tous les composants

## Composants de communication à créer

### Chat - Composants individuels

#### 1. chat-sidebar.blade.php
**Fichier** : `resources/views/components/ui/communication/chat-sidebar.blade.php`

**Description** : Sidebar avec liste des conversations, recherche et liste des utilisateurs en ligne.

**Props** :
```php
@props([
    'conversations' => [], // Collection de conversations
    'currentConversationId' => null,
    'showUserList' => true,
    'onlineUsers' => [],
    'conversationsUrl' => Route::has('chat.conversations') ? route('chat.conversations') : '#',
    // Data accessors
    'conversationIdKey' => 'id',
    'conversationNameKey' => 'name',
    'conversationAvatarKey' => 'avatar',
    'conversationLastMessageKey' => 'lastMessage',
    'conversationUnreadCountKey' => 'unreadCount',
    'conversationIsOnlineKey' => 'isOnline',
])
```

**Utilisation** :
```blade
<x-daisy::ui.communication.chat-sidebar
    :conversations="$conversations"
    :current-conversation-id="$currentConversationId"
/>
```

---

#### 2. chat-header.blade.php
**Fichier** : `resources/views/components/ui/communication/chat-header.blade.php`

**Description** : En-tête avec informations du destinataire/conversation (nom, avatar, statut).

**Props** :
```php
@props([
    'conversation' => null, // Conversation actuelle
    'showBackButton' => false, // Pour mobile/widget
    'backUrl' => null,
    // Data accessors
    'conversationIdKey' => 'id',
    'conversationNameKey' => 'name',
    'conversationAvatarKey' => 'avatar',
    'conversationIsOnlineKey' => 'isOnline',
])
```

**Utilisation** :
```blade
<x-daisy::ui.communication.chat-header :conversation="$conversation" />
```

---

#### 3. chat-messages.blade.php
**Fichier** : `resources/views/components/ui/communication/chat-messages.blade.php`

**Description** : Zone de messages scrollable avec indicateur de frappe.

**Props** :
```php
@props([
    'messages' => [],
    'currentUserId' => null,
    'showTypingIndicator' => true,
    'typingUsers' => [], // Liste des utilisateurs en train d'écrire
    'loadMessagesUrl' => Route::has('chat.messages') ? route('chat.messages') : '#',
    // Options REST/WebSocket
    'useWebSockets' => false,
    'pollingInterval' => 3000,
    'autoReconnect' => true,
    'reconnectDelay' => 5000,
    // Data accessors
    'messageIdKey' => 'id',
    'messageUserIdKey' => 'user_id',
    'messageContentKey' => 'content',
    'messageCreatedAtKey' => 'created_at',
    'messageUserNameKey' => 'user_name',
    'messageUserAvatarKey' => 'user_avatar',
])
```

**Utilisation** :
```blade
<x-daisy::ui.communication.chat-messages
    :messages="$messages"
    :current-user-id="auth()->id()"
/>
```

---

#### 4. chat-input.blade.php
**Fichier** : `resources/views/components/ui/communication/chat-input.blade.php`

**Description** : Zone de saisie avec boutons d'action (envoyer, joindre fichier).

**Props** :
```php
@props([
    'sendMessageUrl' => Route::has('chat.send') ? route('chat.send') : '#',
    'typingUrl' => Route::has('chat.typing') ? route('chat.typing') : null,
    'enableFileUpload' => false,
    'maxFileSize' => 5120, // KB
    'placeholder' => __('chat.type_message'),
    // Options REST/WebSocket
    'useWebSockets' => false,
    'autoReconnect' => true,
])
```

**Utilisation** :
```blade
<x-daisy::ui.communication.chat-input
    :send-message-url="route('chat.send')"
    :enable-file-upload="true"
/>
```

---

#### 5. chat-widget.blade.php
**Fichier** : `resources/views/components/ui/communication/chat-widget.blade.php`

**Description** : Widget flottant (bubble) pour intégrer le chat sur n'importe quelle page.

**Props** :
```php
@props([
    'conversation' => null,
    'messages' => [],
    'currentUserId' => null,
    'position' => 'bottom-right', // bottom-right, bottom-left, top-right, top-left
    'minimized' => false, // État initial (minimisé ou ouvert)
    'showHeader' => true,
    'showInput' => true,
    // Toutes les props des composants enfants (chat-header, chat-messages, chat-input)
    // + Data accessors
    // + Routes
    // + Options
])
```

**Utilisation** :
```blade
<x-daisy::ui.communication.chat-widget
    :conversation="$conversation"
    :messages="$messages"
    position="bottom-right"
/>
```

**Fonctionnalités** :
- Bouton flottant qui ouvre/ferme le widget
- Minimisation/maximisation
- Position configurable
- Réutilise `chat-header`, `chat-messages`, `chat-input`

---

#### 6. chat.blade.php (Template complet)
**Fichier** : `resources/views/templates/chat.blade.php`

**Description** : Template complet de chat assemblant tous les composants (sidebar + zone principale).

**Props** :
```php
@props([
    'title' => __('chat.messages'),
    'theme' => null,
    'conversation' => null,
    'conversations' => [], // Pour la sidebar
    'messages' => [],
    'currentUser' => null,
    'currentUserId' => null,
    'showSidebar' => true,
    'showUserList' => true,
    // Toutes les props des composants enfants
    // + Data accessors
    // + Routes
    // + Options (pollingInterval, useWebSockets, etc.)
])
```

**Structure** :
- Layout en 2 colonnes (si `showSidebar`) :
  - Sidebar gauche : `<x-daisy::ui.communication.chat-sidebar />`
  - Zone principale :
    - `<x-daisy::ui.communication.chat-header />`
    - `<x-daisy::ui.communication.chat-messages />`
    - `<x-daisy::ui.communication.chat-input />`
- Layout 1 colonne (si `!showSidebar`) :
  - Zone principale uniquement

**Utilisation** :
```blade
{{-- Page complète avec sidebar --}}
<x-daisy::templates.chat
    :conversation="$conversation"
    :conversations="$conversations"
    :messages="$messages"
/>

{{-- Page sans sidebar (conversation directe) --}}
<x-daisy::templates.chat
    :conversation="$conversation"
    :messages="$messages"
    :show-sidebar="false"
/>
```

---

### Notifications - Composants individuels

#### 1. notification-item.blade.php
**Fichier** : `resources/views/components/ui/communication/notification-item.blade.php`

**Description** : Un élément de notification individuel avec avatar, contenu, actions.

**Props** :
```php
@props([
    'notification' => null,
    'showActions' => true,
    'markAsReadUrl' => Route::has('notifications.read') ? route('notifications.read') : '#',
    'deleteUrl' => Route::has('notifications.delete') ? route('notifications.delete') : '#',
    // Data accessors
    'notificationIdKey' => 'id',
    'notificationTypeKey' => 'type',
    'notificationDataKey' => 'data',
    'notificationReadAtKey' => 'read_at',
    'notificationCreatedAtKey' => 'created_at',
])
```

**Utilisation** :
```blade
<x-daisy::ui.communication.notification-item
    :notification="$notification"
/>
```

---

#### 2. notification-list.blade.php
**Fichier** : `resources/views/components/ui/communication/notification-list.blade.php`

**Description** : Liste des notifications avec groupement par date optionnel.

**Props** :
```php
@props([
    'notifications' => [],
    'groupByDate' => true,
    'showActions' => true,
    // Data accessors
    // Routes
])
```

**Utilisation** :
```blade
<x-daisy::ui.communication.notification-list
    :notifications="$notifications"
    :group-by-date="true"
/>
```

---

#### 3. notification-filters.blade.php
**Fichier** : `resources/views/components/ui/communication/notification-filters.blade.php`

**Description** : Filtres par type et statut (toutes, non lues, par type).

**Props** :
```php
@props([
    'types' => [], // Types de notifications disponibles
    'currentFilter' => 'all', // all, unread, or specific type
    'onFilterChange' => null, // Callback JS (optionnel)
])
```

**Utilisation** :
```blade
<x-daisy::ui.communication.notification-filters
    :types="['comment', 'like', 'mention']"
    current-filter="unread"
/>
```

---

#### 4. notification-bell.blade.php
**Fichier** : `resources/views/components/ui/communication/notification-bell.blade.php`

**Description** : Widget cloche avec badge de compteur et dropdown de notifications.

**Props** :
```php
@props([
    'notifications' => [],
    'unreadCount' => null,
    'position' => 'dropdown-end', // Position du dropdown
    'showMarkAllRead' => true,
    'markAllAsReadUrl' => Route::has('notifications.read-all') ? route('notifications.read-all') : '#',
    'viewAllUrl' => Route::has('notifications.index') ? route('notifications.index') : '#',
    // Toutes les props de notification-list
])
```

**Utilisation** :
```blade
<x-daisy::ui.communication.notification-bell
    :notifications="$notifications"
    :unread-count="$unreadCount"
/>
```

**Fonctionnalités** :
- Badge avec compteur de non lues
- Dropdown avec liste des notifications récentes
- Bouton "Tout marquer comme lu"
- Lien "Voir toutes les notifications"
- Réutilise `notification-list` et `notification-item`

---

#### 5. notification-center.blade.php (Template complet)
**Fichier** : `resources/views/templates/notification-center.blade.php`

**Description** : Template complet de centre de notifications assemblant tous les composants.

**Props** :
```php
@props([
    'title' => __('notifications.notifications'),
    'theme' => null,
    'notifications' => [],
    'unreadCount' => null,
    'showFilters' => true,
    'showMarkAllRead' => true,
    'showDelete' => true,
    'groupByDate' => true,
    'pagination' => true,
    // Toutes les props des composants enfants
    // + Data accessors
    // + Routes
    // + Options (pollingInterval, useWebSockets, etc.)
])
```

**Structure** :
- En-tête avec titre, compteur, bouton "Tout marquer comme lu"
- `<x-daisy::ui.communication.notification-filters />` (si `showFilters`)
- `<x-daisy::ui.communication.notification-list />`
- Pagination (si `pagination`)

**Utilisation** :
```blade
<x-daisy::templates.notification-center
    :notifications="$notifications"
    :unread-count="$unreadCount"
/>
```

---

## Templates à créer (assemblages finaux)

## Props communes pour REST et Reverb

Tous les composants de communication partagent ces props pour la configuration REST et WebSocket :

### Props de communication

```php
@props([
    // Routes REST (requises)
    'sendMessageUrl' => Route::has('chat.send') ? route('chat.send') : '#',
    'loadMessagesUrl' => Route::has('chat.messages') ? route('chat.messages') : '#',
    'conversationsUrl' => Route::has('chat.conversations') ? route('chat.conversations') : '#',
    
    // Options de communication
    'useWebSockets' => false, // Activer WebSocket (nécessite Reverb configuré)
    'pollingInterval' => 3000, // Intervalle de polling REST en ms (si useWebSockets = false)
    'autoReconnect' => true, // Reconnexion automatique en cas de déconnexion
    'reconnectDelay' => 5000, // Délai avant reconnexion en ms
    
    // Options Reverb (détection automatique si non fournies)
    'reverbAppKey' => null, // Clé Reverb (auto-détectée depuis window.Echo si null)
    'reverbHost' => null, // Host Reverb (auto-détectée depuis window.Echo si null)
    'reverbPort' => null, // Port Reverb (auto-détectée depuis window.Echo si null)
    'reverbScheme' => null, // Schéma Reverb (auto-détectée depuis window.Echo si null)
])
```

### Comportement automatique

1. **Détection Reverb** : Si `useWebSockets = true`, le JavaScript vérifie automatiquement :
   - Présence de `window.Echo`
   - Configuration Reverb disponible
   - Si Reverb n'est pas disponible, fallback automatique vers REST

2. **Polling REST** : Si `useWebSockets = false` ou si Reverb n'est pas disponible :
   - Utilisation du polling REST à l'intervalle défini par `pollingInterval`
   - Toutes les actions utilisent des requêtes REST standard

3. **Reconnexion** : En cas de déconnexion WebSocket :
   - Tentative de reconnexion automatique si `autoReconnect = true`
   - Délai de `reconnectDelay` ms entre les tentatives
   - Fallback vers REST si la reconnexion échoue

---

## Exemples d'utilisation

### Chat - Cas d'usage

#### 1. Page complète avec sidebar
```blade
<x-daisy::templates.chat
    :conversation="$conversation"
    :conversations="$conversations"
    :messages="$messages"
    :show-sidebar="true"
/>
```

#### 2. Page sans sidebar (conversation directe)
```blade
<x-daisy::templates.chat
    :conversation="$conversation"
    :messages="$messages"
    :show-sidebar="false"
/>
```

#### 3. Widget flottant (bubble) sur une page
```blade
{{-- Dans le layout ou n'importe quelle page --}}
<x-daisy::ui.communication.chat-widget
    :conversation="$conversation"
    :messages="$messages"
    position="bottom-right"
/>
```

#### 4. Utilisation des composants individuellement
```blade
{{-- Juste la zone de messages --}}
<x-daisy::ui.communication.chat-messages
    :messages="$messages"
    :current-user-id="auth()->id()"
/>

{{-- Juste l'input --}}
<x-daisy::ui.communication.chat-input
    :send-message-url="route('chat.send')"
/>
```

#### 5. Configuration REST (par défaut)
```blade
{{-- Utilisation REST avec polling --}}
<x-daisy::templates.chat
    :conversation="$conversation"
    :messages="$messages"
    :use-websockets="false"
    :polling-interval="5000"
/>
```

#### 6. Configuration Reverb (WebSocket)
```blade
{{-- Utilisation WebSocket avec Reverb (si configuré) --}}
<x-daisy::templates.chat
    :conversation="$conversation"
    :messages="$messages"
    :use-websockets="true"
    :auto-reconnect="true"
    :reconnect-delay="5000"
/>
```

### Notifications - Cas d'usage

#### 1. Page complète de notifications
```blade
<x-daisy::templates.notification-center
    :notifications="$notifications"
    :unread-count="$unreadCount"
/>
```

#### 2. Cloche dans la navbar
```blade
{{-- Dans la navbar --}}
<x-daisy::ui.communication.notification-bell
    :notifications="$recentNotifications"
    :unread-count="$unreadCount"
/>
```

#### 3. Liste de notifications dans un dropdown personnalisé
```blade
<div class="dropdown">
    <div tabindex="0" role="button" class="btn">Notifications</div>
    <ul tabindex="-1" class="dropdown-content">
        <x-daisy::ui.communication.notification-list
            :notifications="$notifications"
            :group-by-date="false"
        />
    </ul>
</div>
```

#### 4. Utilisation des composants individuellement
```blade
{{-- Juste un élément de notification --}}
<x-daisy::ui.communication.notification-item
    :notification="$notification"
/>

{{-- Juste les filtres --}}
<x-daisy::ui.communication.notification-filters
    :types="['comment', 'like']"
/>
```

#### 5. Configuration REST (par défaut)
```blade
{{-- Utilisation REST avec polling --}}
<x-daisy::templates.notification-center
    :notifications="$notifications"
    :unread-count="$unreadCount"
    :use-websockets="false"
    :polling-interval="30000"
/>
```

#### 6. Configuration Reverb (WebSocket)
```blade
{{-- Utilisation WebSocket avec Reverb (si configuré) --}}
<x-daisy::templates.notification-center
    :notifications="$notifications"
    :unread-count="$unreadCount"
    :use-websockets="true"
    :auto-reconnect="true"
    :reconnect-delay="5000"
/>
```

---

## Structure de données (format agnostique)

### Chat

```php
// Format tableau (agnostique)
$conversation = [
    'id' => 1,
    'name' => 'John Doe',
    'avatar' => '/img/people/people-1.jpg',
    'lastMessage' => 'Salut !',
    'lastMessageAt' => now()->subMinutes(5),
    'unreadCount' => 2,
    'isOnline' => true,
];

$messages = [
    [
        'id' => 1,
        'user_id' => 1,
        'user_name' => 'John Doe',
        'user_avatar' => '/img/people/people-1.jpg',
        'content' => 'Salut !',
        'created_at' => now()->subMinutes(10),
    ],
    [
        'id' => 2,
        'user_id' => auth()->id(),
        'user_name' => 'Jane Doe',
        'user_avatar' => '/img/people/people-2.jpg',
        'content' => 'Bonjour !',
        'created_at' => now()->subMinutes(5),
    ],
];

// Utilisation avec un modèle Eloquent
$conversation = Conversation::with('participants')->find(1);
$messages = Message::where('conversation_id', 1)->with('user')->latest()->get();
```

### Notifications

```php
// Format tableau (agnostique)
$notifications = [
    [
        'id' => 1,
        'type' => 'App\Notifications\NewComment', // ou simplement 'comment'
        'data' => [
            'message' => 'John a commenté votre post',
            'link' => '/posts/123',
            'user' => ['name' => 'John Doe', 'avatar' => '/img/people/people-1.jpg'],
        ],
        'read_at' => null, // null = non lue
        'created_at' => now()->subMinutes(5),
    ],
];

// Utilisation avec Laravel Notifications
$notifications = auth()->user()->notifications()->paginate(20);
```

---

## Fonctionnalités Laravel communes

Tous les composants partagent ces fonctionnalités :

- **Agnostique des modèles** : Acceptent des tableaux, objets ou modèles Eloquent
- Utilisent `data_get()` pour accéder aux données de manière agnostique
- Utilisent `Route::has()` et `route()` pour les URLs
- Les dates peuvent être formatées avec `Carbon` si disponibles
- **Support REST** : Toutes les actions utilisent des routes RESTful (GET, POST, PUT, DELETE)
- **Support WebSocket** : Support optionnel via Laravel Echo + Laravel Reverb (configuré par le projet hôte)
- **Fallback polling** : Fonctionnement sans WebSocket avec polling AJAX via REST
- Utilisent `Storage` pour les fichiers uploadés (chat uniquement)

---

## Composants UI de base nécessaires

### 1. empty-state.blade.php (à créer)
**Fichier** : `resources/views/components/ui/feedback/empty-state.blade.php`

**Description** : Composant pour afficher un état vide (aucune donnée) avec icône, message et action optionnelle.

**Props** :
```php
@props([
    'icon' => 'inbox', // Blade icon name
    'title' => __('common.empty'),
    'message' => null,
    'actionLabel' => null,
    'actionUrl' => null,
    'size' => 'md', // sm, md, lg
])
```

**Utilisation** :
```blade
<x-daisy::ui.feedback.empty-state
    icon="inbox"
    title="Aucune notification"
    message="Vous n'avez aucune notification pour le moment."
    actionLabel="Voir les activités"
    actionUrl="{{ route('activities') }}"
/>
```

---

### Composants UI existants utilisés

Tous les composants suivants existent déjà et seront utilisés par les composants de communication :

- `x-daisy::ui.communication.chat-bubble` (messages)
- `x-daisy::ui.data-display.avatar` (avatars)
- `x-daisy::ui.data-display.badge` (badges)
- `x-daisy::ui.inputs.textarea` (zone de saisie)
- `x-daisy::ui.inputs.button` (boutons)
- `x-daisy::ui.inputs.file-input` (upload)
- `x-daisy::ui.feedback.loading` (chargement)
- `x-daisy::ui.navigation.menu` (menus)
- `x-daisy::ui.feedback.toast` (notifications)
- `x-daisy::ui.layout.list` et `x-daisy::ui.layout.list-row` (listes)
- `x-daisy::ui.advanced.filter` (filtres)
- `x-daisy::ui.navigation.pagination` (pagination)

---

## JavaScript nécessaire

### Modules JS à créer

#### 1. chat.js
**Fichier** : `resources/js/modules/chat.js`

**Fonctionnalités** :
- **REST API** : Toutes les actions utilisent des requêtes REST (GET, POST, PUT, DELETE)
- **Polling REST** : Polling des messages via REST (si `useWebSockets` est false)
- **WebSocket (optionnel)** : Connexion WebSocket via Laravel Echo + Laravel Reverb (si `useWebSockets` est true)
  - Détection automatique de la configuration Reverb
  - Fallback automatique vers REST si Reverb non disponible
- Envoi de messages (AJAX REST)
- Indicateur de frappe
- Scroll automatique vers le bas
- Upload de fichiers via REST (si activé)
- Gestion du widget (minimiser/maximiser)

**Utilisé par** :
- `chat.blade.php` (template complet)
- `chat-widget.blade.php` (widget flottant)
- `chat-messages.blade.php` (zone de messages)
- `chat-input.blade.php` (zone de saisie)

#### 2. notifications.js
**Fichier** : `resources/js/modules/notifications.js`

**Fonctionnalités** :
- **REST API** : Toutes les actions utilisent des requêtes REST (GET, POST, PUT, DELETE)
- **Polling REST** : Polling des nouvelles notifications via REST (si `useWebSockets` est false)
- **WebSocket (optionnel)** : Connexion WebSocket via Laravel Echo + Laravel Reverb (si `useWebSockets` est true)
  - Détection automatique de la configuration Reverb
  - Fallback automatique vers REST si Reverb non disponible
- Actions REST (marquer comme lu, supprimer)
- Filtrage côté client
- Pagination infinie (scroll to load more)
- Toasts de confirmation
- Gestion du dropdown (cloche)

**Utilisé par** :
- `notification-center.blade.php` (template complet)
- `notification-bell.blade.php` (widget cloche)
- `notification-list.blade.php` (liste)
- `notification-item.blade.php` (actions)

### Initialisation JavaScript

Les composants utilisent le système `data-module` pour l'initialisation automatique :

**Exemple pour chat-messages** :
```blade
<div 
    data-module="chat-messages"
    data-load-messages-url="{{ $loadMessagesUrl }}"
    data-current-user-id="{{ $currentUserId }}"
    data-use-websockets="{{ $useWebSockets ? 'true' : 'false' }}"
    data-polling-interval="{{ $pollingInterval }}"
>
    <!-- Contenu -->
</div>
```

Le module JavaScript `chat.js` sera automatiquement initialisé via le système `data-module` du package.

**Options passées via data-attributes** :
- Toutes les props de communication sont converties en `data-*` attributes (kebab-case)
- Les valeurs booléennes sont converties en `'true'`/`'false'` (strings)
- Les objets/arrays sont JSON-stringifiés si nécessaire

### Gestion des erreurs

Les modules JavaScript gèrent les erreurs de manière élégante :

1. **Erreurs réseau** :
   - Tentative de reconnexion automatique (si `autoReconnect = true`)
   - Affichage d'un toast d'erreur à l'utilisateur
   - Fallback vers REST si WebSocket échoue

2. **Erreurs de validation** :
   - Affichage des erreurs de validation dans le formulaire
   - Messages d'erreur traduits

3. **Erreurs d'autorisation** :
   - Redirection vers la page de connexion si 401
   - Affichage d'un message d'erreur si 403

4. **Erreurs serveur** :
   - Affichage d'un message d'erreur générique
   - Log des erreurs en console (mode développement)

---

## Configuration Laravel Reverb (projet hôte)

> **Important** : Laravel Reverb doit être installé et configuré par le **projet hôte**, pas par ce package. Cette section documente la configuration nécessaire pour que les composants de communication fonctionnent avec Reverb.

### Installation Reverb dans le projet hôte

Le projet hôte doit installer Reverb via la commande Artisan :

```bash
php artisan install:broadcasting
```

Cette commande installe Reverb avec une configuration par défaut. Référence : [Laravel Reverb Documentation](https://laravel.com/docs/12.x/reverb)

### Configuration requise

#### Variables d'environnement

Le projet hôte doit configurer les variables suivantes dans son `.env` :

```env
REVERB_APP_ID=my-app-id
REVERB_APP_KEY=my-app-key
REVERB_APP_SECRET=my-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
```

Pour la production avec SSL :

```env
REVERB_SCHEME=https
REVERB_PORT=443
```

#### Configuration Laravel Echo (frontend)

Le projet hôte doit configurer Laravel Echo dans son JavaScript. Exemple avec Reverb :

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
```

#### Variables Vite

Le projet hôte doit exposer les variables Reverb dans son `vite.config.js` :

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    define: {
        'import.meta.env.VITE_REVERB_APP_KEY': JSON.stringify(process.env.VITE_REVERB_APP_KEY),
        'import.meta.env.VITE_REVERB_HOST': JSON.stringify(process.env.VITE_REVERB_HOST),
        'import.meta.env.VITE_REVERB_PORT': JSON.stringify(process.env.VITE_REVERB_PORT),
        'import.meta.env.VITE_REVERB_SCHEME': JSON.stringify(process.env.VITE_REVERB_SCHEME),
    },
});
```

Et dans le `.env` du projet hôte :

```env
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### Détection automatique dans les composants

Les modules JavaScript (`chat.js` et `notifications.js`) détectent automatiquement si Reverb est disponible :

1. **Vérification de la présence de `window.Echo`** : Si Laravel Echo est configuré
2. **Vérification de la configuration Reverb** : Si les variables d'environnement Reverb sont présentes
3. **Fallback automatique** : Si Reverb n'est pas disponible, utilisation du polling REST

### Utilisation dans les composants

Les composants acceptent une prop `useWebSockets` pour activer/désactiver WebSocket :

```blade
{{-- Avec WebSocket (si Reverb configuré) --}}
<x-daisy::templates.chat
    :conversation="$conversation"
    :messages="$messages"
    :use-websockets="true"
/>

{{-- Sans WebSocket (REST uniquement) --}}
<x-daisy::templates.chat
    :conversation="$conversation"
    :messages="$messages"
    :use-websockets="false"
/>
```

### Routes Broadcasting requises

Le projet hôte doit définir les routes de broadcasting dans `routes/channels.php` :

```php
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    // Vérifier que l'utilisateur peut accéder à cette conversation
    return true; // ou votre logique d'autorisation
});

Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
```

### Events Broadcasting

Le projet hôte doit créer des Events qui implémentent `ShouldBroadcast` :

```php
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageSent implements ShouldBroadcast
{
    public function broadcastOn(): array
    {
        return [
            new Channel('chat.' . $this->conversationId),
        ];
    }
}
```

### Démarrage du serveur Reverb

Le projet hôte doit démarrer le serveur Reverb :

```bash
php artisan reverb:start
```

Pour la production, utiliser un gestionnaire de processus comme Supervisor. Référence : [Laravel Reverb - Running Reverb in Production](https://laravel.com/docs/12.x/reverb#running-reverb-in-production)

### Documentation complète

Pour plus de détails sur la configuration Reverb, consulter la [documentation officielle Laravel Reverb](https://laravel.com/docs/12.x/reverb).

---

## Routes REST requises (projet hôte)

> **Important** : Le projet hôte doit définir ces routes REST dans `routes/api.php` ou `routes/web.php`. Les composants JavaScript utilisent ces routes pour toutes les actions (polling, envoi, etc.).

### Routes Chat

#### GET `/chat/conversations`
Récupère la liste des conversations de l'utilisateur connecté.

**Réponse attendue** :
```json
{
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "avatar": "/img/people/people-1.jpg",
            "lastMessage": "Salut !",
            "lastMessageAt": "2024-01-15T10:30:00Z",
            "unreadCount": 2,
            "isOnline": true
        }
    ]
}
```

#### GET `/chat/messages/{conversationId}`
Récupère les messages d'une conversation.

**Paramètres** :
- `conversationId` : ID de la conversation
- Query params optionnels : `page`, `per_page`, `since` (timestamp)

**Réponse attendue** :
```json
{
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "user_name": "John Doe",
            "user_avatar": "/img/people/people-1.jpg",
            "content": "Salut !",
            "created_at": "2024-01-15T10:30:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 20,
        "total": 50
    }
}
```

#### POST `/chat/send`
Envoie un nouveau message.

**Body** :
```json
{
    "conversation_id": 1,
    "content": "Message texte",
    "file": null // Optionnel : fichier uploadé
}
```

**Réponse attendue** :
```json
{
    "data": {
        "id": 123,
        "user_id": 1,
        "user_name": "Jane Doe",
        "user_avatar": "/img/people/people-2.jpg",
        "content": "Message texte",
        "created_at": "2024-01-15T10:35:00Z"
    }
}
```

#### POST `/chat/typing`
Indique que l'utilisateur est en train d'écrire.

**Body** :
```json
{
    "conversation_id": 1
}
```

**Réponse attendue** :
```json
{
    "status": "ok"
}
```

### Routes Notifications

#### GET `/notifications`
Récupère la liste des notifications de l'utilisateur connecté.

**Query params optionnels** :
- `filter` : `all`, `unread`, `read`, ou type spécifique
- `page` : Numéro de page
- `per_page` : Nombre d'éléments par page

**Réponse attendue** :
```json
{
    "data": [
        {
            "id": 1,
            "type": "App\\Notifications\\NewComment",
            "data": {
                "message": "John a commenté votre post",
                "link": "/posts/123",
                "user": {
                    "name": "John Doe",
                    "avatar": "/img/people/people-1.jpg"
                }
            },
            "read_at": null,
            "created_at": "2024-01-15T10:30:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 20,
        "total": 50,
        "unread_count": 5
    }
}
```

#### POST `/notifications/{id}/read`
Marque une notification comme lue.

**Réponse attendue** :
```json
{
    "data": {
        "id": 1,
        "read_at": "2024-01-15T10:35:00Z"
    }
}
```

#### POST `/notifications/read-all`
Marque toutes les notifications comme lues.

**Réponse attendue** :
```json
{
    "status": "ok",
    "count": 5
}
```

#### DELETE `/notifications/{id}`
Supprime une notification.

**Réponse attendue** :
```json
{
    "status": "ok"
}
```

### Gestion des erreurs

Toutes les routes doivent retourner des erreurs au format standard Laravel :

**Erreur 422 (Validation)** :
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "content": ["The content field is required."]
    }
}
```

**Erreur 404 (Not Found)** :
```json
{
    "message": "Conversation not found."
}
```

**Erreur 403 (Forbidden)** :
```json
{
    "message": "You don't have access to this conversation."
}
```

### Authentification

Les routes doivent être protégées par l'authentification Laravel (middleware `auth`). Le projet hôte doit s'assurer que :
- Les utilisateurs sont authentifiés pour accéder aux routes
- Les utilisateurs ne peuvent accéder qu'à leurs propres conversations/notifications
- Les autorisations sont vérifiées (policies, gates, etc.)

---

## Traductions nécessaires

### chat.php
**Fichier** : `resources/lang/fr/chat.php` et `resources/lang/en/chat.php`

```php
return [
    'messages' => 'Messages',
    'conversations' => 'Conversations',
    'new_message' => 'Nouveau message',
    'type_message' => 'Tapez votre message...',
    'send' => 'Envoyer',
    'online' => 'En ligne',
    'offline' => 'Hors ligne',
    'typing' => ':name est en train d\'écrire...',
    'no_conversations' => 'Aucune conversation',
    'no_messages' => 'Aucun message',
    'select_conversation' => 'Sélectionnez une conversation',
    'file_upload' => 'Joindre un fichier',
    'file_too_large' => 'Le fichier est trop volumineux (max : :size KB)',
    'minimize' => 'Réduire',
    'maximize' => 'Agrandir',
    'close' => 'Fermer',
];
```

### notifications.php
**Fichier** : `resources/lang/fr/notifications.php` et `resources/lang/en/notifications.php`

```php
return [
    'notifications' => 'Notifications',
    'all' => 'Toutes',
    'unread' => 'Non lues',
    'read' => 'Lues',
    'mark_as_read' => 'Marquer comme lu',
    'mark_all_as_read' => 'Tout marquer comme lu',
    'delete' => 'Supprimer',
    'no_notifications' => 'Aucune notification',
    'no_unread_notifications' => 'Aucune notification non lue',
    'today' => 'Aujourd\'hui',
    'yesterday' => 'Hier',
    'this_week' => 'Cette semaine',
    'older' => 'Plus ancien',
    'new_notification' => 'Nouvelle notification',
    'notification_deleted' => 'Notification supprimée',
    'all_marked_as_read' => 'Toutes les notifications ont été marquées comme lues',
    'view_all' => 'Voir toutes les notifications',
];
```

---

## Tests à prévoir

### Tests de rendu (Feature)
Pour chaque composant :
1. **Test de rendu par défaut** : Vérifier le rendu avec les props par défaut
2. **Test avec données** : Vérifier l'affichage des messages/notifications
3. **Test d'état vide** : Vérifier l'affichage quand il n'y a pas de données
4. **Test responsive** : Vérifier l'affichage sur mobile

### Tests d'interactions (Browser)
Pour les composants avec JavaScript :
1. **Test d'actions REST** : Vérifier les actions via REST (envoyer message, marquer comme lu, etc.)
2. **Test de pagination** : Vérifier la pagination (notifications)
3. **Test de filtres** : Vérifier les filtres (notifications)
4. **Test polling REST** : Vérifier le polling des messages/notifications via REST
5. **Test WebSocket/Reverb** : Vérifier la réception en temps réel (si Reverb configuré)
6. **Test fallback REST** : Vérifier le fallback automatique vers REST si Reverb non disponible
7. **Test reconnexion** : Vérifier la reconnexion automatique en cas de déconnexion
8. **Test widget chat** : Vérifier minimiser/maximiser
9. **Test cloche notifications** : Vérifier ouverture/fermeture dropdown
10. **Test gestion d'erreurs** : Vérifier l'affichage des erreurs (réseau, validation, autorisation)

---

## Ordre d'implémentation recommandé

### Phase 1 : Composants de base
1. Créer `empty-state.blade.php` (utilisé partout)
2. Créer les traductions (`chat.php`, `notifications.php`)

### Phase 2 : Composants de notifications (plus simples)
3. `notification-item.blade.php`
4. `notification-list.blade.php`
5. `notification-filters.blade.php`
6. `notification-bell.blade.php`
7. `notification-center.blade.php` (template complet)
8. Module JS `notifications.js`

### Phase 3 : Composants de chat (plus complexes)
9. `chat-header.blade.php`
10. `chat-messages.blade.php`
11. `chat-input.blade.php`
12. `chat-sidebar.blade.php`
13. `chat-widget.blade.php`
14. `chat.blade.php` (template complet)
15. Module JS `chat.js`

### Phase 4 : Tests
16. Tests de rendu pour tous les composants
17. Tests d'interactions (browser) pour les composants avec JS

---

## Notes importantes

### Architecture modulaire
- **Composants réutilisables** : Chaque composant peut être utilisé indépendamment
- **Templates d'assemblage** : Les templates (`chat.blade.php`, `notification-center.blade.php`) assemblent les composants
- **Flexibilité d'utilisation** : Possibilité d'utiliser les composants dans différents contextes :
  - Chat en page complète, widget flottant, ou sans sidebar
  - Notifications en page complète, dropdown cloche, ou widget flottant

### Agnosticité des modèles
- **Format agnostique** : Tous les composants acceptent des tableaux, objets ou modèles Eloquent
- **Accesseurs génériques** : Utilisation de `data_get()` pour accéder aux données
- **Pas de dépendance modèle** : Les composants peuvent fonctionner sans modèle (tableaux de données)
- **Compatibilité** : Fonctionnent avec n'importe quel modèle (Conversation, Message, Notification, etc.)

### Technologies et dépendances

#### Support REST (par défaut)
- **REST API** : Toutes les actions utilisent des routes RESTful standard
- **Polling REST** : Polling AJAX via REST pour les mises à jour en temps réel (fallback)
- **Routes API** : Doivent être définies dans `routes/api.php` ou `routes/web.php`
- **Pas de dépendance externe** : Fonctionne sans configuration supplémentaire

#### Support Laravel Reverb (optionnel)
- **Laravel Reverb** : Support optionnel pour WebSocket en temps réel via [Laravel Reverb](https://laravel.com/docs/12.x/reverb)
- **Configuration par le projet hôte** : Reverb doit être installé et configuré par le projet hôte, pas par ce package
- **Détection automatique** : Le JavaScript détecte automatiquement si Reverb est disponible
- **Fallback automatique** : Si Reverb n'est pas disponible, fallback automatique vers REST
- **Laravel Echo** : Utilise Laravel Echo pour la connexion WebSocket (doit être installé par le projet hôte)
- **Pas d'installation requise** : Ce package ne nécessite pas Reverb pour fonctionner

#### Autres dépendances
- **Laravel Notifications** : Compatible avec le système de notifications Laravel (`database` driver)
- **Storage** : Utilise `Storage` pour les fichiers uploadés (chat uniquement)

### Formatage et localisation
- **Dates** : Formatées avec `Carbon` si disponibles, sinon affichées telles quelles
- **Traductions** : Doivent être ajoutées dans `resources/lang/fr/` et `resources/lang/en/`
- **Routes** : Utilisation de `Route::has()` et `route()` pour les URLs

### Exemples d'utilisation

**Chat avec modèle** :
```blade
<x-daisy::templates.chat :conversation="$conversation" :messages="$messages" />
```

**Chat sans modèle** :
```blade
<x-daisy::templates.chat 
    :conversation="['id' => 1, 'name' => 'John']" 
    :messages="[...]" 
/>
```

**Widget chat flottant** :
```blade
<x-daisy::ui.communication.chat-widget
    :conversation="$conversation"
    :messages="$messages"
    position="bottom-right"
/>
```

**Cloche notifications dans navbar** :
```blade
<x-daisy::ui.communication.notification-bell
    :notifications="$recentNotifications"
    :unread-count="$unreadCount"
/>
```

### Organisation des fichiers

**Composants de communication** :
- `resources/views/components/ui/communication/` : Tous les composants réutilisables

**Templates** :
- `resources/views/templates/chat.blade.php` : Template complet de chat
- `resources/views/templates/notification-center.blade.php` : Template complet de notifications

**JavaScript** :
- `resources/js/modules/chat.js` : Module JS pour le chat
- `resources/js/modules/notifications.js` : Module JS pour les notifications

**Traductions** :
- `resources/lang/fr/chat.php` et `resources/lang/en/chat.php`
- `resources/lang/fr/notifications.php` et `resources/lang/en/notifications.php`

