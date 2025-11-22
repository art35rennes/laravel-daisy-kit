@props([
    'conversation' => null,
    'messages' => [],
    'currentUserId' => null,
    // Toutes les props des composants enfants
    'showBackButton' => false,
    'backUrl' => null,
    'showTypingIndicator' => true,
    'typingUsers' => [],
    'enableFileUpload' => false,
    'maxFileSize' => 5120,
    'multipleFiles' => false,
    'showFilePreview' => true,
    'acceptedFileTypes' => 'image/*,application/pdf,.doc,.docx',
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

<div {{ $attributes->merge(['class' => 'flex-1 flex flex-col min-w-0 h-full overflow-hidden']) }} data-conversation-id="{{ data_get($conversation, $conversationIdKey) }}">
    @if($conversation)
        <x-daisy::ui.communication.chat-header
            :conversation="$conversation"
            :show-back-button="$showBackButton"
            :back-url="$backUrl"
            :conversation-id-key="$conversationIdKey"
            :conversation-name-key="$conversationNameKey"
            :conversation-avatar-key="$conversationAvatarKey"
            :conversation-is-online-key="$conversationIsOnlineKey"
        />

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

        <x-daisy::ui.communication.chat-input
            :send-message-url="$sendMessageUrl"
            :typing-url="$typingUrl"
            :enable-file-upload="$enableFileUpload"
            :max-file-size="$maxFileSize"
            :multiple-files="$multipleFiles"
            :show-file-preview="$showFilePreview"
            :accepted-file-types="$acceptedFileTypes"
            :placeholder="$placeholder"
            :use-websockets="$useWebSockets"
            :auto-reconnect="$autoReconnect"
            :conversation-id-key="$conversationIdKey"
        />
    @else
        <div class="flex-1 flex items-center justify-center">
            <x-daisy::ui.feedback.empty-state
                icon="bi-chat-dots"
                :title="__('chat.select_conversation')"
                size="md"
            />
        </div>
    @endif
</div>

