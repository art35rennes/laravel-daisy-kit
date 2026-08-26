<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
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

    $markAllAsReadUrl = $markAllAsReadUrl ?? (Route::has('notifications.read-all') ? route('notifications.read-all') : '#');
    $viewAllUrl = $viewAllUrl ?? (Route::has('notifications.index') ? route('notifications.index') : '#');
    $markAllAsReadUrl = $normalizeEndpoint($markAllAsReadUrl);
    $viewAllUrl = $normalizeEndpoint($viewAllUrl);
    
    // Calculer le nombre de non lues si non fourni
    if (is_null($unreadCount) && !empty($notifications)) {
        $unreadCount = collect($notifications)->filter(function ($notification) use ($notificationReadAtKey) {
            $readAt = data_get($notification, $notificationReadAtKey);
            return empty($readAt);
        })->count();
    }
    
    $hasUnread = $unreadCount > 0;
    $end = str_contains($position, 'end');
?>

<?php if (isset($component)) { $__componentOriginal2244a28cb1b59e7c4b99753b5faee3a9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2244a28cb1b59e7c4b99753b5faee3a9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.overlay.dropdown','data' => ['end' => $end,'type' => 'card','contentClass' => 'dropdown-content mt-4 sm:mt-5 z-[1] p-0 shadow bg-base-100 rounded-box overflow-visible','cardBodyClass' => 'p-0','buttonClass' => 'btn btn-ghost btn-circle relative','buttonCircle' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.overlay.dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['end' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($end),'type' => 'card','content-class' => 'dropdown-content mt-4 sm:mt-5 z-[1] p-0 shadow bg-base-100 rounded-box overflow-visible','card-body-class' => 'p-0','button-class' => 'btn btn-ghost btn-circle relative','button-circle' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

     <?php $__env->slot('trigger', null, []); ?> 
        <div class="relative">
            <?php if (isset($component)) { $__componentOriginal606b6d7eddc2e418f11096356be15e19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal606b6d7eddc2e418f11096356be15e19 = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Icon::resolve(['name' => 'bi-bell'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
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
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasUnread): ?>
                <span class="absolute -top-1 -right-1">
                    <?php if (isset($component)) { $__componentOriginalda6138eea77c89c66aa47df8543151c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalda6138eea77c89c66aa47df8543151c4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.badge','data' => ['size' => 'xs','color' => 'primary','class' => 'p-0 w-5 h-5 flex items-center justify-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'xs','color' => 'primary','class' => 'p-0 w-5 h-5 flex items-center justify-center']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <?php echo e($unreadCount > 99 ? '99+' : $unreadCount); ?>

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
                </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="w-80 sm:w-96 max-w-[calc(100vw-2rem)] p-3 sm:p-4 space-y-4">
        <div class="p-3 sm:p-4 card-border rounded-box flex items-center justify-between gap-2">
            <h3 class="font-semibold text-sm sm:text-base truncate">
                <?php echo e(__('daisy::notifications.notifications')); ?>

            </h3>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasUnread && $showMarkAllRead && $markAllAsReadUrl !== '#'): ?>
                <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['type' => 'button','variant' => 'ghost','size' => 'xs','class' => 'shrink-0','dataAction' => 'mark-all-read','dataUrl' => ''.e($markAllAsReadUrl).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','size' => 'xs','class' => 'shrink-0','data-action' => 'mark-all-read','data-url' => ''.e($markAllAsReadUrl).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <span class="hidden sm:inline"><?php echo e(__('daisy::notifications.mark_all_as_read')); ?></span>
                    <span class="sm:hidden"><?php echo e(__('daisy::notifications.mark_all_as_read')); ?></span>
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

        <div class="max-h-96 overflow-y-auto px-3 sm:px-4 py-4 sm:py-6 bg-base-100 rounded-box card-border">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($notifications)): ?>
                <?php if (isset($component)) { $__componentOriginal35ede04184a85d1a23bc936778c668e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal35ede04184a85d1a23bc936778c668e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.feedback.empty-state','data' => ['icon' => 'bi-bell','title' => __('daisy::notifications.no_notifications'),'size' => 'xs','class' => 'text-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.feedback.empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi-bell','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::notifications.no_notifications')),'size' => 'xs','class' => 'text-center']); ?>
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
                <?php if (isset($component)) { $__componentOriginalae3c74ba7995567f8506d3a7f1ef7840 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae3c74ba7995567f8506d3a7f1ef7840 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.communication.notification-list','data' => ['notifications' => $notifications,'groupByDate' => $groupByDate,'showActions' => $showActions,'notificationIdKey' => $notificationIdKey,'notificationTypeKey' => $notificationTypeKey,'notificationDataKey' => $notificationDataKey,'notificationReadAtKey' => $notificationReadAtKey,'notificationCreatedAtKey' => $notificationCreatedAtKey]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.communication.notification-list'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['notifications' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notifications),'group-by-date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($groupByDate),'show-actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showActions),'notification-id-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notificationIdKey),'notification-type-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notificationTypeKey),'notification-data-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notificationDataKey),'notification-read-at-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notificationReadAtKey),'notification-created-at-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notificationCreatedAtKey)]); ?>
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
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($viewAllUrl !== '#'): ?>
            <div class="p-3 sm:p-4 card-border rounded-box text-center">
                <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['tag' => 'a','href' => $viewAllUrl,'variant' => 'ghost','size' => 'sm','block' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tag' => 'a','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($viewAllUrl),'variant' => 'ghost','size' => 'sm','block' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php echo e(__('daisy::notifications.view_all')); ?>

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
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2244a28cb1b59e7c4b99753b5faee3a9)): ?>
<?php $attributes = $__attributesOriginal2244a28cb1b59e7c4b99753b5faee3a9; ?>
<?php unset($__attributesOriginal2244a28cb1b59e7c4b99753b5faee3a9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2244a28cb1b59e7c4b99753b5faee3a9)): ?>
<?php $component = $__componentOriginal2244a28cb1b59e7c4b99753b5faee3a9; ?>
<?php unset($__componentOriginal2244a28cb1b59e7c4b99753b5faee3a9); ?>
<?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/communication/notification-bell.blade.php ENDPATH**/ ?>