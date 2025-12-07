<?php

use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

describe('Communication Components Rendering', function () {
    describe('Empty State', function () {
        it('renders empty-state component with default props', function () {
            $html = View::make('daisy::components.ui.feedback.empty-state', [
                'title' => 'No data',
                'icon' => 'bi-inbox',
            ])->render();

            expect($html)
                ->toContain('No data');
        });

        it('renders empty-state with icon, message and action', function () {
            $html = View::make('daisy::components.ui.feedback.empty-state', [
                'icon' => 'bi-inbox',
                'title' => 'No notifications',
                'message' => 'You have no notifications yet.',
                'actionLabel' => 'View all',
                'actionUrl' => '/notifications',
            ])->render();

            expect($html)
                ->toContain('bi-inbox')
                ->toContain('No notifications')
                ->toContain('You have no notifications yet.')
                ->toContain('View all')
                ->toContain('/notifications');
        });
    });

    describe('Notification Item', function () {
        it('renders notification-item component with default props', function () {
            $notification = [
                'id' => 1,
                'type' => 'comment',
                'data' => [
                    'message' => 'John commented on your post',
                    'user' => ['name' => 'John Doe', 'avatar' => '/img/avatar.jpg'],
                ],
                'read_at' => null,
                'created_at' => now(),
            ];

            $html = View::make('daisy::components.ui.communication.notification-item', [
                'notification' => $notification,
            ])->render();

            expect($html)
                ->toContain('notification-item')
                ->toContain('John commented on your post')
                ->toContain('John Doe');
        });

        it('renders notification-item as read', function () {
            $notification = [
                'id' => 1,
                'read_at' => now(),
            ];

            $html = View::make('daisy::components.ui.communication.notification-item', [
                'notification' => $notification,
            ])->render();

            expect($html)
                ->toContain('data-read="true"');
        });
    });

    describe('Notification List', function () {
        it('renders notification-list with empty array', function () {
            $html = View::make('daisy::components.ui.communication.notification-list', [
                'notifications' => [],
            ])->render();

            expect($html)
                ->toContain('notification-list');
        });

        it('renders notification-list with notifications', function () {
            $notifications = [
                [
                    'id' => 1,
                    'type' => 'comment',
                    'data' => ['message' => 'Message 1'],
                    'created_at' => now(),
                ],
                [
                    'id' => 2,
                    'type' => 'like',
                    'data' => ['message' => 'Message 2'],
                    'created_at' => now()->subDay(),
                ],
            ];

            $html = View::make('daisy::components.ui.communication.notification-list', [
                'notifications' => $notifications,
            ])->render();

            expect($html)
                ->toContain('notification-list')
                ->toContain('Message 1')
                ->toContain('Message 2');
        });

        it('renders notification-list grouped by date', function () {
            $notifications = [
                [
                    'id' => 1,
                    'created_at' => now(),
                ],
                [
                    'id' => 2,
                    'created_at' => now()->subDay(),
                ],
            ];

            $html = View::make('daisy::components.ui.communication.notification-list', [
                'notifications' => $notifications,
                'groupByDate' => true,
            ])->render();

            expect($html)
                ->toContain('divider');
        });
    });

    describe('Notification Filters', function () {
        it('renders notification-filters with default props', function () {
            $html = View::make('daisy::components.ui.communication.notification-filters', [
                'types' => [],
                'currentFilter' => 'all',
            ])->render();

            expect($html)
                ->toContain('notification-filters')
                ->toContain('filter');
        });

        it('renders notification-filters with types', function () {
            $html = View::make('daisy::components.ui.communication.notification-filters', [
                'types' => ['comment', 'like', 'mention'],
                'currentFilter' => 'unread',
            ])->render();

            expect($html)
                ->toContain('notification-filters')
                ->toContain('filter');
        });
    });

    describe('Notification Bell', function () {
        it('renders notification-bell component', function () {
            $html = View::make('daisy::components.ui.communication.notification-bell', [
                'notifications' => [],
                'unreadCount' => 0,
            ])->render();

            expect($html)
                ->toContain('dropdown');
        });

        it('renders notification-bell with unread count', function () {
            $html = View::make('daisy::components.ui.communication.notification-bell', [
                'notifications' => [],
                'unreadCount' => 5,
            ])->render();

            expect($html)
                ->toContain('badge')
                ->toContain('5');
        });
    });

    describe('Chat Header', function () {
        it('renders chat-header component', function () {
            $conversation = [
                'id' => 1,
                'name' => 'John Doe',
                'avatar' => '/img/avatar.jpg',
                'isOnline' => true,
            ];

            $html = View::make('daisy::components.ui.communication.chat-header', [
                'conversation' => $conversation,
            ])->render();

            expect($html)
                ->toContain('chat-header')
                ->toContain('John Doe')
                ->toContain('avatar-online');
        });

        it('renders chat-header with back button', function () {
            $conversation = ['id' => 1, 'name' => 'John'];

            $html = View::make('daisy::components.ui.communication.chat-header', [
                'conversation' => $conversation,
                'showBackButton' => true,
                'backUrl' => '/chat',
            ])->render();

            expect($html)
                ->toContain('bi-arrow-left')
                ->toContain('/chat');
        });
    });

    describe('Chat Messages', function () {
        it('renders chat-messages with empty array', function () {
            $html = View::make('daisy::components.ui.communication.chat-messages', [
                'messages' => [],
                'currentUserId' => 1,
            ])->render();

            expect($html)
                ->toContain('chat-messages')
                ->toContain('data-module="chat-messages"');
        });

        it('renders chat-messages with messages', function () {
            $messages = [
                [
                    'id' => 1,
                    'user_id' => 1,
                    'user_name' => 'John',
                    'content' => 'Hello',
                    'created_at' => now(),
                ],
                [
                    'id' => 2,
                    'user_id' => 2,
                    'user_name' => 'Jane',
                    'content' => 'Hi',
                    'created_at' => now(),
                ],
            ];

            $html = View::make('daisy::components.ui.communication.chat-messages', [
                'messages' => $messages,
                'currentUserId' => 1,
            ])->render();

            expect($html)
                ->toContain('chat-messages')
                ->toContain('Hello')
                ->toContain('Hi')
                ->toContain('chat-bubble');
        });
    });

    describe('Chat Input', function () {
        it('renders chat-input component', function () {
            $html = View::make('daisy::components.ui.communication.chat-input', [
                'sendMessageUrl' => '/chat/send',
            ])->render();

            expect($html)
                ->toContain('chat-input')
                ->toContain('data-module="chat-input"')
                ->toContain('data-send-message-url="/chat/send"')
                ->toContain('bi-send');
        });

        it('renders chat-input with file upload enabled', function () {
            $html = View::make('daisy::components.ui.communication.chat-input', [
                'enableFileUpload' => true,
                'maxFileSize' => 5120,
            ])->render();

            expect($html)
                ->toContain('data-enable-file-upload="true"')
                ->toContain('data-max-file-size="5120"')
                ->toContain('bi-paperclip');
        });
    });

    describe('Chat Sidebar', function () {
        it('renders chat-sidebar with empty conversations', function () {
            $html = View::make('daisy::components.ui.communication.chat-sidebar', [
                'conversations' => [],
            ])->render();

            expect($html)
                ->toContain('chat-sidebar');
        });

        it('renders chat-sidebar with conversations', function () {
            $conversations = [
                [
                    'id' => 1,
                    'name' => 'John Doe',
                    'avatar' => '/img/avatar.jpg',
                    'lastMessage' => 'Hello',
                    'unreadCount' => 2,
                    'isOnline' => true,
                ],
            ];

            $html = View::make('daisy::components.ui.communication.chat-sidebar', [
                'conversations' => $conversations,
                'currentConversationId' => 1,
            ])->render();

            expect($html)
                ->toContain('chat-sidebar')
                ->toContain('John Doe')
                ->toContain('Hello')
                ->toContain('badge');
        });
    });

    describe('Chat Widget', function () {
        it('renders chat-widget component', function () {
            $html = View::make('daisy::components.ui.communication.chat-widget', [
                'conversation' => ['id' => 1, 'name' => 'John'],
                'messages' => [],
                'currentUserId' => 1,
            ])->render();

            expect($html)
                ->toContain('chat-widget')
                ->toContain('data-module="chat-widget"')
                ->toContain('bi-chat-dots');
        });

        it('renders chat-widget minimized', function () {
            $html = View::make('daisy::components.ui.communication.chat-widget', [
                'conversation' => ['id' => 1],
                'messages' => [],
                'minimized' => true,
            ])->render();

            expect($html)
                ->toContain('data-minimized="true"');
        });
    });

    describe('Notification Center Template', function () {
        it('renders notification-center template', function () {
            $html = View::make('daisy::templates.communication.notification-center', [
                'notifications' => [],
                'unreadCount' => 0,
            ])->render();

            expect($html)
                ->toContain('notification-center')
                ->toContain('data-mark-all-as-read-url')
                ->toContain('data-module="notifications"');
        });

        it('renders notification-center with notifications', function () {
            $notifications = [
                [
                    'id' => 1,
                    'type' => 'comment',
                    'data' => ['message' => 'Test'],
                    'created_at' => now(),
                ],
            ];

            $html = View::make('daisy::templates.communication.notification-center', [
                'notifications' => $notifications,
                'unreadCount' => 1,
            ])->render();

            expect($html)
                ->toContain('Test');
        });
    });

    describe('Chat Template', function () {
        it('renders chat template without conversation', function () {
            $html = View::make('daisy::templates.communication.chat', [
                'conversations' => [],
                'messages' => [],
            ])->render();

            expect($html)
                ->toContain('chat');
        });

        it('renders chat template with conversation', function () {
            $conversation = [
                'id' => 1,
                'name' => 'John Doe',
                'avatar' => '/img/avatar.jpg',
            ];

            $html = View::make('daisy::templates.communication.chat', [
                'conversation' => $conversation,
                'messages' => [],
                'currentUserId' => 1,
            ])->render();

            expect($html)
                ->toContain('chat')
                ->toContain('John Doe')
                ->toContain('chat-header')
                ->toContain('chat-messages')
                ->toContain('chat-input');
        });

        it('renders chat template with sidebar', function () {
            $conversations = [
                ['id' => 1, 'name' => 'John'],
            ];

            $html = View::make('daisy::templates.communication.chat', [
                'conversations' => $conversations,
                'showSidebar' => true,
            ])->render();

            expect($html)
                ->toContain('chat-sidebar')
                ->toContain('John');

            expect(Str::substrCount($html, 'class="chat-sidebar'))->toBe(1);
        });
    });

    describe('File Preview', function () {
        it('renders file-preview component with image type', function () {
            $html = View::make('daisy::components.ui.data-display.file-preview', [
                'url' => 'https://example.com/image.jpg',
                'name' => 'test-image.jpg',
                'type' => 'image',
            ])->render();

            expect($html)
                ->toContain('file-preview')
                ->toContain('test-image.jpg')
                ->toContain('https://example.com/image.jpg')
                ->toContain('img');
        });

        it('renders file-preview component with video type', function () {
            $html = View::make('daisy::components.ui.data-display.file-preview', [
                'url' => 'https://example.com/video.mp4',
                'name' => 'test-video.mp4',
                'type' => 'video',
            ])->render();

            expect($html)
                ->toContain('file-preview')
                ->toContain('test-video.mp4')
                ->toContain('video');
        });

        it('renders file-preview component with pdf type', function () {
            $html = View::make('daisy::components.ui.data-display.file-preview', [
                'url' => 'https://example.com/document.pdf',
                'name' => 'test-document.pdf',
                'type' => 'pdf',
            ])->render();

            expect($html)
                ->toContain('file-preview')
                ->toContain('test-document.pdf')
                ->toContain('bi-file-pdf');
        });

        it('renders file-preview component with auto-detected type from extension', function () {
            $html = View::make('daisy::components.ui.data-display.file-preview', [
                'url' => 'https://example.com/image.png',
                'name' => 'test.png',
            ])->render();

            expect($html)
                ->toContain('file-preview')
                ->toContain('img');
        });

        it('renders file-preview component with different sizes', function () {
            $html = View::make('daisy::components.ui.data-display.file-preview', [
                'url' => 'https://example.com/image.jpg',
                'type' => 'image',
                'size' => 'lg',
            ])->render();

            expect($html)
                ->toContain('file-preview')
                ->toContain('max-w-96');
        });

        it('renders file-preview component without download button when downloadable is false', function () {
            $html = View::make('daisy::components.ui.data-display.file-preview', [
                'url' => 'https://example.com/image.jpg',
                'type' => 'image',
                'downloadable' => false,
            ])->render();

            expect($html)
                ->toContain('file-preview')
                ->not->toContain('file-download');
        });
    });
});
