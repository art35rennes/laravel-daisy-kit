@props([
    'notifications' => [],
    'groupByDate' => true,
    'showActions' => true,
    // Data accessors
    'notificationIdKey' => 'id',
    'notificationTypeKey' => 'type',
    'notificationDataKey' => 'data',
    'notificationReadAtKey' => 'read_at',
    'notificationCreatedAtKey' => 'created_at',
    // Routes
    'markAsReadUrl' => null,
    'deleteUrl' => null,
])

@php
    $markAsReadUrl = $markAsReadUrl ?? (Route::has('notifications.read') ? route('notifications.read', ':id') : '#');
    $deleteUrl = $deleteUrl ?? (Route::has('notifications.delete') ? route('notifications.delete', ':id') : '#');

    // Grouper par date si demandé
    $groupedNotifications = [];
    if ($groupByDate && !empty($notifications)) {
        foreach ($notifications as $notification) {
            $createdAt = data_get($notification, 'created_at');
            $dateKey = 'older';
            
            if ($createdAt) {
                try {
                    $date = is_string($createdAt) ? \Carbon\Carbon::parse($createdAt) : $createdAt;
                    $now = \Carbon\Carbon::now();
                    
                    if ($date->isToday()) {
                        $dateKey = 'today';
                    } elseif ($date->isYesterday()) {
                        $dateKey = 'yesterday';
                    } elseif ($date->isCurrentWeek()) {
                        $dateKey = 'this_week';
                    }
                } catch (\Exception $e) {
                    // En cas d'erreur, on met dans "older"
                }
            }
            
            if (!isset($groupedNotifications[$dateKey])) {
                $groupedNotifications[$dateKey] = [];
            }
            $groupedNotifications[$dateKey][] = $notification;
        }
    } else {
        $groupedNotifications = ['all' => $notifications];
    }
@endphp

<div {{ $attributes->merge(['class' => 'notification-list']) }}>
    @if(empty($notifications))
        <x-daisy::ui.feedback.empty-state
            icon="bi-bell"
            :title="__('notifications.no_notifications')"
        />
    @else
        @if($groupByDate)
            @foreach($groupedNotifications as $dateKey => $group)
                @if($dateKey !== 'all')
                    <div class="divider divider-start text-xs opacity-60 px-4">
                        {{ __('notifications.' . $dateKey) }}
                    </div>
                @endif
                @foreach($group as $notification)
                    <x-daisy::ui.communication.notification-item
                        :notification="$notification"
                        :show-actions="$showActions"
                        :mark-as-read-url="str_replace(':id', data_get($notification, 'id'), $markAsReadUrl)"
                        :delete-url="str_replace(':id', data_get($notification, 'id'), $deleteUrl)"
                        :notification-id-key="$notificationIdKey"
                        :notification-type-key="$notificationTypeKey"
                        :notification-data-key="$notificationDataKey"
                        :notification-read-at-key="$notificationReadAtKey"
                        :notification-created-at-key="$notificationCreatedAtKey"
                    />
                @endforeach
            @endforeach
        @else
            @foreach($notifications as $notification)
                <x-daisy::ui.communication.notification-item
                    :notification="$notification"
                    :show-actions="$showActions"
                    :mark-as-read-url="str_replace(':id', data_get($notification, 'id'), $markAsReadUrl)"
                    :delete-url="str_replace(':id', data_get($notification, 'id'), $deleteUrl)"
                    :notification-id-key="$notificationIdKey"
                    :notification-type-key="$notificationTypeKey"
                    :notification-data-key="$notificationDataKey"
                    :notification-read-at-key="$notificationReadAtKey"
                    :notification-created-at-key="$notificationCreatedAtKey"
                />
            @endforeach
        @endif
    @endif
</div>

