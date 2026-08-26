<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'conversation' => null,
    'messages' => [],
    'currentUserId' => null,
    'position' => 'bottom-right', // bottom-right, bottom-left, top-right, top-left
    'minimized' => false,
    'showHeader' => true,
    'showInput' => true,
    // Toutes les props des composants enfants
    'showBackButton' => false,
    'backUrl' => null,
    'showTypingIndicator' => true,
    'typingUsers' => [],
    'enableFileUpload' => false,
    'maxFileSize' => 5120,
    'placeholder' => __('daisy::chat.type_message'),
    // Routes
    'sendMessageUrl' => null,
    'typingUrl' => null,
    'loadMessagesUrl' => null,
    // Options REST/WebSocket
    'useWebSockets' => false,
    'pollingInterval' => 3000,
    'autoReconnect' => true,
    'reconnectDelay' => 5000,
    // Data accessors
    'conversationIdKey' => 'id',
    'conversationNameKey' => 'name',
    'conversationAvatarKey' => 'avatar',
    'conversationIsOnlineKey' => 'isOnline',
    'messageIdKey' => 'id',
    'messageUserIdKey' => 'user_id',
    'messageContentKey' => 'content',
    'messageCreatedAtKey' => 'created_at',
    'messageUserNameKey' => 'user_name',
    'messageUserAvatarKey' => 'user_avatar',
    // Module override
    'module' => 'chat-widget',
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
    'conversation' => null,
    'messages' => [],
    'currentUserId' => null,
    'position' => 'bottom-right', // bottom-right, bottom-left, top-right, top-left
    'minimized' => false,
    'showHeader' => true,
    'showInput' => true,
    // Toutes les props des composants enfants
    'showBackButton' => false,
    'backUrl' => null,
    'showTypingIndicator' => true,
    'typingUsers' => [],
    'enableFileUpload' => false,
    'maxFileSize' => 5120,
    'placeholder' => __('daisy::chat.type_message'),
    // Routes
    'sendMessageUrl' => null,
    'typingUrl' => null,
    'loadMessagesUrl' => null,
    // Options REST/WebSocket
    'useWebSockets' => false,
    'pollingInterval' => 3000,
    'autoReconnect' => true,
    'reconnectDelay' => 5000,
    // Data accessors
    'conversationIdKey' => 'id',
    'conversationNameKey' => 'name',
    'conversationAvatarKey' => 'avatar',
    'conversationIsOnlineKey' => 'isOnline',
    'messageIdKey' => 'id',
    'messageUserIdKey' => 'user_id',
    'messageContentKey' => 'content',
    'messageCreatedAtKey' => 'created_at',
    'messageUserNameKey' => 'user_name',
    'messageUserAvatarKey' => 'user_avatar',
    // Module override
    'module' => 'chat-widget',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $positionClasses = [
        'bottom-right' => 'bottom-4 right-4',
        'bottom-left' => 'bottom-4 left-4',
        'top-right' => 'top-4 right-4',
        'top-left' => 'top-4 left-4',
    ];
    $positionClass = $positionClasses[$position] ?? 'bottom-4 right-4';
?>

<div 
    class="chat-widget fixed <?php echo e($positionClass); ?> z-50"
    data-module="<?php echo e($module); ?>"
    data-position="<?php echo e($position); ?>"
    data-minimized="<?php echo e($minimized ? 'true' : 'false'); ?>"
>
    
    <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['type' => 'button','variant' => 'solid','color' => 'primary','size' => 'lg','circle' => true,'class' => 'shadow chat-widget-toggle','dataWidgetToggle' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'solid','color' => 'primary','size' => 'lg','circle' => true,'class' => 'shadow chat-widget-toggle','data-widget-toggle' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <?php if (isset($component)) { $__componentOriginal606b6d7eddc2e418f11096356be15e19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal606b6d7eddc2e418f11096356be15e19 = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Icon::resolve(['name' => 'bi-chat-dots'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-6 h-6']); ?>
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

    
    <div 
        class="chat-widget-panel hidden bg-base-100 rounded-box shadow flex flex-col w-[calc(100vw-2rem)] sm:w-96 h-[600px] max-h-[calc(100vh-2rem)]"
        data-widget-panel
    >
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showHeader): ?>
            <?php if (isset($component)) { $__componentOriginal7b6c29bdc1773624b1e42dcb2aa7e6ae = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7b6c29bdc1773624b1e42dcb2aa7e6ae = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.communication.chat-header','data' => ['conversation' => $conversation,'showBackButton' => $showBackButton,'backUrl' => $backUrl,'conversationIdKey' => $conversationIdKey,'conversationNameKey' => $conversationNameKey,'conversationAvatarKey' => $conversationAvatarKey,'conversationIsOnlineKey' => $conversationIsOnlineKey]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.communication.chat-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['conversation' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversation),'show-back-button' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showBackButton),'back-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($backUrl),'conversation-id-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversationIdKey),'conversation-name-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversationNameKey),'conversation-avatar-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversationAvatarKey),'conversation-is-online-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversationIsOnlineKey)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7b6c29bdc1773624b1e42dcb2aa7e6ae)): ?>
<?php $attributes = $__attributesOriginal7b6c29bdc1773624b1e42dcb2aa7e6ae; ?>
<?php unset($__attributesOriginal7b6c29bdc1773624b1e42dcb2aa7e6ae); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7b6c29bdc1773624b1e42dcb2aa7e6ae)): ?>
<?php $component = $__componentOriginal7b6c29bdc1773624b1e42dcb2aa7e6ae; ?>
<?php unset($__componentOriginal7b6c29bdc1773624b1e42dcb2aa7e6ae); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal12313c75401e66e569ab4377d0667fb2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal12313c75401e66e569ab4377d0667fb2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.communication.chat-messages','data' => ['messages' => $messages,'currentUserId' => $currentUserId,'showTypingIndicator' => $showTypingIndicator,'typingUsers' => $typingUsers,'loadMessagesUrl' => $loadMessagesUrl,'useWebsockets' => $useWebSockets,'pollingInterval' => $pollingInterval,'autoReconnect' => $autoReconnect,'reconnectDelay' => $reconnectDelay,'messageIdKey' => $messageIdKey,'messageUserIdKey' => $messageUserIdKey,'messageContentKey' => $messageContentKey,'messageCreatedAtKey' => $messageCreatedAtKey,'messageUserNameKey' => $messageUserNameKey,'messageUserAvatarKey' => $messageUserAvatarKey,'class' => 'flex-1 min-h-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.communication.chat-messages'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messages),'current-user-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentUserId),'show-typing-indicator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showTypingIndicator),'typing-users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($typingUsers),'load-messages-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($loadMessagesUrl),'use-websockets' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($useWebSockets),'polling-interval' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pollingInterval),'auto-reconnect' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($autoReconnect),'reconnect-delay' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($reconnectDelay),'message-id-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messageIdKey),'message-user-id-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messageUserIdKey),'message-content-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messageContentKey),'message-created-at-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messageCreatedAtKey),'message-user-name-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messageUserNameKey),'message-user-avatar-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messageUserAvatarKey),'class' => 'flex-1 min-h-0']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal12313c75401e66e569ab4377d0667fb2)): ?>
<?php $attributes = $__attributesOriginal12313c75401e66e569ab4377d0667fb2; ?>
<?php unset($__attributesOriginal12313c75401e66e569ab4377d0667fb2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal12313c75401e66e569ab4377d0667fb2)): ?>
<?php $component = $__componentOriginal12313c75401e66e569ab4377d0667fb2; ?>
<?php unset($__componentOriginal12313c75401e66e569ab4377d0667fb2); ?>
<?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showInput): ?>
            <?php if (isset($component)) { $__componentOriginal9ecd7182203a3087afbb046feb5feaf0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ecd7182203a3087afbb046feb5feaf0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.communication.chat-input','data' => ['sendMessageUrl' => $sendMessageUrl,'typingUrl' => $typingUrl,'enableFileUpload' => $enableFileUpload,'maxFileSize' => $maxFileSize,'placeholder' => $placeholder,'useWebsockets' => $useWebSockets,'autoReconnect' => $autoReconnect,'conversationIdKey' => $conversationIdKey]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.communication.chat-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['send-message-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sendMessageUrl),'typing-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($typingUrl),'enable-file-upload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($enableFileUpload),'max-file-size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($maxFileSize),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($placeholder),'use-websockets' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($useWebSockets),'auto-reconnect' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($autoReconnect),'conversation-id-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversationIdKey)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ecd7182203a3087afbb046feb5feaf0)): ?>
<?php $attributes = $__attributesOriginal9ecd7182203a3087afbb046feb5feaf0; ?>
<?php unset($__attributesOriginal9ecd7182203a3087afbb046feb5feaf0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ecd7182203a3087afbb046feb5feaf0)): ?>
<?php $component = $__componentOriginal9ecd7182203a3087afbb046feb5feaf0; ?>
<?php unset($__componentOriginal9ecd7182203a3087afbb046feb5feaf0); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['type' => 'button','variant' => 'ghost','size' => 'sm','circle' => true,'class' => 'absolute top-2 right-2','dataWidgetMinimize' => true,'title' => ''.e(__('daisy::chat.minimize')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','size' => 'sm','circle' => true,'class' => 'absolute top-2 right-2','data-widget-minimize' => true,'title' => ''.e(__('daisy::chat.minimize')).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php if (isset($component)) { $__componentOriginal606b6d7eddc2e418f11096356be15e19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal606b6d7eddc2e418f11096356be15e19 = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Icon::resolve(['name' => 'bi-x'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
</div>

<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/communication/chat-widget.blade.php ENDPATH**/ ?>