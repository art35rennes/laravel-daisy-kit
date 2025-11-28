@props([
    'notifications' => [],
    'unreadCount' => null,
    'position' => 'dropdown-end', // Position du dropdown
    'showMarkAllRead' => true,
    'markAllAsReadUrl' => null,
    'viewAllUrl' => null,
    // Toutes les props de notification-list
    'groupByDate' => false,
    'showActions' => false,
    // Data accessors
    'notificationIdKey' => 'id',
    'notificationTypeKey' => 'type',
    'notificationDataKey' => 'data',
    'notificationReadAtKey' => 'read_at',
    'notificationCreatedAtKey' => 'created_at',
])

@php
    $markAllAsReadUrl = $markAllAsReadUrl ?? (Route::has('notifications.read-all') ? route('notifications.read-all') : '#');
    $viewAllUrl = $viewAllUrl ?? (Route::has('notifications.index') ? route('notifications.index') : '#');
    
    // Calculer le nombre de non lues si non fourni
    if (is_null($unreadCount) && !empty($notifications)) {
        $unreadCount = collect($notifications)->filter(function ($notification) use ($notificationReadAtKey) {
            $readAt = data_get($notification, $notificationReadAtKey);
            return empty($readAt);
        })->count();
    }
    
    $hasUnread = $unreadCount > 0;
    $end = str_contains($position, 'end');
@endphp

<x-daisy::ui.overlay.dropdown
    :end="$end"
    type="card"
    content-class="dropdown-content mt-4 sm:mt-5 z-[1] p-0 shadow bg-base-100 rounded-box overflow-visible"
    card-body-class="p-0"
    button-class="btn btn-ghost btn-circle relative"
    :button-circle="true"
>
    <x-slot:trigger>
        <div class="relative">
            <x-icon name="bi-bell" class="w-5 h-5" />
            @if($hasUnread)
                <span class="absolute -top-1 -right-1">
                    <x-daisy::ui.data-display.badge
                        size="xs"
                        color="primary"
                        class="p-0 w-5 h-5 flex items-center justify-center"
                    >
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </x-daisy::ui.data-display.badge>
                </span>
            @endif
        </div>
    </x-slot:trigger>

    <div class="w-80 sm:w-96 max-w-[calc(100vw-2rem)] p-3 sm:p-4 space-y-4">
        <div class="p-3 sm:p-4 card-border rounded-box flex items-center justify-between gap-2">
            <h3 class="font-semibold text-sm sm:text-base truncate">
                {{ __('notifications.notifications') }}
            </h3>
            @if($hasUnread && $showMarkAllRead && $markAllAsReadUrl !== '#')
                <x-daisy::ui.inputs.button
                    type="button"
                    variant="ghost"
                    size="xs"
                    class="shrink-0"
                    data-action="mark-all-read"
                    data-url="{{ $markAllAsReadUrl }}"
                >
                    <span class="hidden sm:inline">{{ __('notifications.mark_all_as_read') }}</span>
                    <span class="sm:hidden">{{ __('notifications.mark_all_as_read') }}</span>
                </x-daisy::ui.inputs.button>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto px-3 sm:px-4 py-4 sm:py-6 bg-base-100 rounded-box card-border">
            @if(empty($notifications))
                <x-daisy::ui.feedback.empty-state
                    icon="bi-bell"
                    :title="__('notifications.no_notifications')"
                    size="xs"
                    class="text-center"
                />
            @else
                <x-daisy::ui.communication.notification-list
                    :notifications="$notifications"
                    :group-by-date="$groupByDate"
                    :show-actions="$showActions"
                    :notification-id-key="$notificationIdKey"
                    :notification-type-key="$notificationTypeKey"
                    :notification-data-key="$notificationDataKey"
                    :notification-read-at-key="$notificationReadAtKey"
                    :notification-created-at-key="$notificationCreatedAtKey"
                />
            @endif
        </div>

        @if($viewAllUrl !== '#')
            <div class="p-3 sm:p-4 card-border rounded-box text-center">
                <x-daisy::ui.inputs.button
                    tag="a"
                    :href="$viewAllUrl"
                    variant="ghost"
                    size="sm"
                    block
                >
                    {{ __('notifications.view_all') }}
                </x-daisy::ui.inputs.button>
            </div>
        @endif
    </div>
</x-daisy::ui.overlay.dropdown>

