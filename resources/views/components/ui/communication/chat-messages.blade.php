@props([
    'messages' => [],
    'currentUserId' => null,
    'showTypingIndicator' => true,
    'typingUsers' => [],
    'loadMessagesUrl' => null,
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
    'messageAttachmentKey' => 'attachment',
    'messageAttachmentsKey' => 'attachments',
    // Module override
    'module' => 'chat-messages',
])

@php
    $loadMessagesUrl = $loadMessagesUrl ?? (Route::has('chat.messages') ? route('chat.messages', ':conversationId') : '#');
@endphp

<div 
    {{ $attributes->merge(['class' => 'chat-messages flex flex-col overflow-y-auto p-3 sm:p-4 space-y-3 sm:space-y-4']) }}
    data-module="{{ $module }}"
    data-load-messages-url="{{ $loadMessagesUrl }}"
    data-current-user-id="{{ $currentUserId }}"
    data-use-websockets="{{ $useWebSockets ? 'true' : 'false' }}"
    data-polling-interval="{{ $pollingInterval }}"
    data-auto-reconnect="{{ $autoReconnect ? 'true' : 'false' }}"
    data-reconnect-delay="{{ $reconnectDelay }}"
    data-message-id-key="{{ $messageIdKey }}"
    data-message-user-id-key="{{ $messageUserIdKey }}"
    data-message-content-key="{{ $messageContentKey }}"
    data-message-created-at-key="{{ $messageCreatedAtKey }}"
    data-message-user-name-key="{{ $messageUserNameKey }}"
    data-message-user-avatar-key="{{ $messageUserAvatarKey }}"
>
    @if(empty($messages))
        <x-daisy::ui.feedback.empty-state
            icon="bi-chat-dots"
            :title="__('chat.no_messages')"
            size="sm"
        />
    @else
        @foreach($messages as $message)
            @php
                // Extraction des propriétés du message via data_get (support des clés personnalisables).
                $messageId = data_get($message, $messageIdKey);
                $messageUserId = data_get($message, $messageUserIdKey);
                $messageContent = data_get($message, $messageContentKey, '');
                $messageCreatedAt = data_get($message, $messageCreatedAtKey);
                $messageUserName = data_get($message, $messageUserNameKey, '');
                $messageUserAvatar = data_get($message, $messageUserAvatarKey);
                
                // Récupération des pièces jointes : support d'une pièce unique (attachment) ou multiple (attachments).
                $attachment = data_get($message, $messageAttachmentKey);
                $attachments = data_get($message, $messageAttachmentsKey, []);
                // Normalisation : si une seule pièce est fournie, la convertir en tableau.
                if ($attachment && !$attachments) {
                    $attachments = [$attachment];
                }
                
                // Détermination de l'alignement : messages de l'utilisateur courant à droite (end), autres à gauche (start).
                $isCurrentUser = $currentUserId && (string) $messageUserId === (string) $currentUserId;
                $align = $isCurrentUser ? 'end' : 'start';
                
                // Formatage de la date : conversion en Carbon si string, formatage en H:i (ex: "14:30").
                $dateFormatted = null;
                if ($messageCreatedAt) {
                    try {
                        $date = is_string($messageCreatedAt) ? \Carbon\Carbon::parse($messageCreatedAt) : $messageCreatedAt;
                        $dateFormatted = $date->format('H:i');
                    } catch (\Exception $e) {
                        // Fallback : utiliser la valeur brute si le parsing échoue.
                        $dateFormatted = $messageCreatedAt;
                    }
                }
            @endphp

            <x-daisy::ui.communication.chat-bubble
                :align="$align"
                :name="$isCurrentUser ? null : $messageUserName"
                :time="$dateFormatted"
                :color="$isCurrentUser ? 'primary' : null"
            >
                <x-slot:avatar>
                    @if($messageUserAvatar || $messageUserName)
                        <x-daisy::ui.data-display.avatar
                            :src="$messageUserAvatar"
                            :placeholder="$messageUserName ? substr($messageUserName, 0, 1) : 'U'"
                            size="sm"
                        />
                    @endif
                </x-slot:avatar>

                <div class="space-y-2">
                    @if(!empty($attachments))
                        <div class="space-y-2">
                            @foreach($attachments as $att)
                                @php
                                    $attUrl = is_array($att) ? data_get($att, 'url') : $att;
                                    $attName = is_array($att) ? data_get($att, 'name') : null;
                                    $attType = is_array($att) ? data_get($att, 'type') : null;
                                    $attSize = is_array($att) ? data_get($att, 'size') : null;
                                @endphp
                                @if($attUrl)
                                    <x-daisy::ui.data-display.file-preview
                                        :url="$attUrl"
                                        :name="$attName"
                                        :type="$attType"
                                        :file-size="$attSize"
                                        size="sm"
                                    />
                                @endif
                            @endforeach
                        </div>
                    @endif
                    
                    @if($messageContent)
                        <div>{{ $messageContent }}</div>
                    @endif
                </div>
            </x-daisy::ui.communication.chat-bubble>
        @endforeach
    @endif

    @if($showTypingIndicator && !empty($typingUsers))
        <div class="chat-typing-indicator flex items-center gap-2 text-sm text-base-content opacity-70">
            <x-daisy::ui.feedback.loading shape="dots" size="sm" />
            <span>
                @foreach($typingUsers as $index => $user)
                    {{ $index > 0 ? ', ' : '' }}{{ data_get($user, 'name', 'Quelqu\'un') }}
                @endforeach
                {{ __('chat.typing', ['name' => '']) }}
            </span>
        </div>
    @endif
</div>

