@props([
    'conversation' => null,
    'showBackButton' => false,
    'backUrl' => null,
    // Data accessors
    'conversationIdKey' => 'id',
    'conversationNameKey' => 'name',
    'conversationAvatarKey' => 'avatar',
    'conversationIsOnlineKey' => 'isOnline',
])

@php
    if (!$conversation) {
        return;
    }

    $id = data_get($conversation, $conversationIdKey);
    $name = data_get($conversation, $conversationNameKey, '');
    $avatar = data_get($conversation, $conversationAvatarKey);
    $isOnline = data_get($conversation, $conversationIsOnlineKey, false);
@endphp

<div {{ $attributes->merge(['class' => 'chat-header flex items-center gap-2 sm:gap-3 p-3 sm:p-4 border-b bg-base-100']) }}>
    {{-- Bouton pour ouvrir la sidebar en mobile --}}
    <label for="chat-sidebar-drawer" class="btn btn-ghost btn-sm btn-circle lg:hidden drawer-button">
        <x-icon name="bi-list" class="w-5 h-5" />
    </label>

    @if($showBackButton)
        <x-daisy::ui.inputs.button
            tag="a"
            :href="$backUrl ?? '#'"
            variant="ghost"
            size="sm"
            circle
            class="lg:hidden"
        >
            <x-icon name="bi-arrow-left" class="w-5 h-5" />
        </x-daisy::ui.inputs.button>
    @endif

    @if($avatar || $name)
        <div class="flex-shrink-0">
            <x-daisy::ui.data-display.avatar
                :src="$avatar"
                :placeholder="$name ? substr($name, 0, 1) : 'U'"
                size="md"
                :status="$isOnline ? 'online' : 'offline'"
            />
        </div>
    @endif

    <div class="flex-1 min-w-0">
        @if($name)
            <div class="font-semibold text-sm sm:text-base text-base-content truncate">
                {{ $name }}
            </div>
        @endif
        @if($isOnline !== null)
            <div class="text-xs text-base-content opacity-70">
                {{ $isOnline ? __('chat.online') : __('chat.offline') }}
            </div>
        @endif
    </div>
</div>

