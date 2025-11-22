<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Si la documentation publique est activée, rediriger la racine vers la page Docs
    if (config('daisy-kit.docs.enabled')) {
        $prefix = (string) config('daisy-kit.docs.prefix', 'docs');

        return redirect('/'.ltrim($prefix, '/'));
    }

    return view('welcome');
});

// Page de démo réservée au dev (non publiée) via le namespace daisy-dev
Route::get('/demo', function () {
    return view('daisy-dev::demo.ui.index');
})->name('demo');

// Endpoint REST simple pour Calendar Full (démo)
Route::get('/demo/api/calendar-events', function (\Illuminate\Http\Request $request) {
    $start = new DateTime((string) $request->query('start', date('Y-m-01')));
    $end = new DateTime((string) $request->query('end', date('Y-m-t')));
    // Génère quelques évènements factices dans la plage demandée
    $events = [];
    $cur = clone $start;
    while ($cur < $end) {
        $day = (int) $cur->format('j');
        if (in_array($day, [1, 7, 12, 14, 28], true)) {
            $iso = $cur->format('Y-m-d');
            if ($day === 1) {
                $events[] = ['id' => "a-$iso", 'title' => 'All Day Event', 'start' => $iso, 'allDay' => true];
            }
            if ($day === 12) {
                $events[] = ['id' => "m1-$iso", 'title' => 'Meeting', 'start' => "$iso 10:30", 'end' => "$iso 12:30"];
            }
            if ($day === 14) {
                $events[] = ['id' => "b-$iso", 'title' => 'Birthday Party', 'start' => "$iso 07:00"];
            }
            if ($day === 28) {
                $events[] = ['id' => "g-$iso", 'title' => 'Click for Google', 'start' => $iso, 'url' => 'https://google.com'];
            }
            if ($day === 7) {
                $events[] = ['id' => "long-$iso", 'title' => 'Long Event', 'start' => $iso, 'end' => $cur->modify('+7 day')->format('Y-m-d')];
            }
        }
        $cur->modify('+1 day');
    }

    return response()->json($events);
})->name('demo.calendar.events');

// Endpoint REST pour lazy-loading du TreeView en démo
Route::get('/demo/api/tree-children', function (\Illuminate\Http\Request $request) {
    $node = (string) $request->query('node', '');
    // Exemple simple: renvoie des enfants factices selon l'id du nœud
    $data = match ($node) {
        'b' => [
            ['id' => 'b1', 'label' => 'Fichier B1'],
            // Exemple de nœud lazy imbriqué (avec disabled supporté)
            ['id' => 'b2', 'label' => 'Dossier B2 (lazy, disabled)', 'lazy' => true, 'disabled' => true],
            ['id' => 'b3', 'label' => 'Fichier B3'],
        ],
        // Quand on ouvre B2, on renvoie ses enfants (B2-2 devient lazy)
        'b2' => [
            ['id' => 'b2-1', 'label' => 'Fichier B2-1'],
            ['id' => 'b2-2', 'label' => 'Dossier B2-2 (lazy)', 'lazy' => true],
        ],
        // Les enfants de B2-2 ne sont renvoyés que lorsqu'on ouvre B2-2
        'b2-2' => [
            ['id' => 'b2-2-1', 'label' => 'Fichier B2-2-1'],
            ['id' => 'b2-2-2', 'label' => 'Fichier B2-2-2'],
        ],
        'root' => [
            ['id' => 'r1', 'label' => 'Fichier Racine 1'],
            ['id' => 'r2', 'label' => 'Fichier Racine 2'],
        ],
        default => [
            ['id' => $node.'-1', 'label' => 'Fichier '.$node.'-1'],
            ['id' => $node.'-2', 'label' => 'Fichier '.$node.'-2'],
        ],
    };

    return response()->json($data);
})->name('demo.tree.children');

// Endpoint REST pour recherche dans le TreeView (démo)
Route::get('/demo/api/tree-search', function (\Illuminate\Http\Request $request) {
    $q = strtolower((string) $request->query('q', ''));
    // Données de démo correspondant à demoTreeSingle
    $data = [
        ['id' => 'root', 'label' => 'Racine', 'children' => [
            ['id' => 'a', 'label' => 'Dossier A', 'children' => [
                ['id' => 'a1', 'label' => 'Fichier A1'],
                ['id' => 'a2', 'label' => 'Fichier A2'],
            ]],
            ['id' => 'b', 'label' => 'Dossier B (lazy)', 'children' => [
                ['id' => 'b1', 'label' => 'Fichier B1'],
                ['id' => 'b2', 'label' => 'Dossier B2 (lazy, disabled)', 'children' => [
                    ['id' => 'b2-1', 'label' => 'Fichier B2-1'],
                    ['id' => 'b2-2', 'label' => 'Dossier B2-2 (lazy)', 'children' => [
                        ['id' => 'b2-2-1', 'label' => 'Fichier B2-2-1'],
                        ['id' => 'b2-2-2', 'label' => 'Fichier B2-2-2'],
                    ]],
                ]],
            ]],
            ['id' => 'c', 'label' => 'Fichier C'],
        ]],
    ];
    $paths = [];
    $cur = [];
    $walk = function ($nodes) use (&$walk, &$paths, &$cur, $q) {
        foreach ($nodes as $n) {
            $cur[] = $n['id'];
            $label = strtolower((string) ($n['label'] ?? (string) $n['id']));
            if ($q !== '' && (str_contains($label, $q) || str_contains(strtolower((string) $n['id']), $q))) {
                // On retourne uniquement le chemin jusqu'au nœud trouvé (sans forcer l'extension des descendants)
                $paths[] = $cur; // copie implicite
            }
            $children = $n['children'] ?? [];
            if (! empty($children)) {
                $walk($children);
            }
            array_pop($cur);
        }
    };
    $walk($data);
    // Limite pour éviter les réponses trop volumineuses côté démo
    $paths = array_slice($paths, 0, 50);

    return response()->json(['paths' => $paths]);
})->name('demo.tree.search');

// Endpoint REST pour autocomplete des selects (démo)
Route::get('/demo/api/select-options', function (\Illuminate\Http\Request $request) {
    $q = strtolower((string) $request->query('q', ''));
    // Données factices - mélange simple + contacts (avatar/sous-titre)
    $pool = [
        ['value' => 'alpha', 'label' => 'Alpha'],
        ['value' => 'alpine', 'label' => 'Alpine'],
        ['value' => 'beta', 'label' => 'Beta'],
        ['value' => 'bravo', 'label' => 'Bravo'],
        ['value' => 'charlie', 'label' => 'Charlie'],
        ['value' => 'delta', 'label' => 'Delta'],
        ['value' => 'echo', 'label' => 'Echo'],
        ['value' => 'foxtrot', 'label' => 'Foxtrot'],
        ['value' => 'golf', 'label' => 'Golf'],
        ['value' => 'hotel', 'label' => 'Hotel'],
        ['value' => 'india', 'label' => 'India'],
        ['value' => 'juliet', 'label' => 'Juliet'],
        ['value' => 'kilo', 'label' => 'Kilo'],
        ['value' => 'lima', 'label' => 'Lima'],
        ['value' => 'mike', 'label' => 'Mike'],
        ['value' => 'november', 'label' => 'November'],
        ['value' => 'oscar', 'label' => 'Oscar'],
        ['value' => 'papa', 'label' => 'Papa'],
        ['value' => 'quebec', 'label' => 'Quebec'],
        ['value' => 'romeo', 'label' => 'Romeo'],
        ['value' => 'sierra', 'label' => 'Sierra'],
        ['value' => 'tango', 'label' => 'Tango'],
        ['value' => 'uniform', 'label' => 'Uniform'],
        ['value' => 'victor', 'label' => 'Victor'],
        ['value' => 'whiskey', 'label' => 'Whiskey'],
        ['value' => 'xray', 'label' => 'X-Ray'],
        ['value' => 'yankee', 'label' => 'Yankee'],
        ['value' => 'zulu', 'label' => 'Zulu'],
    ];
    $contacts = [
        ['value' => 'c_john', 'label' => 'John Carter', 'subtitle' => 'john.carter@example.com', 'avatar' => '/img/people/people-1.jpg'],
        ['value' => 'c_jane', 'label' => 'Jane Doe', 'subtitle' => 'jane.doe@example.com', 'avatar' => '/img/people/people-2.jpg'],
        ['value' => 'c_alex', 'label' => 'Alex Martin', 'subtitle' => 'alex.martin@example.com', 'avatar' => '/img/people/people-3.jpg'],
        ['value' => 'c_sara', 'label' => 'Sara Kim', 'subtitle' => 'sara.kim@example.com', 'avatar' => '/img/people/people-4.jpg'],
        ['value' => 'c_luc', 'label' => 'Luc Bernard', 'subtitle' => 'luc.bernard@example.com', 'avatar' => '/img/people/people-5.jpg'],
    ];

    // Réponse groupée si la requête commence par "@"
    if (str_starts_with((string) $request->query('q', ''), '@')) {
        $groups = [
            ['title' => 'Contacts', 'items' => $contacts],
            ['title' => 'Mots', 'items' => array_slice($pool, 0, 8)],
        ];

        return response()->json([
            'groups' => $groups,
            'meta' => ['more' => 0],
        ]);
    }

    // Réponse items + meta.more
    $items = [];
    foreach (array_merge($contacts, $pool) as $item) {
        $label = strtolower((string) ($item['label'] ?? ''));
        $value = strtolower((string) ($item['value'] ?? ''));
        $subtitle = strtolower((string) ($item['subtitle'] ?? ''));
        if ($q === '' || str_contains($label, $q) || str_contains($value, $q) || ($subtitle !== '' && str_contains($subtitle, $q))) {
            $items[] = $item;
        }
    }
    $limit = 10;
    $more = max(0, count($items) - $limit);
    $items = array_slice($items, 0, $limit);

    return response()->json([
        'items' => $items,
        'meta' => ['more' => $more],
    ]);
})->name('demo.select.options');

// Routes de démo pour le chat
Route::post('/demo/api/chat/send', function (\Illuminate\Http\Request $request) {
    $conversationId = $request->input('conversation_id', 1);
    $content = $request->input('content', '');
    $files = $request->file('files', []);
    $file = $request->file('file');

    // Simuler l'envoi d'un message
    $message = [
        'id' => rand(100, 999),
        'user_id' => 1, // Utilisateur courant
        'user_name' => 'Moi',
        'user_avatar' => 'https://www.placeholderimage.eu/api/id/4/200/200',
        'content' => $content,
        'created_at' => now()->toDateTimeString(),
    ];

    // Gérer les fichiers
    if ($file || ! empty($files)) {
        $attachments = [];
        $fileList = $file ? [$file] : $files;

        foreach ($fileList as $index => $f) {
            $attachments[] = [
                'url' => 'https://www.placeholderimage.eu/api/id/'.(20 + $index).'/400/300',
                'name' => $f->getClientOriginalName(),
                'type' => str_starts_with($f->getMimeType(), 'image/') ? 'image' : 'other',
                'size' => round($f->getSize() / 1024, 1).' KB',
            ];
        }

        if (count($attachments) === 1) {
            $message['attachment'] = $attachments[0];
        } else {
            $message['attachments'] = $attachments;
        }
    }

    return response()->json([
        'success' => true,
        'message' => $message,
    ]);
})->name('demo.chat.send');

Route::post('/demo/api/chat/typing', function (\Illuminate\Http\Request $request) {
    // Simuler l'indicateur de frappe
    return response()->json(['success' => true]);
})->name('demo.chat.typing');

Route::get('/demo/api/chat/messages/{conversationId}', function ($conversationId) {
    // Retourner les messages de la conversation (pour le polling)
    $messages = [
        [
            'id' => 1,
            'user_id' => 2,
            'user_name' => 'Alice Martin',
            'user_avatar' => 'https://www.placeholderimage.eu/api/id/1/200/200',
            'content' => 'Salut ! Comment allez-vous ?',
            'created_at' => now()->subMinutes(30)->toDateTimeString(),
        ],
        [
            'id' => 2,
            'user_id' => 1,
            'user_name' => 'Moi',
            'user_avatar' => 'https://www.placeholderimage.eu/api/id/4/200/200',
            'content' => 'Très bien, merci ! Et vous ?',
            'created_at' => now()->subMinutes(25)->toDateTimeString(),
        ],
    ];

    return response()->json([
        'success' => true,
        'data' => $messages,
    ]);
})->name('demo.chat.messages');

// Routes pour les templates
Route::prefix('templates')->name('templates.')->group(function () {
    // Templates d'authentification
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::view('/login-simple', 'daisy::templates.auth.login-simple')->name('login-simple');
        Route::view('/login-split', 'daisy::templates.auth.login-split')->name('login-split');
        Route::view('/register-simple', 'daisy::templates.auth.register-simple')->name('register-simple');
        Route::view('/register-split', 'daisy::templates.auth.register-split')->name('register-split');
        Route::view('/forgot-password', 'daisy::templates.auth.forgot-password')->name('forgot-password');
        Route::view('/reset-password', 'daisy::templates.auth.reset-password')->name('reset-password');
        Route::view('/two-factor', 'daisy::templates.auth.two-factor')->name('two-factor');
        Route::view('/verify-email', 'daisy::templates.auth.verify-email')->name('verify-email');
        Route::view('/resend-verification', 'daisy::templates.auth.resend-verification')->name('resend-verification');
    });

    // Templates de profil
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::view('/view', 'daisy::templates.profile.profile-view')->name('view');
        Route::view('/edit', 'daisy::templates.profile.profile-edit')->name('edit');
        Route::view('/settings', 'daisy::templates.profile.profile-settings')->name('settings');
    });

    // Templates de communication
    Route::prefix('communication')->name('communication.')->group(function () {
        Route::get('/chat', function () {
            // Données de démo pour le template chat
            $conversations = [
                [
                    'id' => 1,
                    'name' => 'Alice Martin',
                    'avatar' => 'https://www.placeholderimage.eu/api/id/1/200/200',
                    'isOnline' => true,
                    'lastMessage' => 'Salut, comment ça va ?',
                    'unreadCount' => 2,
                ],
                [
                    'id' => 2,
                    'name' => 'Bob Dupont',
                    'avatar' => 'https://www.placeholderimage.eu/api/id/2/200/200',
                    'isOnline' => false,
                    'lastMessage' => 'Merci pour votre aide !',
                    'unreadCount' => 0,
                ],
                [
                    'id' => 3,
                    'name' => 'Charlie Bernard',
                    'avatar' => 'https://www.placeholderimage.eu/api/id/3/200/200',
                    'isOnline' => true,
                    'lastMessage' => 'À bientôt !',
                    'unreadCount' => 5,
                ],
            ];

            $conversation = $conversations[0] ?? null;

            $messages = $conversation ? [
                [
                    'id' => 1,
                    'user_id' => 2,
                    'user_name' => 'Alice Martin',
                    'user_avatar' => 'https://www.placeholderimage.eu/api/id/1/200/200',
                    'content' => 'Salut ! Comment allez-vous ?',
                    'created_at' => now()->subMinutes(30)->toDateTimeString(),
                ],
                [
                    'id' => 2,
                    'user_id' => 1,
                    'user_name' => 'Moi',
                    'user_avatar' => 'https://www.placeholderimage.eu/api/id/4/200/200',
                    'content' => 'Très bien, merci ! Et vous ?',
                    'created_at' => now()->subMinutes(25)->toDateTimeString(),
                ],
                [
                    'id' => 3,
                    'user_id' => 2,
                    'user_name' => 'Alice Martin',
                    'user_avatar' => 'https://www.placeholderimage.eu/api/id/1/200/200',
                    'content' => 'Parfait ! Je voulais vous parler du projet. Voici une image du prototype :',
                    'attachment' => [
                        'url' => 'https://www.placeholderimage.eu/api/id/10/800/600',
                        'name' => 'prototype.jpg',
                        'type' => 'image',
                        'size' => '2.3 MB',
                    ],
                    'created_at' => now()->subMinutes(20)->toDateTimeString(),
                ],
                [
                    'id' => 4,
                    'user_id' => 1,
                    'user_name' => 'Moi',
                    'user_avatar' => 'https://www.placeholderimage.eu/api/id/4/200/200',
                    'content' => 'D\'accord, je vous écoute.',
                    'created_at' => now()->subMinutes(15)->toDateTimeString(),
                ],
                [
                    'id' => 5,
                    'user_id' => 2,
                    'user_name' => 'Alice Martin',
                    'user_avatar' => 'https://www.placeholderimage.eu/api/id/1/200/200',
                    'content' => 'Et voici le document de spécifications :',
                    'attachment' => [
                        'url' => 'https://www.placeholderimage.eu/api/id/15/800/600',
                        'name' => 'specifications.pdf',
                        'type' => 'pdf',
                        'size' => '1.8 MB',
                    ],
                    'created_at' => now()->subMinutes(10)->toDateTimeString(),
                ],
                [
                    'id' => 6,
                    'user_id' => 1,
                    'user_name' => 'Moi',
                    'user_avatar' => 'https://www.placeholderimage.eu/api/id/4/200/200',
                    'content' => 'Merci ! Je vais le consulter.',
                    'created_at' => now()->subMinutes(5)->toDateTimeString(),
                ],
                [
                    'id' => 7,
                    'user_id' => 2,
                    'user_name' => 'Alice Martin',
                    'user_avatar' => 'https://www.placeholderimage.eu/api/id/1/200/200',
                    'content' => '',
                    'attachments' => [
                        [
                            'url' => 'https://www.placeholderimage.eu/api/id/20/400/300',
                            'name' => 'screenshot-1.png',
                            'type' => 'image',
                            'size' => '850 KB',
                        ],
                        [
                            'url' => 'https://www.placeholderimage.eu/api/id/21/400/300',
                            'name' => 'screenshot-2.png',
                            'type' => 'image',
                            'size' => '920 KB',
                        ],
                    ],
                    'created_at' => now()->subMinutes(2)->toDateTimeString(),
                ],
            ] : [];

            return view('daisy::templates.chat', [
                'conversation' => $conversation,
                'conversations' => $conversations,
                'messages' => $messages,
                'currentUserId' => 1,
                'showSidebar' => true,
                'enableFileUpload' => true,
                'multipleFiles' => true,
                'showFilePreview' => true,
                'sendMessageUrl' => route('demo.chat.send'),
                'typingUrl' => route('demo.chat.typing'),
                'loadMessagesUrl' => route('demo.chat.messages', ['conversationId' => ':conversationId']),
            ]);
        })->name('chat');

        Route::get('/notification-center', function () {
            $now = now();

            $notifications = [
                [
                    'id' => 1,
                    'type' => 'project',
                    'data' => [
                        'message' => 'Sophie vous a assigné la résolution du ticket Phoenix-241.',
                        'priority' => 'critical',
                        'channel' => 'in_app',
                        'tags' => ['Phoenix', 'Sprint 8'],
                        'user' => [
                            'name' => 'Sophie Trentin',
                            'avatar' => 'https://www.placeholderimage.eu/api/id/5/200/200',
                        ],
                        'action' => [
                            'label' => 'Ouvrir le ticket',
                            'url' => '#',
                            'icon' => 'bi-kanban',
                        ],
                        'due_at' => $now->copy()->addHours(6)->toIso8601String(),
                    ],
                    'created_at' => $now->copy()->subMinutes(15)->toIso8601String(),
                    'read_at' => null,
                ],
                [
                    'id' => 2,
                    'type' => 'approval',
                    'data' => [
                        'message' => 'Le devis #4582 est en attente de validation financière.',
                        'priority' => 'high',
                        'channel' => 'email',
                        'tags' => ['Finance'],
                        'user' => [
                            'name' => 'Clément Dubois',
                            'avatar' => 'https://www.placeholderimage.eu/api/id/8/200/200',
                        ],
                        'action' => [
                            'label' => 'Revoir le devis',
                            'url' => '#',
                            'icon' => 'bi-file-earmark-check',
                        ],
                        'due_at' => $now->copy()->addDay()->startOfDay()->toIso8601String(),
                    ],
                    'created_at' => $now->copy()->subHour()->toIso8601String(),
                    'read_at' => null,
                ],
                [
                    'id' => 3,
                    'type' => 'mention',
                    'data' => [
                        'message' => 'Lucas vous a mentionné dans les retours UX de la release 4.2.',
                        'priority' => 'medium',
                        'channel' => 'push',
                        'tags' => ['UX', 'Release'],
                        'user' => [
                            'name' => 'Lucas Perret',
                            'avatar' => 'https://www.placeholderimage.eu/api/id/12/200/200',
                        ],
                        'action' => [
                            'label' => 'Voir le fil',
                            'url' => '#',
                            'icon' => 'bi-chat-dots',
                        ],
                    ],
                    'created_at' => $now->copy()->subHours(3)->toIso8601String(),
                    'read_at' => null,
                ],
                [
                    'id' => 4,
                    'type' => 'report',
                    'data' => [
                        'message' => 'Rapport hebdo prêt : adoption produit +18 %.',
                        'priority' => 'low',
                        'channel' => 'email',
                        'tags' => ['Reporting'],
                        'user' => [
                            'name' => 'Insights Bot',
                            'avatar' => null,
                        ],
                        'action' => [
                            'label' => 'Consulter le rapport',
                            'url' => '#',
                            'icon' => 'bi-graph-up',
                        ],
                    ],
                    'created_at' => $now->copy()->subDay()->toIso8601String(),
                    'read_at' => $now->copy()->subDay()->addMinutes(10)->toIso8601String(),
                ],
            ];

            $types = [
                ['label' => 'Projets', 'value' => 'project'],
                ['label' => 'Approvals', 'value' => 'approval'],
                ['label' => 'Mentions', 'value' => 'mention'],
                ['label' => 'Rapports', 'value' => 'report'],
            ];

            return view('daisy::templates.notification-center', [
                'notifications' => $notifications,
                'unreadCount' => 3,
                'types' => $types,
                'currentFilter' => 'all',
                'preferencesUrl' => '#',
                'digestTime' => '08:00',
                'userId' => 1,
                'showFilters' => true,
            ]);
        })->name('notification-center');
    });

    // Templates de layouts
    Route::prefix('layouts')->name('layouts.')->group(function () {
        Route::view('/navbar', 'daisy-dev::demo.templates.test-navbar')->name('navbar');
        Route::view('/sidebar', 'daisy-dev::demo.templates.test-sidebar')->name('sidebar');
        Route::view('/navbar-sidebar', 'daisy-dev::demo.templates.test-navbar-sidebar')->name('navbar-sidebar');
        Route::view('/grid-layout', 'daisy-dev::demo.templates.test-grid-layout')->name('grid-layout');
        Route::view('/crud-layout', 'daisy-dev::demo.templates.test-crud-layout')->name('crud-layout');
    });

    // Templates de documentation
    Route::prefix('documentation')->name('documentation.')->group(function () {
        Route::get('/changelog', function () {
            $versions = [
                [
                    'version' => '2.0.0',
                    'date' => '2024-01-15',
                    'isCurrent' => true,
                    'tagUrl' => 'https://github.com/user/repo/releases/tag/2.0.0',
                    'compareUrl' => 'https://github.com/user/repo/compare/1.9.0...2.0.0',
                    'items' => [
                        [
                            'type' => 'added',
                            'category' => 'Features',
                            'description' => 'Nouvelle fonctionnalité de recherche avancée',
                            'breaking' => false,
                            'issues' => [123, 456],
                            'contributors' => ['user1', 'user2'],
                        ],
                        [
                            'type' => 'changed',
                            'category' => 'Performance',
                            'description' => 'Amélioration des performances de chargement',
                            'breaking' => false,
                            'issues' => [789],
                        ],
                        [
                            'type' => 'fixed',
                            'category' => 'Bugfixes',
                            'description' => 'Correction du bug de connexion',
                            'breaking' => false,
                            'issues' => [101],
                        ],
                        [
                            'type' => 'removed',
                            'category' => 'Deprecations',
                            'description' => 'Suppression de l\'ancien système de cache',
                            'breaking' => true,
                            'migration' => true,
                            'migrationGuide' => 'https://docs.example.com/migrations/cache',
                        ],
                        [
                            'type' => 'security',
                            'category' => 'Security',
                            'description' => 'Mise à jour de sécurité critique',
                            'breaking' => false,
                            'cve' => 'CVE-2024-1234',
                            'severity' => 'high',
                        ],
                    ],
                ],
                [
                    'version' => '1.9.0',
                    'date' => '2023-12-01',
                    'isCurrent' => false,
                    'items' => [
                        [
                            'type' => 'added',
                            'description' => 'Nouvelle page de profil',
                        ],
                        [
                            'type' => 'fixed',
                            'description' => 'Correction de plusieurs bugs mineurs',
                        ],
                    ],
                ],
                [
                    'version' => '1.8.0',
                    'date' => '2023-11-15',
                    'isCurrent' => false,
                    'changes' => [
                        'added' => [
                            'Support des thèmes personnalisés',
                        ],
                        'fixed' => [
                            'Correction de l\'affichage sur mobile',
                        ],
                    ],
                ],
            ];

            return view('daisy::templates.changelog', [
                'title' => 'Historique des versions',
                'versions' => $versions,
                'currentVersion' => '2.0.0',
                'rssUrl' => '#',
                'atomUrl' => '#',
                'showFilters' => true,
                'showSearch' => true,
                'expandLatest' => true,
            ]);
        })->name('changelog');
    });
});
