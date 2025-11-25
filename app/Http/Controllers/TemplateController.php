<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class TemplateController extends Controller
{
    public function chat(): View
    {
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
    }

    public function notificationCenter(): View
    {
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
    }

    public function changelog(): View
    {
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
    }
}
