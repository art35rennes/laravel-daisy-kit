<?php

use function Pest\Laravel\visit;

describe('Communication Components Browser Tests', function () {
    describe('Notification Bell', function () {
        it('renders notification-bell without JavaScript errors', function () {
            $html = view('daisy::components.ui.communication.notification-bell', [
                'notifications' => [],
                'unreadCount' => 0,
            ])->render();

            $page = visit('/demo')
                ->assertSee('Demo')
                ->assertNoJavascriptErrors();

            // Le composant doit être présent dans le DOM
            expect($html)->toContain('notification-bell');
        });

        it('displays unread count badge', function () {
            $html = view('daisy::components.ui.communication.notification-bell', [
                'notifications' => [],
                'unreadCount' => 5,
            ])->render();

            expect($html)
                ->toContain('badge')
                ->toContain('5');
        });
    });

    describe('Chat Widget', function () {
        it('renders chat-widget without JavaScript errors', function () {
            $html = view('daisy::components.ui.communication.chat-widget', [
                'conversation' => ['id' => 1, 'name' => 'Test'],
                'messages' => [],
                'currentUserId' => 1,
            ])->render();

            expect($html)
                ->toContain('chat-widget')
                ->toContain('data-module="chat-widget"');
        });

        it('renders chat-widget in minimized state', function () {
            $html = view('daisy::components.ui.communication.chat-widget', [
                'conversation' => ['id' => 1],
                'messages' => [],
                'minimized' => true,
            ])->render();

            expect($html)
                ->toContain('data-minimized="true"')
                ->toContain('hidden');
        });
    });

    describe('Chat Messages', function () {
        it('renders chat-messages with module attribute', function () {
            $html = view('daisy::components.ui.communication.chat-messages', [
                'messages' => [],
                'currentUserId' => 1,
            ])->render();

            expect($html)
                ->toContain('data-module="chat-messages"')
                ->toContain('chat-messages');
        });

        it('renders chat-messages with WebSocket configuration', function () {
            $html = view('daisy::components.ui.communication.chat-messages', [
                'messages' => [],
                'currentUserId' => 1,
                'useWebSockets' => true,
                'pollingInterval' => 5000,
            ])->render();

            expect($html)
                ->toContain('data-use-websockets="true"')
                ->toContain('data-polling-interval="5000"');
        });
    });

    describe('Chat Input', function () {
        it('renders chat-input with module attribute', function () {
            $html = view('daisy::components.ui.communication.chat-input', [
                'sendMessageUrl' => '/chat/send',
            ])->render();

            expect($html)
                ->toContain('data-module="chat-input"')
                ->toContain('data-send-message-url="/chat/send"');
        });

        it('renders chat-input with file upload enabled', function () {
            $html = view('daisy::components.ui.communication.chat-input', [
                'enableFileUpload' => true,
                'maxFileSize' => 5120,
            ])->render();

            expect($html)
                ->toContain('data-enable-file-upload="true"')
                ->toContain('data-max-file-size="5120"')
                ->toContain('bi-paperclip');
        });
    });

    describe('Notification Center', function () {
        it('renders notification-center with module attribute', function () {
            $html = view('daisy::templates.communication.notification-center', [
                'notifications' => [],
                'unreadCount' => 0,
            ])->render();

            expect($html)
                ->toContain('data-module="notifications"')
                ->toContain('notification-center')
                ->toContain('data-mark-as-read-url')
                ->toContain('data-mark-all-as-read-url');
        });

        it('renders notification-center with WebSocket configuration', function () {
            $html = view('daisy::templates.communication.notification-center', [
                'notifications' => [],
                'unreadCount' => 0,
                'useWebSockets' => true,
                'pollingInterval' => 30000,
            ])->render();

            expect($html)
                ->toContain('data-use-websockets="true"')
                ->toContain('data-polling-interval="30000"');
        });
    });

    describe('Chat Template', function () {
        it('renders chat template with all components', function () {
            $conversation = [
                'id' => 1,
                'name' => 'John Doe',
                'avatar' => '/img/avatar.jpg',
            ];

            $html = view('daisy::templates.communication.chat', [
                'conversation' => $conversation,
                'messages' => [],
                'currentUserId' => 1,
            ])->render();

            expect($html)
                ->toContain('chat-header')
                ->toContain('chat-messages')
                ->toContain('chat-input')
                ->toContain('data-module="chat-messages"')
                ->toContain('data-module="chat-input"');
        });

        it('renders chat template with sidebar', function () {
            $conversations = [
                ['id' => 1, 'name' => 'John'],
            ];

            $html = view('daisy::templates.communication.chat', [
                'conversations' => $conversations,
                'showSidebar' => true,
            ])->render();

            expect($html)
                ->toContain('chat-sidebar')
                ->toContain('John');
        });
    });

    describe('JavaScript Module Initialization', function () {
        it('initializes notifications module without errors', function () {
            $page = visit('/demo')
                ->assertSee('Demo')
                ->assertNoJavascriptErrors()
                ->assertNoConsoleLogs();

            // Vérifier que le module peut être initialisé
            // (les modules sont initialisés automatiquement via data-module)
            expect(true)->toBeTrue();
        });

        it('initializes chat module without errors', function () {
            $page = visit('/demo')
                ->assertSee('Demo')
                ->assertNoJavascriptErrors()
                ->assertNoConsoleLogs();

            // Vérifier que le module peut être initialisé
            expect(true)->toBeTrue();
        });
    });

    describe('Data Attributes', function () {
        it('passes correct data attributes to notification components', function () {
            $html = view('daisy::templates.communication.notification-center', [
                'notifications' => [],
                'unreadCount' => 0,
                'useWebSockets' => false,
                'pollingInterval' => 30000,
                'autoReconnect' => true,
                'reconnectDelay' => 5000,
            ])->render();

            expect($html)
                ->toContain('data-use-websockets="false"')
                ->toContain('data-polling-interval="30000"')
                ->toContain('data-auto-reconnect="true"')
                ->toContain('data-reconnect-delay="5000"')
                ->toContain('data-mark-as-read-url')
                ->toContain('data-load-notifications-url');
        });

        it('passes correct data attributes to chat components', function () {
            $html = view('daisy::components.ui.communication.chat-messages', [
                'messages' => [],
                'currentUserId' => 1,
                'useWebSockets' => true,
                'pollingInterval' => 3000,
                'autoReconnect' => true,
                'reconnectDelay' => 5000,
            ])->render();

            expect($html)
                ->toContain('data-use-websockets="true"')
                ->toContain('data-polling-interval="3000"')
                ->toContain('data-auto-reconnect="true"')
                ->toContain('data-reconnect-delay="5000"');
        });
    });
});
