@props([
    'title' => __('notifications.notifications'),
    'theme' => null,
    'notifications' => [],
    'unreadCount' => null,
    'showFilters' => true,
    'showMarkAllRead' => true,
    'showDelete' => true,
    'groupByDate' => true,
    'pagination' => true,
    // Filtres
    'types' => [],
    'currentFilter' => 'all',
    // Routes
    'markAsReadUrl' => null,
    'markAllAsReadUrl' => null,
    'deleteUrl' => null,
    'loadNotificationsUrl' => null,
    'preferencesUrl' => null,
    // Options REST/WebSocket
    'useWebSockets' => false,
    'pollingInterval' => 30000,
    'autoReconnect' => true,
    'reconnectDelay' => 5000,
    // Données supplémentaires
    'digestTime' => '08:00',
    'userId' => null,
    // Data accessors
    'notificationIdKey' => 'id',
    'notificationTypeKey' => 'type',
    'notificationDataKey' => 'data',
    'notificationReadAtKey' => 'read_at',
    'notificationCreatedAtKey' => 'created_at',
])

@php
    $markAsReadUrl = $markAsReadUrl ?? (Route::has('notifications.read') ? route('notifications.read', ':id') : '#');
    $markAllAsReadUrl = $markAllAsReadUrl ?? (Route::has('notifications.read-all') ? route('notifications.read-all') : '#');
    $deleteUrl = $deleteUrl ?? (Route::has('notifications.delete') ? route('notifications.delete', ':id') : '#');
    $loadNotificationsUrl = $loadNotificationsUrl ?? (Route::has('notifications.index') ? route('notifications.index') : '#');
    $preferencesUrl = $preferencesUrl ?? (Route::has('notifications.preferences') ? route('notifications.preferences') : null);

    $notificationsCollection = collect($notifications);

    if (is_null($unreadCount) && $notificationsCollection->isNotEmpty()) {
        $unreadCount = $notificationsCollection->filter(function ($notification) use ($notificationReadAtKey) {
            return empty(data_get($notification, $notificationReadAtKey));
        })->count();
    }

    $criticalPriorities = ['critical', 'urgent', 'high'];

    $criticalNotifications = $notificationsCollection
        ->filter(function ($notification) use ($notificationDataKey, $notificationReadAtKey, $criticalPriorities) {
            $priority = strtolower((string) data_get($notification, "{$notificationDataKey}.priority", 'normal'));
            $isCritical = in_array($priority, $criticalPriorities, true);
            $isUnread = empty(data_get($notification, $notificationReadAtKey));

            return $isCritical && $isUnread;
        })
        ->values();

    $focusNotifications = $criticalNotifications->take(3);

    $actionableCount = $notificationsCollection->filter(function ($notification) use ($notificationDataKey) {
        return filled(data_get($notification, "{$notificationDataKey}.action.label"));
    })->count();

    $dueSoonCount = $notificationsCollection->filter(function ($notification) use ($notificationDataKey) {
        $dueAt = data_get($notification, "{$notificationDataKey}.due_at");
        if (empty($dueAt)) {
            return false;
        }

        try {
            $dueDate = is_string($dueAt) ? \Carbon\Carbon::parse($dueAt) : $dueAt;
            return $dueDate->isFuture() && $dueDate->diffInHours(now()) <= 48;
        } catch (\Exception $exception) {
            return false;
        }
    })->count();

    $mentionCount = $notificationsCollection->filter(function ($notification) use ($notificationDataKey) {
        $category = strtolower((string) data_get($notification, "{$notificationDataKey}.category", ''));
        return in_array($category, ['mention', 'review', 'social'], true);
    })->count();

    $channelBreakdown = $notificationsCollection
        ->groupBy(function ($notification) use ($notificationDataKey) {
            return strtolower((string) data_get($notification, "{$notificationDataKey}.channel", 'in_app'));
        })
        ->map(function ($group) use ($notificationReadAtKey) {
            return [
                'count' => $group->count(),
                'unread' => $group->filter(function ($notification) use ($notificationReadAtKey) {
                    return empty(data_get($notification, $notificationReadAtKey));
                })->count(),
            ];
        })
        ->sortByDesc('count');

    $statCards = [
        [
            'label' => __('notifications.critical_alerts'),
            'value' => $criticalNotifications->count(),
            'count' => $criticalNotifications->count(),
            'icon' => 'bi-exclamation-triangle',
            'iconColor' => 'text-error',
        ],
        [
            'label' => __('notifications.actionable_notifications'),
            'value' => $actionableCount,
            'count' => $actionableCount,
            'icon' => 'bi-lightning-charge',
            'iconColor' => 'text-warning',
        ],
        [
            'label' => __('notifications.upcoming_followups'),
            'value' => $dueSoonCount,
            'count' => $dueSoonCount,
            'icon' => 'bi-calendar-event',
            'iconColor' => 'text-info',
        ],
        [
            'label' => __('notifications.mentions'),
            'value' => $mentionCount,
            'count' => $mentionCount,
            'icon' => 'bi-at',
            'iconColor' => 'text-secondary',
        ],
    ];
@endphp

<x-daisy::layout.app :title="$title" :theme="$theme" :container="true">
    <section class="notification-center max-w-6xl mx-auto px-4 sm:px-6 space-y-10">
        <header class="bg-base-100 rounded-box p-5 sm:p-8 shadow-lg space-y-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="min-w-0 space-y-3 flex-1">
                    <p class="text-xs uppercase tracking-wide text-primary font-semibold">
                        {{ __('notifications.control_room') }}
                    </p>
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-3xl font-semibold tracking-tight">{{ $title }}</h1>
                        @if(!is_null($unreadCount))
                            <div class="flex items-center gap-2" data-unread-count>
                                <x-daisy::ui.data-display.badge color="primary" size="lg">
                                    {{ $unreadCount }}
                                </x-daisy::ui.data-display.badge>
                                <span class="text-sm text-base-content/70">
                                    {{ trans_choice('notifications.unread_count', $unreadCount, ['count' => $unreadCount]) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <p class="text-sm text-base-content/70">
                        {{ __('notifications.center_helper') }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 shrink-0">
                    <x-daisy::ui.inputs.button
                        size="sm"
                        variant="ghost"
                        color="neutral"
                        :tag="$preferencesUrl ? 'a' : 'button'"
                        :href="$preferencesUrl"
                    >
                        <span class="flex items-center gap-2">
                            <x-icon name="bi-gear" class="w-4 h-4" />
                            {{ __('notifications.notification_preferences') }}
                        </span>
                    </x-daisy::ui.inputs.button>
                    @if($unreadCount > 0 && $showMarkAllRead && $markAllAsReadUrl !== '#')
                        <x-daisy::ui.inputs.button
                            size="sm"
                            variant="outline"
                            color="primary"
                            data-action="mark-all-read"
                            data-url="{{ $markAllAsReadUrl }}"
                        >
                            <span class="flex items-center gap-2">
                                <x-icon name="bi-check2-all" class="w-4 h-4" />
                                {{ __('notifications.mark_all_as_read') }}
                            </span>
                        </x-daisy::ui.inputs.button>
                    @endif
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach($statCards as $card)
                    <div class="rounded-box border border-base-300/60 bg-base-200/40 p-4 shadow-sm">
                        <div class="flex items-center justify-between text-sm text-base-content/70">
                            <span>{{ $card['label'] }}</span>
                            <x-icon :name="$card['icon']" class="w-5 h-5 {{ $card['iconColor'] }}" />
                        </div>
                        <div class="mt-3 text-3xl font-semibold tracking-tight text-base-content">
                            {{ $card['value'] }}
                        </div>
                        <p class="mt-1 text-xs text-base-content/60">
                            {{ trans_choice('notifications.channel_score', $card['count'], ['count' => $card['count']]) }}
                        </p>
                    </div>
                @endforeach
            </div>
        </header>

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <div class="space-y-8">
                <x-daisy::ui.layout.card
                    :title="__('notifications.focus_section_title')"
                    class="border border-error/30 bg-error/5 shadow-sm"
                >
                    <p class="text-sm text-base-content/70">
                        {{ __('notifications.focus_section_description') }}
                    </p>
                    <div class="mt-4 space-y-4">
                        @forelse($focusNotifications as $focusNotification)
                            @php
                                $focusData = data_get($focusNotification, $notificationDataKey, []);
                                $focusLink = data_get($focusData, 'action.url', data_get($focusData, 'link', '#'));
                                $focusMessage = data_get($focusData, 'message', __('notifications.new_notification'));
                                $focusCreatedAt = data_get($focusNotification, $notificationCreatedAtKey);
                                $focusDate = null;

                                if ($focusCreatedAt) {
                                    try {
                                        $focusCarbon = is_string($focusCreatedAt) ? \Carbon\Carbon::parse($focusCreatedAt) : $focusCreatedAt;
                                        $focusDate = $focusCarbon->diffForHumans();
                                    } catch (\Exception $exception) {
                                        $focusDate = $focusCreatedAt;
                                    }
                                }
                            @endphp
                            <div class="flex items-start gap-3">
                                <x-icon name="bi-exclamation-diamond" class="w-4 h-4 text-error mt-1" />
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-sm">{{ $focusMessage }}</p>
                                    @if($focusDate)
                                        <p class="text-xs text-base-content/60 mt-1">{{ $focusDate }}</p>
                                    @endif
                                </div>
                                @if($focusLink && $focusLink !== '#')
                                    <x-daisy::ui.inputs.button
                                        size="xs"
                                        variant="ghost"
                                        color="error"
                                        tag="a"
                                        :href="$focusLink"
                                    >
                                        {{ __('notifications.cta_view_details') }}
                                    </x-daisy::ui.inputs.button>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-base-content/70">
                                {{ __('notifications.empty_focus') }}
                            </p>
                        @endforelse
                    </div>
                </x-daisy::ui.layout.card>

                @if($showFilters && !empty($types))
                    <x-daisy::ui.communication.notification-filters
                        :types="$types"
                        :current-filter="$currentFilter"
                    />
                @endif

                <div
                    class="bg-base-100 rounded-box shadow-lg divide-y divide-base-200"
                    data-module="notifications"
                    data-use-websockets="{{ $useWebSockets ? 'true' : 'false' }}"
                    data-polling-interval="{{ $pollingInterval }}"
                    data-auto-reconnect="{{ $autoReconnect ? 'true' : 'false' }}"
                    data-reconnect-delay="{{ $reconnectDelay }}"
                    data-mark-as-read-url="{{ $markAsReadUrl }}"
                    data-mark-all-as-read-url="{{ $markAllAsReadUrl }}"
                    data-delete-url="{{ $deleteUrl }}"
                    data-load-notifications-url="{{ $loadNotificationsUrl }}"
                    @if($userId) data-user-id="{{ $userId }}" @endif
                >
                    <x-daisy::ui.communication.notification-list
                        :notifications="$notifications"
                        :group-by-date="$groupByDate"
                        :show-actions="$showDelete"
                        :mark-as-read-url="$markAsReadUrl"
                        :delete-url="$deleteUrl"
                        :notification-id-key="$notificationIdKey"
                        :notification-type-key="$notificationTypeKey"
                        :notification-data-key="$notificationDataKey"
                        :notification-read-at-key="$notificationReadAtKey"
                        :notification-created-at-key="$notificationCreatedAtKey"
                    />
                </div>

                @if($pagination && isset($paginationData))
                    <div class="flex justify-center">
                        <x-daisy::ui.navigation.pagination
                            :total="$paginationData['total'] ?? 1"
                            :current="$paginationData['current'] ?? 1"
                        />
                    </div>
                @endif
            </div>

            <div class="space-y-4">
                <x-daisy::ui.layout.card :title="__('notifications.channel_preferences')">
                    <p class="text-sm text-base-content/70">
                        {{ __('notifications.channel_preferences_helper') }}
                    </p>
                    <div class="mt-4 space-y-3">
                        @forelse($channelBreakdown->take(4) as $channel => $meta)
                            @php
                                $channelKey = 'notifications.channel_' . \Illuminate\Support\Str::slug($channel, '_');
                                $channelLabel = __($channelKey);
                                if ($channelLabel === $channelKey) {
                                    $channelLabel = \Illuminate\Support\Str::headline($channel);
                                }

                                $channelColorMap = [
                                    'email' => 'info',
                                    'sms' => 'warning',
                                    'push' => 'accent',
                                    'webhook' => 'secondary',
                                    'in_app' => 'success',
                                ];
                                $statusColor = $channelColorMap[$channel] ?? 'neutral';
                            @endphp
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-2">
                                    <x-daisy::ui.data-display.status :color="$statusColor" size="sm" />
                                    <span class="text-sm font-medium">{{ $channelLabel }}</span>
                                </div>
                                <div class="text-xs text-base-content/70 text-right">
                                    <div>{{ trans_choice('notifications.channel_score', $meta['count'], ['count' => $meta['count']]) }}</div>
                                    @if($meta['unread'] > 0)
                                        <div class="text-error mt-0.5">
                                            {{ trans_choice('notifications.unread_count', $meta['unread'], ['count' => $meta['unread']]) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-base-content/60">
                                {{ __('notifications.empty_channels') }}
                            </p>
                        @endforelse
                    </div>
                    <x-slot:actions>
                        <x-daisy::ui.inputs.button
                            size="sm"
                            variant="ghost"
                            color="neutral"
                            :tag="$preferencesUrl ? 'a' : 'button'"
                            :href="$preferencesUrl"
                            :disabled="!$preferencesUrl"
                        >
                            {{ __('notifications.notification_preferences') }}
                        </x-daisy::ui.inputs.button>
                    </x-slot:actions>
                </x-daisy::ui.layout.card>

                <x-daisy::ui.layout.card :title="__('notifications.daily_digest')">
                    <p class="text-sm text-base-content/70">
                        {{ __('notifications.daily_digest_helper', ['time' => $digestTime]) }}
                    </p>
                    <x-slot:actions>
                        <x-daisy::ui.inputs.button
                            size="sm"
                            variant="outline"
                            color="primary"
                            :tag="$preferencesUrl ? 'a' : 'button'"
                            :href="$preferencesUrl"
                            :disabled="!$preferencesUrl"
                        >
                            {{ __('notifications.set_digest') }}
                        </x-daisy::ui.inputs.button>
                    </x-slot:actions>
                </x-daisy::ui.layout.card>
            </div>
        </div>
    </section>
</x-daisy::layout.app>
