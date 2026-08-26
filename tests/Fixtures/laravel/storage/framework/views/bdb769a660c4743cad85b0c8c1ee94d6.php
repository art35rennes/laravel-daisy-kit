<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
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

    $conversationsUrl = $conversationsUrl ?? (Route::has('chat.conversations') ? route('chat.conversations') : '#');
    $conversationsUrl = $normalizeEndpoint($conversationsUrl);
?>

<div <?php echo e($attributes->merge(['class' => 'chat-sidebar flex flex-col h-full border-r bg-base-100'])); ?>>
    
    <div class="p-3 sm:p-4 border-b">
        <?php if (isset($component)) { $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.input','data' => ['type' => 'text','placeholder' => __('daisy::chat.search_conversations'),'class' => 'w-full','dataSearchInput' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'text','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::chat.search_conversations')),'class' => 'w-full','data-search-input' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b)): ?>
<?php $attributes = $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b; ?>
<?php unset($__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b)): ?>
<?php $component = $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b; ?>
<?php unset($__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b); ?>
<?php endif; ?>
    </div>

    
    <div class="flex-1 overflow-y-auto">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($conversations)): ?>
            <?php if (isset($component)) { $__componentOriginal35ede04184a85d1a23bc936778c668e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal35ede04184a85d1a23bc936778c668e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.feedback.empty-state','data' => ['icon' => 'bi-chat-dots','title' => __('daisy::chat.no_conversations'),'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.feedback.empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi-chat-dots','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::chat.no_conversations')),'size' => 'sm']); ?>
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
            <?php if (isset($component)) { $__componentOriginal2923c1d87b28b12695879243343d6dbf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2923c1d87b28b12695879243343d6dbf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.list','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.list'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conversation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $id = data_get($conversation, $conversationIdKey);
                        $name = data_get($conversation, $conversationNameKey, '');
                        $avatar = data_get($conversation, $conversationAvatarKey);
                        $lastMessage = data_get($conversation, $conversationLastMessageKey);
                        $unreadCount = data_get($conversation, $conversationUnreadCountKey, 0);
                        $isOnline = data_get($conversation, $conversationIsOnlineKey, false);
                        $isActive = $currentConversationId && (string) $id === (string) $currentConversationId;
                    ?>

                    <?php if (isset($component)) { $__componentOriginal67293358efa8f7bce39f1e109634dc5f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67293358efa8f7bce39f1e109634dc5f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.list-row','data' => ['class' => 'cursor-pointer hover:bg-base-200 transition-colors '.e($isActive ? 'bg-base-200' : '').'','dataConversationId' => ''.e($id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.list-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'cursor-pointer hover:bg-base-200 transition-colors '.e($isActive ? 'bg-base-200' : '').'','data-conversation-id' => ''.e($id).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <div class="flex items-center gap-3 w-full">
                            <?php if (isset($component)) { $__componentOriginalc4b515aecc51170d16885bb5fad2ac22 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc4b515aecc51170d16885bb5fad2ac22 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.avatar','data' => ['src' => $avatar,'placeholder' => $name ? substr($name, 0, 1) : 'U','size' => 'md','status' => $isOnline ? 'online' : 'offline']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['src' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($avatar),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($name ? substr($name, 0, 1) : 'U'),'size' => 'md','status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isOnline ? 'online' : 'offline')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc4b515aecc51170d16885bb5fad2ac22)): ?>
<?php $attributes = $__attributesOriginalc4b515aecc51170d16885bb5fad2ac22; ?>
<?php unset($__attributesOriginalc4b515aecc51170d16885bb5fad2ac22); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc4b515aecc51170d16885bb5fad2ac22)): ?>
<?php $component = $__componentOriginalc4b515aecc51170d16885bb5fad2ac22; ?>
<?php unset($__componentOriginalc4b515aecc51170d16885bb5fad2ac22); ?>
<?php endif; ?>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-sm text-base-content">
                                    <?php echo e($name); ?>

                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lastMessage): ?>
                                    <div class="text-xs text-base-content opacity-70 truncate">
                                        <?php echo e($lastMessage); ?>

                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
                                <?php if (isset($component)) { $__componentOriginalda6138eea77c89c66aa47df8543151c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalda6138eea77c89c66aa47df8543151c4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.badge','data' => ['size' => 'sm','color' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','color' => 'primary']); ?>
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
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal67293358efa8f7bce39f1e109634dc5f)): ?>
<?php $attributes = $__attributesOriginal67293358efa8f7bce39f1e109634dc5f; ?>
<?php unset($__attributesOriginal67293358efa8f7bce39f1e109634dc5f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal67293358efa8f7bce39f1e109634dc5f)): ?>
<?php $component = $__componentOriginal67293358efa8f7bce39f1e109634dc5f; ?>
<?php unset($__componentOriginal67293358efa8f7bce39f1e109634dc5f); ?>
<?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2923c1d87b28b12695879243343d6dbf)): ?>
<?php $attributes = $__attributesOriginal2923c1d87b28b12695879243343d6dbf; ?>
<?php unset($__attributesOriginal2923c1d87b28b12695879243343d6dbf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2923c1d87b28b12695879243343d6dbf)): ?>
<?php $component = $__componentOriginal2923c1d87b28b12695879243343d6dbf; ?>
<?php unset($__componentOriginal2923c1d87b28b12695879243343d6dbf); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showUserList && !empty($onlineUsers)): ?>
        <div class="p-3 sm:p-4 border-t">
            <div class="text-xs font-semibold text-base-content opacity-70 mb-2">
                <?php echo e(__('daisy::chat.users_online')); ?>

            </div>
            <div class="flex flex-wrap gap-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $onlineUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $userId = data_get($user, 'id');
                        $userName = data_get($user, 'name', '');
                        $userAvatar = data_get($user, 'avatar');
                    ?>
                    <?php if (isset($component)) { $__componentOriginalc4b515aecc51170d16885bb5fad2ac22 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc4b515aecc51170d16885bb5fad2ac22 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.avatar','data' => ['src' => $userAvatar,'placeholder' => $userName ? substr($userName, 0, 1) : 'U','size' => 'sm','status' => 'online']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['src' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($userAvatar),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($userName ? substr($userName, 0, 1) : 'U'),'size' => 'sm','status' => 'online']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc4b515aecc51170d16885bb5fad2ac22)): ?>
<?php $attributes = $__attributesOriginalc4b515aecc51170d16885bb5fad2ac22; ?>
<?php unset($__attributesOriginalc4b515aecc51170d16885bb5fad2ac22); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc4b515aecc51170d16885bb5fad2ac22)): ?>
<?php $component = $__componentOriginalc4b515aecc51170d16885bb5fad2ac22; ?>
<?php unset($__componentOriginalc4b515aecc51170d16885bb5fad2ac22); ?>
<?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/communication/chat-sidebar.blade.php ENDPATH**/ ?>