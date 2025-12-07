@props([
    'title' => __('chat.messages'),
    'theme' => null,
    'conversation' => null,
    'conversations' => [],
    'messages' => [],
    'currentUser' => null,
    'currentUserId' => null,
    'showSidebar' => true,
    'showUserList' => true,
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
    'conversationsUrl' => null,
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
    'conversationLastMessageKey' => 'lastMessage',
    'conversationUnreadCountKey' => 'unreadCount',
    'messageIdKey' => 'id',
    'messageUserIdKey' => 'user_id',
    'messageContentKey' => 'content',
    'messageCreatedAtKey' => 'created_at',
    'messageUserNameKey' => 'user_name',
    'messageUserAvatarKey' => 'user_avatar',
])

<x-daisy::layout.app :title="$title" :theme="$theme" :container="false">
    <x-daisy::ui.partials.theme-selector position="fixed" placement="top-right" />
    @if($showSidebar && !empty($conversations))
        <x-daisy::ui.overlay.drawer id="chat-sidebar-drawer" :responsiveOpen="'lg'" :sideIsMenu="false" sideClass="w-80" :fullHeight="false">
            <x-slot:content>
                <div class="flex h-screen overflow-hidden">
                    <x-daisy::ui.communication.conversation-view
                        :conversation="$conversation"
                        :messages="$messages"
                        :current-user-id="$currentUserId"
                        :show-back-button="$showBackButton"
                        :back-url="$backUrl"
                        :show-typing-indicator="$showTypingIndicator"
                        :typing-users="$typingUsers"
                        :enable-file-upload="$enableFileUpload"
                        :max-file-size="$maxFileSize"
                        :multiple-files="$multipleFiles"
                        :show-file-preview="$showFilePreview"
                        :accepted-file-types="$acceptedFileTypes"
                        :placeholder="$placeholder"
                        :send-message-url="$sendMessageUrl"
                        :typing-url="$typingUrl"
                        :load-messages-url="$loadMessagesUrl"
                        :use-websockets="$useWebSockets"
                        :polling-interval="$pollingInterval"
                        :auto-reconnect="$autoReconnect"
                        :reconnect-delay="$reconnectDelay"
                        :conversation-id-key="$conversationIdKey"
                        :conversation-name-key="$conversationNameKey"
                        :conversation-avatar-key="$conversationAvatarKey"
                        :conversation-is-online-key="$conversationIsOnlineKey"
                        :message-id-key="$messageIdKey"
                        :message-user-id-key="$messageUserIdKey"
                        :message-content-key="$messageContentKey"
                        :message-created-at-key="$messageCreatedAtKey"
                        :message-user-name-key="$messageUserNameKey"
                        :message-user-avatar-key="$messageUserAvatarKey"
                    />
                </div>
            </x-slot:content>
            <x-slot:side>
                {{-- Sidebar mobile (dans le drawer) --}}
                <x-daisy::ui.communication.chat-sidebar
                    :conversations="$conversations"
                    :current-conversation-id="data_get($conversation, $conversationIdKey)"
                    :show-user-list="$showUserList"
                    :conversations-url="$conversationsUrl"
                    :conversation-id-key="$conversationIdKey"
                    :conversation-name-key="$conversationNameKey"
                    :conversation-avatar-key="$conversationAvatarKey"
                    :conversation-last-message-key="$conversationLastMessageKey"
                    :conversation-unread-count-key="$conversationUnreadCountKey"
                    :conversation-is-online-key="$conversationIsOnlineKey"
                />
            </x-slot:side>
        </x-daisy::ui.overlay.drawer>

        @push('scripts')
        <script>
            // Fermer le drawer en mobile quand on clique sur une conversation
            document.addEventListener('DOMContentLoaded', function() {
                const drawerToggle = document.getElementById('chat-sidebar-drawer');
                const conversationRows = document.querySelectorAll('.chat-sidebar [data-conversation-id]');
                
                conversationRows.forEach(row => {
                    row.addEventListener('click', function() {
                        // Fermer le drawer en mobile uniquement
                        if (window.innerWidth < 1024 && drawerToggle) {
                            drawerToggle.checked = false;
                        }
                    });
                });
            });
        </script>
        @endpush
    @else
        <div class="flex h-screen overflow-hidden">
            <x-daisy::ui.communication.conversation-view
                :conversation="$conversation"
                :messages="$messages"
                :current-user-id="$currentUserId"
                :show-back-button="$showBackButton"
                :back-url="$backUrl"
                :show-typing-indicator="$showTypingIndicator"
                :typing-users="$typingUsers"
                :enable-file-upload="$enableFileUpload"
                :max-file-size="$maxFileSize"
                :multiple-files="$multipleFiles"
                :show-file-preview="$showFilePreview"
                :accepted-file-types="$acceptedFileTypes"
                :placeholder="$placeholder"
                :send-message-url="$sendMessageUrl"
                :typing-url="$typingUrl"
                :load-messages-url="$loadMessagesUrl"
                :use-websockets="$useWebSockets"
                :polling-interval="$pollingInterval"
                :auto-reconnect="$autoReconnect"
                :reconnect-delay="$reconnectDelay"
                :conversation-id-key="$conversationIdKey"
                :conversation-name-key="$conversationNameKey"
                :conversation-avatar-key="$conversationAvatarKey"
                :conversation-is-online-key="$conversationIsOnlineKey"
                :message-id-key="$messageIdKey"
                :message-user-id-key="$messageUserIdKey"
                :message-content-key="$messageContentKey"
                :message-created-at-key="$messageCreatedAtKey"
                :message-user-name-key="$messageUserNameKey"
                :message-user-avatar-key="$messageUserAvatarKey"
            />
        </div>
    @endif
</x-daisy::layout.app>


