<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
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
    $deleteUrl = $deleteUrl ?? (Route::has('notifications.delete') ? route('notifications.delete', ':id') : '#');
    $markAsReadUrl = $normalizeEndpoint($markAsReadUrl);
    $deleteUrl = $normalizeEndpoint($deleteUrl);

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
?>

<div <?php echo e($attributes->merge(['class' => 'notification-list'])); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($notifications)): ?>
        <?php if (isset($component)) { $__componentOriginal35ede04184a85d1a23bc936778c668e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal35ede04184a85d1a23bc936778c668e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.feedback.empty-state','data' => ['icon' => 'bi-bell','title' => __('daisy::notifications.no_notifications')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.feedback.empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi-bell','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::notifications.no_notifications'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal35ede04184a85d1a23bc936778c668e2)): ?>
<?php $attributes = $__attributesOriginal35ede04184a85d1a23bc936778c668e2; ?>
<?php unset($__attributesOriginal35ede04184a85d1a23bc936778c668e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal35ede04184a85d1a23bc936778c668e2)): ?>
<?php $component = $__componentOriginal35ede04184a85d1a23bc936778c668e2; ?>
<?php unset($__componentOriginal35ede04184a85d1a23bc936778c668e2); ?>
<?php endif; ?>
    <?php else: ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($groupByDate): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $groupedNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dateKey => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dateKey !== 'all'): ?>
                    <div class="divider divider-start text-xs opacity-60 px-4">
                        <?php echo e(__('daisy::notifications.' . $dateKey)); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $group; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal8e983931f35aa0d358b17ca27b66e559 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8e983931f35aa0d358b17ca27b66e559 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.communication.notification-item','data' => ['notification' => $notification,'showActions' => $showActions,'markAsReadUrl' => str_replace(':id', data_get($notification, 'id'), $markAsReadUrl),'deleteUrl' => str_replace(':id', data_get($notification, 'id'), $deleteUrl),'notificationIdKey' => $notificationIdKey,'notificationTypeKey' => $notificationTypeKey,'notificationDataKey' => $notificationDataKey,'notificationReadAtKey' => $notificationReadAtKey,'notificationCreatedAtKey' => $notificationCreatedAtKey]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.communication.notification-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['notification' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notification),'show-actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showActions),'mark-as-read-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_replace(':id', data_get($notification, 'id'), $markAsReadUrl)),'delete-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_replace(':id', data_get($notification, 'id'), $deleteUrl)),'notification-id-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notificationIdKey),'notification-type-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notificationTypeKey),'notification-data-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notificationDataKey),'notification-read-at-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notificationReadAtKey),'notification-created-at-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notificationCreatedAtKey)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8e983931f35aa0d358b17ca27b66e559)): ?>
<?php $attributes = $__attributesOriginal8e983931f35aa0d358b17ca27b66e559; ?>
<?php unset($__attributesOriginal8e983931f35aa0d358b17ca27b66e559); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e983931f35aa0d358b17ca27b66e559)): ?>
<?php $component = $__componentOriginal8e983931f35aa0d358b17ca27b66e559; ?>
<?php unset($__componentOriginal8e983931f35aa0d358b17ca27b66e559); ?>
<?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <?php else: ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal8e983931f35aa0d358b17ca27b66e559 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8e983931f35aa0d358b17ca27b66e559 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.communication.notification-item','data' => ['notification' => $notification,'showActions' => $showActions,'markAsReadUrl' => str_replace(':id', data_get($notification, 'id'), $markAsReadUrl),'deleteUrl' => str_replace(':id', data_get($notification, 'id'), $deleteUrl),'notificationIdKey' => $notificationIdKey,'notificationTypeKey' => $notificationTypeKey,'notificationDataKey' => $notificationDataKey,'notificationReadAtKey' => $notificationReadAtKey,'notificationCreatedAtKey' => $notificationCreatedAtKey]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.communication.notification-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['notification' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notification),'show-actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showActions),'mark-as-read-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_replace(':id', data_get($notification, 'id'), $markAsReadUrl)),'delete-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_replace(':id', data_get($notification, 'id'), $deleteUrl)),'notification-id-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notificationIdKey),'notification-type-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notificationTypeKey),'notification-data-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notificationDataKey),'notification-read-at-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notificationReadAtKey),'notification-created-at-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notificationCreatedAtKey)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8e983931f35aa0d358b17ca27b66e559)): ?>
<?php $attributes = $__attributesOriginal8e983931f35aa0d358b17ca27b66e559; ?>
<?php unset($__attributesOriginal8e983931f35aa0d358b17ca27b66e559); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e983931f35aa0d358b17ca27b66e559)): ?>
<?php $component = $__componentOriginal8e983931f35aa0d358b17ca27b66e559; ?>
<?php unset($__componentOriginal8e983931f35aa0d358b17ca27b66e559); ?>
<?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/communication/notification-list.blade.php ENDPATH**/ ?>