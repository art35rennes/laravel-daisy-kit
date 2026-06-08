@props([
    'conversations' => [],
    'currentConversationId' => null,
    'showUserList' => true,
    'onlineUsers' => [],
    'conversationsUrl' => null,
    // Data accessors
    'conversationIdKey' => 'id',
    'conversationNameKey' => 'name',
    'conversationAvatarKey' => 'avatar',
    'conversationLastMessageKey' => 'lastMessage',
    'conversationUnreadCountKey' => 'unreadCount',
    'conversationIsOnlineKey' => 'isOnline',
])

@php
    $normalizeEndpoint = function($url, $fallback = '#') {
        if (!is_string($url) && !$url instanceof \Stringable) {
            return $fallback;
        }

        $url = trim((string) $url);

        if ($url === '') {
            return $fallback;
        }

        if ($url === '#' || str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return $url;
        }

        return preg_match('/^https?:\/\//i', $url) === 1 ? $url : $fallback;
    };

    $conversationsUrl = $conversationsUrl ?? (Route::has('chat.conversations') ? route('chat.conversations') : '#');
    $conversationsUrl = $normalizeEndpoint($conversationsUrl);
@endphp

<div {{ $attributes->merge(['class' => 'chat-sidebar flex flex-col h-full border-r bg-base-100']) }}>
    {{-- Recherche --}}
    <div class="p-3 sm:p-4 border-b">
        <x-daisy::ui.inputs.input
            type="text"
            :placeholder="__('daisy::chat.search_conversations')"
            class="w-full"
            data-search-input
        />
    </div>

    {{-- Liste des conversations --}}
    <div class="flex-1 overflow-y-auto">
        @if(empty($conversations))
            <x-daisy::ui.feedback.empty-state
                icon="bi-chat-dots"
                :title="__('daisy::chat.no_conversations')"
                size="sm"
            />
        @else
            <x-daisy::ui.layout.list>
                @foreach($conversations as $conversation)
                    @php
                        $id = data_get($conversation, $conversationIdKey);
                        $name = data_get($conversation, $conversationNameKey, '');
                        $avatar = data_get($conversation, $conversationAvatarKey);
                        $lastMessage = data_get($conversation, $conversationLastMessageKey);
                        $unreadCount = data_get($conversation, $conversationUnreadCountKey, 0);
                        $isOnline = data_get($conversation, $conversationIsOnlineKey, false);
                        $isActive = $currentConversationId && (string) $id === (string) $currentConversationId;
                    @endphp

                    <x-daisy::ui.layout.list-row
                        class="cursor-pointer hover:bg-base-200 transition-colors {{ $isActive ? 'bg-base-200' : '' }}"
                        data-conversation-id="{{ $id }}"
                    >
                        <div class="flex items-center gap-3 w-full">
                            <x-daisy::ui.data-display.avatar
                                :src="$avatar"
                                :placeholder="$name ? substr($name, 0, 1) : 'U'"
                                size="md"
                                :status="$isOnline ? 'online' : 'offline'"
                            />
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-sm text-base-content">
                                    {{ $name }}
                                </div>
                                @if($lastMessage)
                                    <div class="text-xs text-base-content opacity-70 truncate">
                                        {{ $lastMessage }}
                                    </div>
                                @endif
                            </div>
                            @if($unreadCount > 0)
                                <x-daisy::ui.data-display.badge
                                    size="sm"
                                    color="primary"
                                >
                                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                </x-daisy::ui.data-display.badge>
                            @endif
                        </div>
                    </x-daisy::ui.layout.list-row>
                @endforeach
            </x-daisy::ui.layout.list>
        @endif
    </div>

    {{-- Liste des utilisateurs en ligne --}}
    @if($showUserList && !empty($onlineUsers))
        <div class="p-3 sm:p-4 border-t">
            <div class="text-xs font-semibold text-base-content opacity-70 mb-2">
                {{ __('daisy::chat.users_online') }}
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach($onlineUsers as $user)
                    @php
                        $userId = data_get($user, 'id');
                        $userName = data_get($user, 'name', '');
                        $userAvatar = data_get($user, 'avatar');
                    @endphp
                    <x-daisy::ui.data-display.avatar
                        :src="$userAvatar"
                        :placeholder="$userName ? substr($userName, 0, 1) : 'U'"
                        size="sm"
                        status="online"
                    />
                @endforeach
            </div>
        </div>
    @endif
</div>
