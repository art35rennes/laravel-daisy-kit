@props([
    'conversation' => null,
    'messages' => [],
    'currentUserId' => null,
    'position' => 'bottom-right', // bottom-right, bottom-left, top-right, top-left
    'minimized' => false,
    'showHeader' => true,
    'showInput' => true,
    // Toutes les props des composants enfants
    'showBackButton' => false,
    'backUrl' => null,
    'showTypingIndicator' => true,
    'typingUsers' => [],
    'enableFileUpload' => false,
    'maxFileSize' => 5120,
    'placeholder' => __('chat.type_message'),
    // Routes
    'sendMessageUrl' => null,
    'typingUrl' => null,
    'loadMessagesUrl' => null,
    // Options REST/WebSocket
    'useWebSockets' => false,
    'pollingInterval' => 3000,
    'autoReconnect' => true,
    'reconnectDelay' => 5000,
    // Data accessors
    'conversationIdKey' => 'id',
    'conversationNameKey' => 'name',
    'conversationAvatarKey' => 'avatar',
    'conversationIsOnlineKey' => 'isOnline',
    'messageIdKey' => 'id',
    'messageUserIdKey' => 'user_id',
    'messageContentKey' => 'content',
    'messageCreatedAtKey' => 'created_at',
    'messageUserNameKey' => 'user_name',
    'messageUserAvatarKey' => 'user_avatar',
])

@php
    $positionClasses = [
        'bottom-right' => 'bottom-4 right-4',
        'bottom-left' => 'bottom-4 left-4',
        'top-right' => 'top-4 right-4',
        'top-left' => 'top-4 left-4',
    ];
    $positionClass = $positionClasses[$position] ?? 'bottom-4 right-4';
@endphp

<div 
    class="chat-widget fixed {{ $positionClass }} z-50"
    data-module="chat-widget"
    data-position="{{ $position }}"
    data-minimized="{{ $minimized ? 'true' : 'false' }}"
>
    {{-- Bouton flottant (quand minimisé) --}}
    <x-daisy::ui.inputs.button
        type="button"
        variant="solid"
        color="primary"
        size="lg"
        circle
        class="shadow-lg chat-widget-toggle"
        data-widget-toggle
    >
        <x-icon name="bi-chat-dots" class="w-6 h-6" />
    </x-daisy::ui.inputs.button>

    {{-- Widget (quand ouvert) --}}
    <div 
        class="chat-widget-panel hidden bg-base-100 rounded-box shadow-2xl flex flex-col w-[calc(100vw-2rem)] sm:w-96 h-[600px] max-h-[calc(100vh-2rem)]"
        data-widget-panel
    >
        @if($showHeader)
            <x-daisy::ui.communication.chat-header
                :conversation="$conversation"
                :show-back-button="$showBackButton"
                :back-url="$backUrl"
                :conversation-id-key="$conversationIdKey"
                :conversation-name-key="$conversationNameKey"
                :conversation-avatar-key="$conversationAvatarKey"
                :conversation-is-online-key="$conversationIsOnlineKey"
            />
        @endif

        <x-daisy::ui.communication.chat-messages
            :messages="$messages"
            :current-user-id="$currentUserId"
            :show-typing-indicator="$showTypingIndicator"
            :typing-users="$typingUsers"
            :load-messages-url="$loadMessagesUrl"
            :use-websockets="$useWebSockets"
            :polling-interval="$pollingInterval"
            :auto-reconnect="$autoReconnect"
            :reconnect-delay="$reconnectDelay"
            :message-id-key="$messageIdKey"
            :message-user-id-key="$messageUserIdKey"
            :message-content-key="$messageContentKey"
            :message-created-at-key="$messageCreatedAtKey"
            :message-user-name-key="$messageUserNameKey"
            :message-user-avatar-key="$messageUserAvatarKey"
            class="flex-1 min-h-0"
        />

        @if($showInput)
            <x-daisy::ui.communication.chat-input
                :send-message-url="$sendMessageUrl"
                :typing-url="$typingUrl"
                :enable-file-upload="$enableFileUpload"
                :max-file-size="$maxFileSize"
                :placeholder="$placeholder"
                :use-websockets="$useWebSockets"
                :auto-reconnect="$autoReconnect"
                :conversation-id-key="$conversationIdKey"
            />
        @endif

        {{-- Bouton minimiser --}}
        <x-daisy::ui.inputs.button
            type="button"
            variant="ghost"
            size="sm"
            circle
            class="absolute top-2 right-2"
            data-widget-minimize
            title="{{ __('chat.minimize') }}"
        >
            <x-icon name="bi-x" class="w-4 h-4" />
        </x-daisy::ui.inputs.button>
    </div>
</div>

