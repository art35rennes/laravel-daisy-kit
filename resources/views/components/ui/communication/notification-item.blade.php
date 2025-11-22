@props([
    'notification' => null,
    'showActions' => true,
    'markAsReadUrl' => null,
    'deleteUrl' => null,
    // Data accessors
    'notificationIdKey' => 'id',
    'notificationTypeKey' => 'type',
    'notificationDataKey' => 'data',
    'notificationReadAtKey' => 'read_at',
    'notificationCreatedAtKey' => 'created_at',
])

@php
    if (!$notification) {
        return;
    }

    $id = data_get($notification, $notificationIdKey);
    $type = data_get($notification, $notificationTypeKey);
    $data = data_get($notification, $notificationDataKey, []);
    $readAt = data_get($notification, $notificationReadAtKey);
    $createdAt = data_get($notification, $notificationCreatedAtKey);

    $isRead = !empty($readAt);
    $message = data_get($data, 'message', '');
    $link = data_get($data, 'link', '#');
    $user = data_get($data, 'user', []);
    $userName = data_get($user, 'name', '');
    $userAvatar = data_get($user, 'avatar', null);

    $priority = strtolower((string) data_get($data, 'priority', 'normal'));
    $priorityColorMap = [
        'critical' => 'error',
        'urgent' => 'error',
        'high' => 'warning',
        'medium' => 'info',
        'low' => 'ghost',
    ];
    $priorityColor = $priorityColorMap[$priority] ?? 'ghost';
    $priorityLabelKey = 'notifications.priority_' . $priority;
    $priorityLabel = __($priorityLabelKey);
    if ($priorityLabel === $priorityLabelKey) {
        $priorityLabel = ucfirst($priority);
    }

    $channel = strtolower((string) data_get($data, 'channel', 'in_app'));
    $channelKey = 'notifications.channel_' . $channel;
    $channelLabel = __($channelKey);
    if ($channelLabel === $channelKey) {
        $channelLabel = \Illuminate\Support\Str::headline($channel);
    }

    $tags = collect((array) data_get($data, 'tags', []))
        ->filter(function ($tag) {
            return filled($tag);
        })
        ->take(3)
        ->values();

    $action = data_get($data, 'action', []);
    $actionLabel = data_get($action, 'label');
    $actionUrl = data_get($action, 'url', $link);
    $actionIcon = data_get($action, 'icon', 'bi-arrow-right');

    $dueAt = data_get($data, 'due_at');
    $dueLabel = null;
    if ($dueAt) {
        try {
            $dueDate = is_string($dueAt) ? \Carbon\Carbon::parse($dueAt) : $dueAt;
            $dueLabel = __('notifications.due_by', ['date' => $dueDate->isoFormat('MMM D, HH:mm')]);
        } catch (\Exception $exception) {
            $dueLabel = __('notifications.due_by', ['date' => $dueAt]);
        }
    }

    // Formatage de la date
    $dateFormatted = null;
    if ($createdAt) {
        try {
            $date = is_string($createdAt) ? \Carbon\Carbon::parse($createdAt) : $createdAt;
            $dateFormatted = $date->diffForHumans();
        } catch (\Exception $e) {
            $dateFormatted = $createdAt;
        }
    }

    $markAsReadUrl = $markAsReadUrl ?? (Route::has('notifications.read') ? route('notifications.read', $id) : '#');
    $deleteUrl = $deleteUrl ?? (Route::has('notifications.delete') ? route('notifications.delete', $id) : '#');
@endphp

<div
    {{ $attributes->merge(['class' => 'notification-item flex gap-3 p-4 border-b border-base-200 last:border-b-0 transition-colors' . ($isRead ? '' : ' bg-base-200/60')]) }}
    data-notification-id="{{ $id }}"
    data-read="{{ $isRead ? 'true' : 'false' }}"
>
    @if($userAvatar || $userName)
        <div class="flex-shrink-0">
            <x-daisy::ui.data-display.avatar
                :src="$userAvatar"
                :placeholder="$userName ? substr($userName, 0, 1) : 'U'"
                size="sm"
            />
        </div>
    @endif

    <div class="flex-1 min-w-0">
        <div class="flex items-start justify-between gap-3">
            <div class="flex-1 min-w-0 space-y-2">
                <div class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-wide text-base-content/70">
                    @if($userName)
                        <span class="font-semibold text-base-content">{{ $userName }}</span>
                    @endif
                    <x-daisy::ui.data-display.badge
                        :color="$priorityColor"
                        size="xs"
                        :variant="$priorityColor === 'ghost' ? 'ghost' : 'soft'"
                        class="tracking-wide"
                    >
                        {{ $priorityLabel }}
                    </x-daisy::ui.data-display.badge>
                    @if($type)
                        <span class="text-base-content/60">{{ $type }}</span>
                    @endif
                </div>

                @if($message)
                    <div class="text-sm text-base-content line-clamp-2">
                        @if($link && $link !== '#')
                            <x-daisy::ui.advanced.link :href="$link" color="primary">
                                {{ $message }}
                            </x-daisy::ui.advanced.link>
                        @else
                            {{ $message }}
                        @endif
                    </div>
                @endif

                <div class="flex flex-wrap items-center gap-2 text-xs text-base-content/60">
                    @if($channelLabel)
                        <x-daisy::ui.data-display.badge size="xs" variant="ghost">
                            {{ $channelLabel }}
                        </x-daisy::ui.data-display.badge>
                    @endif
                    @foreach($tags as $tag)
                        <x-daisy::ui.data-display.badge size="xs" variant="outline">
                            {{ $tag }}
                        </x-daisy::ui.data-display.badge>
                    @endforeach
                    @if($dueLabel)
                        <span class="flex items-center gap-1 text-warning">
                            <x-icon name="bi-alarm" class="w-3 h-3" />
                            {{ $dueLabel }}
                        </span>
                    @endif
                    @if($dateFormatted)
                        <span>{{ $dateFormatted }}</span>
                    @endif
                </div>
            </div>

            @if(!$isRead)
                <x-daisy::ui.data-display.badge size="xs" color="primary" />
            @endif
        </div>

        @if($showActions && ($markAsReadUrl !== '#' || $deleteUrl !== '#' || ($actionLabel && $actionUrl && $actionUrl !== '#')))
            <div class="flex flex-wrap gap-2 mt-3">
                @if($actionLabel && $actionUrl && $actionUrl !== '#')
                    <x-daisy::ui.inputs.button
                        tag="a"
                        :href="$actionUrl"
                        size="xs"
                        variant="ghost"
                        color="primary"
                    >
                        <span class="flex items-center gap-1">
                            <x-icon :name="$actionIcon" class="w-3 h-3" />
                            {{ $actionLabel }}
                        </span>
                    </x-daisy::ui.inputs.button>
                @endif
                @if(!$isRead && $markAsReadUrl !== '#')
                    <x-daisy::ui.inputs.button
                        type="button"
                        variant="ghost"
                        size="xs"
                        data-action="mark-as-read"
                        data-url="{{ $markAsReadUrl }}"
                    >
                        {{ __('notifications.mark_as_read') }}
                    </x-daisy::ui.inputs.button>
                @endif
                @if($deleteUrl !== '#')
                    <x-daisy::ui.inputs.button
                        type="button"
                        variant="ghost"
                        size="xs"
                        color="error"
                        data-action="delete"
                        data-url="{{ $deleteUrl }}"
                    >
                        {{ __('notifications.delete') }}
                    </x-daisy::ui.inputs.button>
                @endif
            </div>
        @endif
    </div>
</div>
