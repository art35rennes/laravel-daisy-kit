<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => __('daisy::notifications.notifications'),
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
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'title' => __('daisy::notifications.notifications'),
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
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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

    $markAsReadUrl = $markAsReadUrl ?? (Route::has('notifications.read') ? route('notifications.read', ':id') : '#');
    $markAllAsReadUrl = $markAllAsReadUrl ?? (Route::has('notifications.read-all') ? route('notifications.read-all') : '#');
    $deleteUrl = $deleteUrl ?? (Route::has('notifications.delete') ? route('notifications.delete', ':id') : '#');
    $loadNotificationsUrl = $loadNotificationsUrl ?? (Route::has('notifications.index') ? route('notifications.index') : '#');
    $preferencesUrl = $preferencesUrl ?? (Route::has('notifications.preferences') ? route('notifications.preferences') : null);
    $markAsReadUrl = $normalizeEndpoint($markAsReadUrl);
    $markAllAsReadUrl = $normalizeEndpoint($markAllAsReadUrl);
    $deleteUrl = $normalizeEndpoint($deleteUrl);
    $loadNotificationsUrl = $normalizeEndpoint($loadNotificationsUrl);
    $preferencesUrl = $normalizeEndpoint($preferencesUrl, null);

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
            'label' => __('daisy::notifications.critical_alerts'),
            'value' => $criticalNotifications->count(),
            'count' => $criticalNotifications->count(),
            'icon' => 'bi-exclamation-triangle',
            'iconColor' => 'text-error',
        ],
        [
            'label' => __('daisy::notifications.actionable_notifications'),
            'value' => $actionableCount,
            'count' => $actionableCount,
            'icon' => 'bi-lightning-charge',
            'iconColor' => 'text-warning',
        ],
        [
            'label' => __('daisy::notifications.upcoming_followups'),
            'value' => $dueSoonCount,
            'count' => $dueSoonCount,
            'icon' => 'bi-calendar-event',
            'iconColor' => 'text-info',
        ],
        [
            'label' => __('daisy::notifications.mentions'),
            'value' => $mentionCount,
            'count' => $mentionCount,
            'icon' => 'bi-at',
            'iconColor' => 'text-secondary',
        ],
    ];
?>

<?php if (isset($component)) { $__componentOriginala7bea3f816103b034498a0cafca82f36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala7bea3f816103b034498a0cafca82f36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.layout.app','data' => ['title' => $title,'theme' => $theme,'container' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::layout.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title),'theme' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($theme),'container' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php if (isset($component)) { $__componentOriginald4aaa4b01baa94db46e38e4697384b0c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4aaa4b01baa94db46e38e4697384b0c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.theme-selector','data' => ['position' => 'fixed','placement' => 'top-right']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.theme-selector'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['position' => 'fixed','placement' => 'top-right']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4aaa4b01baa94db46e38e4697384b0c)): ?>
<?php $attributes = $__attributesOriginald4aaa4b01baa94db46e38e4697384b0c; ?>
<?php unset($__attributesOriginald4aaa4b01baa94db46e38e4697384b0c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4aaa4b01baa94db46e38e4697384b0c)): ?>
<?php $component = $__componentOriginald4aaa4b01baa94db46e38e4697384b0c; ?>
<?php unset($__componentOriginald4aaa4b01baa94db46e38e4697384b0c); ?>
<?php endif; ?>
    <section class="notification-center max-w-6xl mx-auto px-4 sm:px-6 space-y-10">
        <header class="bg-base-100 rounded-box p-5 sm:p-8 shadow space-y-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="min-w-0 space-y-3 flex-1">
                    <p class="text-xs uppercase tracking-wide text-primary font-semibold">
                        <?php echo e(__('daisy::notifications.control_room')); ?>

                    </p>
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-3xl font-semibold tracking-tight"><?php echo e($title); ?></h1>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!is_null($unreadCount)): ?>
                            <div class="flex items-center gap-2" data-unread-count>
                                <?php if (isset($component)) { $__componentOriginalda6138eea77c89c66aa47df8543151c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalda6138eea77c89c66aa47df8543151c4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.badge','data' => ['color' => 'primary','size' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'primary','size' => 'lg']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                    <?php echo e($unreadCount); ?>

                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $attributes = $__attributesOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__attributesOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $component = $__componentOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__componentOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>
                                <span class="text-sm text-base-content/70">
                                    <?php echo e(trans_choice('daisy::notifications.unread_count', $unreadCount, ['count' => $unreadCount])); ?>

                                </span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <p class="text-sm text-base-content/70">
                        <?php echo e(__('daisy::notifications.center_helper')); ?>

                    </p>
                </div>
                <div class="flex flex-wrap gap-2 shrink-0">
                    <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['size' => 'sm','variant' => 'ghost','color' => 'neutral','tag' => $preferencesUrl ? 'a' : 'button','href' => $preferencesUrl,'disabled' => !$preferencesUrl]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','variant' => 'ghost','color' => 'neutral','tag' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($preferencesUrl ? 'a' : 'button'),'href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($preferencesUrl),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!$preferencesUrl)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <span class="flex items-center gap-2">
                            <?php if (isset($component)) { $__componentOriginal606b6d7eddc2e418f11096356be15e19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal606b6d7eddc2e418f11096356be15e19 = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Icon::resolve(['name' => 'bi-gear'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $attributes = $__attributesOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__attributesOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $component = $__componentOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__componentOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?>
                            <?php echo e(__('daisy::notifications.notification_preferences')); ?>

                        </span>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0 && $showMarkAllRead && $markAllAsReadUrl !== '#'): ?>
                        <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['size' => 'sm','variant' => 'outline','color' => 'primary','dataAction' => 'mark-all-read','dataUrl' => ''.e($markAllAsReadUrl).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','variant' => 'outline','color' => 'primary','data-action' => 'mark-all-read','data-url' => ''.e($markAllAsReadUrl).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <span class="flex items-center gap-2">
                                <?php if (isset($component)) { $__componentOriginal606b6d7eddc2e418f11096356be15e19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal606b6d7eddc2e418f11096356be15e19 = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Icon::resolve(['name' => 'bi-check2-all'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $attributes = $__attributesOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__attributesOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $component = $__componentOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__componentOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?>
                                <?php echo e(__('daisy::notifications.mark_all_as_read')); ?>

                            </span>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="rounded-box card-border bg-base-200/40 p-4 shadow">
                        <div class="flex items-center justify-between text-sm text-base-content/70">
                            <span><?php echo e($card['label']); ?></span>
                            <?php if (isset($component)) { $__componentOriginal606b6d7eddc2e418f11096356be15e19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal606b6d7eddc2e418f11096356be15e19 = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Icon::resolve(['name' => $card['icon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 '.e($card['iconColor']).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $attributes = $__attributesOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__attributesOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $component = $__componentOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__componentOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?>
                        </div>
                        <div class="mt-3 text-3xl font-semibold tracking-tight text-base-content">
                            <?php echo e($card['value']); ?>

                        </div>
                        <p class="mt-1 text-xs text-base-content/60">
                            <?php echo e(trans_choice('daisy::notifications.channel_score', $card['count'], ['count' => $card['count']])); ?>

                        </p>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </header>

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <div class="space-y-8">
                <?php if (isset($component)) { $__componentOriginal5efd72db83161bae6e1dc57d5f89d224 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5efd72db83161bae6e1dc57d5f89d224 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.card','data' => ['title' => __('daisy::notifications.focus_section_title'),'class' => 'card-border bg-error/5 shadow']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::notifications.focus_section_title')),'class' => 'card-border bg-error/5 shadow']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <p class="text-sm text-base-content/70">
                        <?php echo e(__('daisy::notifications.focus_section_description')); ?>

                    </p>
                    <div class="mt-4 space-y-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $focusNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $focusNotification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $focusData = data_get($focusNotification, $notificationDataKey, []);
                                $focusLink = $normalizeEndpoint(data_get($focusData, 'action.url', data_get($focusData, 'link', '#')));
                                $focusMessage = data_get($focusData, 'message', __('daisy::notifications.new_notification'));
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
                            ?>
                            <div class="flex items-start gap-3">
                                <?php if (isset($component)) { $__componentOriginal606b6d7eddc2e418f11096356be15e19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal606b6d7eddc2e418f11096356be15e19 = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Icon::resolve(['name' => 'bi-exclamation-diamond'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 text-error mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $attributes = $__attributesOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__attributesOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $component = $__componentOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__componentOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?>
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-sm"><?php echo e($focusMessage); ?></p>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($focusDate): ?>
                                        <p class="text-xs text-base-content/60 mt-1"><?php echo e($focusDate); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($focusLink && $focusLink !== '#'): ?>
                                    <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['size' => 'xs','variant' => 'ghost','color' => 'error','tag' => 'a','href' => $focusLink]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'xs','variant' => 'ghost','color' => 'error','tag' => 'a','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($focusLink)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                        <?php echo e(__('daisy::notifications.cta_view_details')); ?>

                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <p class="text-sm text-base-content/70">
                                <?php echo e(__('daisy::notifications.empty_focus')); ?>

                            </p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5efd72db83161bae6e1dc57d5f89d224)): ?>
<?php $attributes = $__attributesOriginal5efd72db83161bae6e1dc57d5f89d224; ?>
<?php unset($__attributesOriginal5efd72db83161bae6e1dc57d5f89d224); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5efd72db83161bae6e1dc57d5f89d224)): ?>
<?php $component = $__componentOriginal5efd72db83161bae6e1dc57d5f89d224; ?>
<?php unset($__componentOriginal5efd72db83161bae6e1dc57d5f89d224); ?>
<?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showFilters && !empty($types)): ?>
                    <?php if (isset($component)) { $__componentOriginalfc0fb8a0dd8220ddf81fda6cb8cf24b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfc0fb8a0dd8220ddf81fda6cb8cf24b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.communication.notification-filters','data' => ['types' => $types,'currentFilter' => $currentFilter]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.communication.notification-filters'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['types' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($types),'current-filter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentFilter)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfc0fb8a0dd8220ddf81fda6cb8cf24b9)): ?>
<?php $attributes = $__attributesOriginalfc0fb8a0dd8220ddf81fda6cb8cf24b9; ?>
<?php unset($__attributesOriginalfc0fb8a0dd8220ddf81fda6cb8cf24b9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfc0fb8a0dd8220ddf81fda6cb8cf24b9)): ?>
<?php $component = $__componentOriginalfc0fb8a0dd8220ddf81fda6cb8cf24b9; ?>
<?php unset($__componentOriginalfc0fb8a0dd8220ddf81fda6cb8cf24b9); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div
                    class="bg-base-100 rounded-box shadow divide-y divide-base-200"
                    data-module="notifications"
                    data-use-websockets="<?php echo e($useWebSockets ? 'true' : 'false'); ?>"
                    data-polling-interval="<?php echo e($pollingInterval); ?>"
                    data-auto-reconnect="<?php echo e($autoReconnect ? 'true' : 'false'); ?>"
                    data-reconnect-delay="<?php echo e($reconnectDelay); ?>"
                    data-mark-as-read-url="<?php echo e($markAsReadUrl !== '#' ? $markAsReadUrl : ''); ?>"
                    data-mark-all-as-read-url="<?php echo e($markAllAsReadUrl !== '#' ? $markAllAsReadUrl : ''); ?>"
                    data-delete-url="<?php echo e($deleteUrl !== '#' ? $deleteUrl : ''); ?>"
                    data-load-notifications-url="<?php echo e($loadNotificationsUrl !== '#' ? $loadNotificationsUrl : ''); ?>"
                    <?php if($userId): ?> data-user-id="<?php echo e($userId); ?>" <?php endif; ?>
                >
                    <?php if (isset($component)) { $__componentOriginalae3c74ba7995567f8506d3a7f1ef7840 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae3c74ba7995567f8506d3a7f1ef7840 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.communication.notification-list','data' => ['notifications' => $notifications,'groupByDate' => $groupByDate,'showActions' => $showDelete,'markAsReadUrl' => $markAsReadUrl,'deleteUrl' => $deleteUrl,'notificationIdKey' => $notificationIdKey,'notificationTypeKey' => $notificationTypeKey,'notificationDataKey' => $notificationDataKey,'notificationReadAtKey' => $notificationReadAtKey,'notificationCreatedAtKey' => $notificationCreatedAtKey]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.communication.notification-list'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['notifications' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notifications),'group-by-date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($groupByDate),'show-actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showDelete),'mark-as-read-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($markAsReadUrl),'delete-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($deleteUrl),'notification-id-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notificationIdKey),'notification-type-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notificationTypeKey),'notification-data-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notificationDataKey),'notification-read-at-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notificationReadAtKey),'notification-created-at-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notificationCreatedAtKey)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae3c74ba7995567f8506d3a7f1ef7840)): ?>
<?php $attributes = $__attributesOriginalae3c74ba7995567f8506d3a7f1ef7840; ?>
<?php unset($__attributesOriginalae3c74ba7995567f8506d3a7f1ef7840); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae3c74ba7995567f8506d3a7f1ef7840)): ?>
<?php $component = $__componentOriginalae3c74ba7995567f8506d3a7f1ef7840; ?>
<?php unset($__componentOriginalae3c74ba7995567f8506d3a7f1ef7840); ?>
<?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pagination && isset($paginationData)): ?>
                    <div class="flex justify-center">
                        <?php if (isset($component)) { $__componentOriginale55b5075277bb0c7550c33a521cdb8f9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale55b5075277bb0c7550c33a521cdb8f9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.navigation.pagination','data' => ['total' => $paginationData['total'] ?? 1,'current' => $paginationData['current'] ?? 1]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.navigation.pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paginationData['total'] ?? 1),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paginationData['current'] ?? 1)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale55b5075277bb0c7550c33a521cdb8f9)): ?>
<?php $attributes = $__attributesOriginale55b5075277bb0c7550c33a521cdb8f9; ?>
<?php unset($__attributesOriginale55b5075277bb0c7550c33a521cdb8f9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale55b5075277bb0c7550c33a521cdb8f9)): ?>
<?php $component = $__componentOriginale55b5075277bb0c7550c33a521cdb8f9; ?>
<?php unset($__componentOriginale55b5075277bb0c7550c33a521cdb8f9); ?>
<?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="space-y-4">
                <?php if (isset($component)) { $__componentOriginal5efd72db83161bae6e1dc57d5f89d224 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5efd72db83161bae6e1dc57d5f89d224 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.card','data' => ['title' => __('daisy::notifications.channel_preferences')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::notifications.channel_preferences'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <p class="text-sm text-base-content/70">
                        <?php echo e(__('daisy::notifications.channel_preferences_helper')); ?>

                    </p>
                    <div class="mt-4 space-y-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $channelBreakdown->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $channel => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $channelKey = 'daisy::notifications.channel_' . \Illuminate\Support\Str::slug($channel, '_');
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
                            ?>
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-2">
                                    <?php if (isset($component)) { $__componentOriginal817cfa6a60a07e091fab5124ed9d029f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal817cfa6a60a07e091fab5124ed9d029f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.status','data' => ['color' => $statusColor,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statusColor),'size' => 'sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal817cfa6a60a07e091fab5124ed9d029f)): ?>
<?php $attributes = $__attributesOriginal817cfa6a60a07e091fab5124ed9d029f; ?>
<?php unset($__attributesOriginal817cfa6a60a07e091fab5124ed9d029f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal817cfa6a60a07e091fab5124ed9d029f)): ?>
<?php $component = $__componentOriginal817cfa6a60a07e091fab5124ed9d029f; ?>
<?php unset($__componentOriginal817cfa6a60a07e091fab5124ed9d029f); ?>
<?php endif; ?>
                                    <span class="text-sm font-medium"><?php echo e($channelLabel); ?></span>
                                </div>
                                <div class="text-xs text-base-content/70 text-right">
                                    <div><?php echo e(trans_choice('daisy::notifications.channel_score', $meta['count'], ['count' => $meta['count']])); ?></div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($meta['unread'] > 0): ?>
                                        <div class="text-error mt-0.5">
                                            <?php echo e(trans_choice('daisy::notifications.unread_count', $meta['unread'], ['count' => $meta['unread']])); ?>

                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <p class="text-sm text-base-content/60">
                                <?php echo e(__('daisy::notifications.empty_channels')); ?>

                            </p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                     <?php $__env->slot('actions', null, []); ?> 
                        <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['size' => 'sm','variant' => 'ghost','color' => 'neutral','tag' => $preferencesUrl ? 'a' : 'button','href' => $preferencesUrl,'disabled' => !$preferencesUrl]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','variant' => 'ghost','color' => 'neutral','tag' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($preferencesUrl ? 'a' : 'button'),'href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($preferencesUrl),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!$preferencesUrl)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php echo e(__('daisy::notifications.notification_preferences')); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
                     <?php $__env->endSlot(); ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5efd72db83161bae6e1dc57d5f89d224)): ?>
<?php $attributes = $__attributesOriginal5efd72db83161bae6e1dc57d5f89d224; ?>
<?php unset($__attributesOriginal5efd72db83161bae6e1dc57d5f89d224); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5efd72db83161bae6e1dc57d5f89d224)): ?>
<?php $component = $__componentOriginal5efd72db83161bae6e1dc57d5f89d224; ?>
<?php unset($__componentOriginal5efd72db83161bae6e1dc57d5f89d224); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal5efd72db83161bae6e1dc57d5f89d224 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5efd72db83161bae6e1dc57d5f89d224 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.card','data' => ['title' => __('daisy::notifications.daily_digest')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::notifications.daily_digest'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <p class="text-sm text-base-content/70">
                        <?php echo e(__('daisy::notifications.daily_digest_helper', ['time' => $digestTime])); ?>

                    </p>
                     <?php $__env->slot('actions', null, []); ?> 
                        <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['size' => 'sm','variant' => 'outline','color' => 'primary','tag' => $preferencesUrl ? 'a' : 'button','href' => $preferencesUrl,'disabled' => !$preferencesUrl]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','variant' => 'outline','color' => 'primary','tag' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($preferencesUrl ? 'a' : 'button'),'href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($preferencesUrl),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!$preferencesUrl)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php echo e(__('daisy::notifications.set_digest')); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
                     <?php $__env->endSlot(); ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5efd72db83161bae6e1dc57d5f89d224)): ?>
<?php $attributes = $__attributesOriginal5efd72db83161bae6e1dc57d5f89d224; ?>
<?php unset($__attributesOriginal5efd72db83161bae6e1dc57d5f89d224); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5efd72db83161bae6e1dc57d5f89d224)): ?>
<?php $component = $__componentOriginal5efd72db83161bae6e1dc57d5f89d224; ?>
<?php unset($__componentOriginal5efd72db83161bae6e1dc57d5f89d224); ?>
<?php endif; ?>
            </div>
        </div>
    </section>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala7bea3f816103b034498a0cafca82f36)): ?>
<?php $attributes = $__attributesOriginala7bea3f816103b034498a0cafca82f36; ?>
<?php unset($__attributesOriginala7bea3f816103b034498a0cafca82f36); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala7bea3f816103b034498a0cafca82f36)): ?>
<?php $component = $__componentOriginala7bea3f816103b034498a0cafca82f36; ?>
<?php unset($__componentOriginala7bea3f816103b034498a0cafca82f36); ?>
<?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/templates/communication/notification-center.blade.php ENDPATH**/ ?>