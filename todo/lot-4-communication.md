# Lot 4 : Templates de communication

## Vue d'ensemble
Créer deux templates pour les fonctionnalités de communication : chat en temps réel et centre de notifications. Ces templates utilisent les fonctionnalités Laravel (Broadcasting, Events, Notifications, etc.).

## Templates à créer

### 1. chat.blade.php
**Fichier** : `resources/views/templates/chat.blade.php`

**Description** : Interface de chat en temps réel avec sidebar des conversations, zone de messages et input de saisie.

**Props** :
```php
@props([
    'title' => __('chat.messages'),
    'theme' => null,
    // Current conversation
    'conversation' => null, // Conversation model or array
    'messages' => [], // Collection of messages
    'currentUser' => auth()->user(),
    // Routes
    'sendMessageUrl' => Route::has('chat.send') ? route('chat.send') : '#',
    'loadMessagesUrl' => Route::has('chat.messages') ? route('chat.messages') : '#',
    'conversationsUrl' => Route::has('chat.conversations') ? route('chat.conversations') : '#',
    // Options
    'showSidebar' => true,
    'showUserList' => true,
    'showTypingIndicator' => true,
    'enableFileUpload' => false,
    'maxFileSize' => 5120, // KB
    'pollingInterval' => 3000, // ms (if not using WebSockets)
    'useWebSockets' => false, // Use Laravel Echo + Pusher/Ably
])
```

**Fonctionnalités Laravel** :
- Utilise `auth()->user()` pour l'utilisateur actuel
- Utilise Laravel Broadcasting (Echo) pour les messages en temps réel
- Utilise les Events Laravel pour les nouveaux messages
- Utilise les routes API pour charger les messages
- Utilise `Route::has()` et `route()` pour les URLs
- Peut utiliser des relations Eloquent (`conversation->messages`, `message->user`)
- Utilise `Carbon` pour formater les dates
- Utilise `Storage` pour les fichiers uploadés (si activé)

**Composants UI utilisés** :
- `x-daisy::layout.app` ou layout sans sidebar (le chat a sa propre sidebar)
- `x-daisy::ui.advanced.chat-bubble` (messages)
- `x-daisy::ui.data-display.avatar` (avatars des utilisateurs)
- `x-daisy::ui.data-display.badge` (badge "non lu", statut en ligne)
- `x-daisy::ui.inputs.textarea` (zone de saisie)
- `x-daisy::ui.inputs.button` (envoyer, joindre fichier)
- `x-daisy::ui.inputs.file-input` (upload de fichiers)
- `x-daisy::ui.feedback.loading` (indicateur de chargement)
- `x-daisy::ui.navigation.menu` (liste des conversations)
- `x-daisy::ui.feedback.toast` (notifications de nouveaux messages)

**Structure** :
- Layout en 2 colonnes :
  - **Sidebar gauche** (si `showSidebar`) :
    - Liste des conversations (menu)
    - Recherche de conversations
    - Liste des utilisateurs en ligne (si `showUserList`)
  - **Zone principale** :
    - En-tête avec info du destinataire/conversation
    - Zone de messages (scrollable)
    - Indicateur de frappe (si `showTypingIndicator`)
    - Zone de saisie avec boutons d'action

**Exemple de structure de données** :

```php
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
        'isOwn' => false,
    ],
    [
        'id' => 2,
        'user_id' => auth()->id(),
        'user_name' => auth()->user()->name,
        'user_avatar' => auth()->user()->avatar,
        'content' => 'Bonjour !',
        'created_at' => now()->subMinutes(5),
        'isOwn' => true,
    ],
];
```

**JavaScript nécessaire** :
- Module JavaScript pour gérer :
  - Le polling des messages (si `useWebSockets` est false)
  - La connexion WebSocket via Laravel Echo (si `useWebSockets` est true)
  - L'envoi de messages (AJAX)
  - L'indicateur de frappe
  - Le scroll automatique vers le bas
  - L'upload de fichiers (si activé)

**Module JS recommandé** : `resources/js/modules/chat.js`

**Traductions nécessaires** (à créer `resources/lang/fr/chat.php`) :
- `messages" : "Messages"
- `conversations" : "Conversations"
- `new_message" : "Nouveau message"
- `type_message" : "Tapez votre message..."
- `send" : "Envoyer"
- `online" : "En ligne"
- `offline" : "Hors ligne"
- `typing" : ":name est en train d'écrire..."
- `no_conversations" : "Aucune conversation"
- `no_messages" : "Aucun message"
- `select_conversation" : "Sélectionnez une conversation"
- `file_upload" : "Joindre un fichier"
- `file_too_large" : "Le fichier est trop volumineux (max : :size KB)"

---

### 2. notification-center.blade.php
**Fichier** : `resources/views/templates/notification-center.blade.php`

**Description** : Centre de notifications avec liste des notifications, filtres par type, marquage comme lu/non lu, actions sur les notifications.

**Props** :
```php
@props([
    'title' => __('notifications.notifications'),
    'theme' => null,
    // Notifications data
    'notifications' => [], // Collection of notifications
    'unreadCount' => null, // Auto-calculate if not provided
    // Routes
    'markAsReadUrl' => Route::has('notifications.read') ? route('notifications.read') : '#',
    'markAllAsReadUrl' => Route::has('notifications.read-all') ? route('notifications.read-all') : '#',
    'deleteUrl' => Route::has('notifications.delete') ? route('notifications.delete') : '#',
    'loadMoreUrl' => Route::has('notifications.load-more') ? route('notifications.load-more') : '#',
    // Options
    'showFilters' => true,
    'showMarkAllRead' => true,
    'showDelete' => true,
    'groupByDate' => true,
    'pagination' => true,
    'itemsPerPage' => 20,
    'pollingInterval' => 30000, // ms (if not using WebSockets)
    'useWebSockets' => false, // Use Laravel Echo for real-time
])
```

**Fonctionnalités Laravel** :
- Utilise Laravel Notifications (`auth()->user()->notifications`)
- Utilise `auth()->user()->unreadNotifications` pour les non lues
- Utilise les routes RESTful pour les actions (read, delete)
- Utilise Laravel Broadcasting pour les notifications en temps réel
- Utilise `Carbon` pour formater les dates
- Utilise la pagination Laravel (`$notifications->links()`)
- Utilise les types de notifications Laravel (database, mail, etc.)

**Composants UI utilisés** :
- `x-daisy::layout.app` ou layout approprié
- `x-daisy::ui.layout.list` et `x-daisy::ui.layout.list-row` (liste des notifications)
- `x-daisy::ui.data-display.avatar` (avatar de l'expéditeur)
- `x-daisy::ui.data-display.badge` (badge "non lu", type de notification)
- `x-daisy::ui.inputs.button` (marquer comme lu, supprimer, tout marquer comme lu)
- `x-daisy::ui.advanced.filter` (filtres par type)
- `x-daisy::ui.navigation.pagination` (pagination)
- `x-daisy::ui.feedback.empty-state` (aucune notification)
- `x-daisy::ui.feedback.loading` (chargement)
- `x-daisy::ui.feedback.toast` (confirmation d'actions)

**Structure** :
- En-tête avec :
  - Titre "Notifications"
  - Compteur de non lues (badge)
  - Bouton "Tout marquer comme lu" (si `showMarkAllRead`)
- Filtres (si `showFilters`) :
  - Toutes
  - Non lues
  - Par type (Comment, Like, Mention, etc.)
- Liste des notifications :
  - Groupées par date (si `groupByDate`) : "Aujourd'hui", "Hier", "Cette semaine", etc.
  - Chaque notification :
    - Avatar de l'expéditeur
    - Contenu (texte, lien)
    - Date/heure
    - Badge "non lu"
    - Actions (marquer comme lu, supprimer)
- Pagination en bas (si `pagination`)
- État vide si aucune notification

**Exemple de structure de données** :

```php
$notifications = [
    [
        'id' => 1,
        'type' => 'App\Notifications\NewComment',
        'data' => [
            'message' => 'John a commenté votre post',
            'link' => '/posts/123',
            'user' => ['name' => 'John Doe', 'avatar' => '/img/people/people-1.jpg'],
        ],
        'read_at' => null,
        'created_at' => now()->subMinutes(5),
    ],
    [
        'id' => 2,
        'type' => 'App\Notifications\NewLike',
        'data' => [
            'message' => 'Jane a aimé votre post',
            'link' => '/posts/456',
            'user' => ['name' => 'Jane Smith', 'avatar' => '/img/people/people-2.jpg'],
        ],
        'read_at' => now()->subHours(2),
        'created_at' => now()->subHours(3),
    ],
];
```

**Actions disponibles** :
- **Marquer comme lu** : AJAX vers `markAsReadUrl` avec l'ID de la notification
- **Marquer tout comme lu** : AJAX vers `markAllAsReadUrl`
- **Supprimer** : AJAX vers `deleteUrl` avec l'ID de la notification
- **Cliquer sur la notification** : Redirige vers le lien dans `data['link']` et marque comme lu

**JavaScript nécessaire** :
- Module JavaScript pour gérer :
  - Le polling des nouvelles notifications (si `useWebSockets` est false)
  - La connexion WebSocket via Laravel Echo (si `useWebSockets` est true)
  - Les actions (marquer comme lu, supprimer)
  - Le filtrage côté client
  - La pagination infinie (scroll to load more)
  - Les toasts de confirmation

**Module JS recommandé** : `resources/js/modules/notifications.js`

**Traductions nécessaires** (à créer `resources/lang/fr/notifications.php`) :
- `notifications" : "Notifications"
- `all" : "Toutes"
- `unread" : "Non lues"
- `read" : "Lues"
- `mark_as_read" : "Marquer comme lu"
- `mark_all_as_read" : "Tout marquer comme lu"
- `delete" : "Supprimer"
- `no_notifications" : "Aucune notification"
- `no_unread_notifications" : "Aucune notification non lue"
- `today" : "Aujourd'hui"
- `yesterday" : "Hier"
- `this_week" : "Cette semaine"
- `older" : "Plus ancien"
- `new_notification" : "Nouvelle notification"
- `notification_deleted" : "Notification supprimée"
- `all_marked_as_read" : "Toutes les notifications ont été marquées comme lues"

---

## Composants/Wrappers nécessaires

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

### Aucun autre composant requis
Tous les autres composants existent déjà.

---

## Tests à prévoir

Pour chaque template :
1. **Test de rendu** : Vérifier le rendu avec les props par défaut
2. **Test avec données** : Vérifier l'affichage des messages/notifications
3. **Test d'actions** : Vérifier les actions (envoyer message, marquer comme lu, etc.)
4. **Test de pagination** : Vérifier la pagination (notifications)
5. **Test de filtres** : Vérifier les filtres (notifications)
6. **Test responsive** : Vérifier l'affichage sur mobile
7. **Test WebSocket** : Vérifier la réception en temps réel (si activé)
8. **Test d'état vide** : Vérifier l'affichage quand il n'y a pas de données

---

## Ordre d'implémentation recommandé

1. Créer le composant `empty-state.blade.php` (utilisé par les deux templates)
2. `notification-center.blade.php` (plus simple, pas de saisie)
3. `chat.blade.php` (plus complexe avec saisie et WebSocket)

---

## Notes importantes

- **chat** : Nécessite Laravel Echo + Pusher/Ably pour le temps réel, ou polling AJAX en fallback
- **notification-center** : Utilise le système de notifications Laravel (`database` driver recommandé)
- Les deux templates peuvent fonctionner sans WebSocket avec du polling AJAX
- Les routes API doivent être définies dans `routes/api.php` ou `routes/web.php`
- Les traductions doivent être ajoutées dans `resources/lang/fr/chat.php`, `resources/lang/fr/notifications.php` et leurs équivalents anglais
- Pour le chat, utiliser les relations Eloquent (`conversation->messages()->with('user')->latest()->paginate()`)
- Pour les notifications, utiliser `auth()->user()->notifications()->paginate()`
- Les dates doivent être formatées avec `Carbon` (ex: `$message->created_at->diffForHumans()`)

