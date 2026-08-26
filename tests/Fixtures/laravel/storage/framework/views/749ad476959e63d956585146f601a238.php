<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'conversation' => null,
    'messages' => [],
    'currentUserId' => null,
    // Toutes les props des composants enfants
    'showBackButton' => false,
    'backUrl' => null,
    'showTypingIndicator' => true,
    'typingUsers' => [],
    'enableFileUpload' => false,
    'maxFileSize' => 5120,
    'multipleFiles' => false,
    'showFilePreview' => true,
    'acceptedFileTypes' => 'image/*,application/pdf,.doc,.docx',
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
    // Toutes les props des composants enfants
    'showBackButton' => false,
    'backUrl' => null,
    'showTypingIndicator' => true,
    'typingUsers' => [],
    'enableFileUpload' => false,
    'maxFileSize' => 5120,
    'multipleFiles' => false,
    'showFilePreview' => true,
    'acceptedFileTypes' => 'image/*,application/pdf,.doc,.docx',
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
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div <?php echo e($attributes->merge(['class' => 'flex-1 flex flex-col min-w-0 h-full overflow-hidden'])); ?> data-conversation-id="<?php echo e(data_get($conversation, $conversationIdKey)); ?>">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($conversation): ?>
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

        <?php if (isset($component)) { $__componentOriginal9ecd7182203a3087afbb046feb5feaf0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ecd7182203a3087afbb046feb5feaf0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.communication.chat-input','data' => ['sendMessageUrl' => $sendMessageUrl,'typingUrl' => $typingUrl,'enableFileUpload' => $enableFileUpload,'maxFileSize' => $maxFileSize,'multipleFiles' => $multipleFiles,'showFilePreview' => $showFilePreview,'acceptedFileTypes' => $acceptedFileTypes,'placeholder' => $placeholder,'useWebsockets' => $useWebSockets,'autoReconnect' => $autoReconnect,'conversationIdKey' => $conversationIdKey]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.communication.chat-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['send-message-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sendMessageUrl),'typing-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($typingUrl),'enable-file-upload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($enableFileUpload),'max-file-size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($maxFileSize),'multiple-files' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($multipleFiles),'show-file-preview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showFilePreview),'accepted-file-types' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($acceptedFileTypes),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($placeholder),'use-websockets' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($useWebSockets),'auto-reconnect' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($autoReconnect),'conversation-id-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversationIdKey)]); ?>
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
    <?php else: ?>
        <div class="flex-1 flex items-center justify-center">
            <?php if (isset($component)) { $__componentOriginal35ede04184a85d1a23bc936778c668e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal35ede04184a85d1a23bc936778c668e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.feedback.empty-state','data' => ['icon' => 'bi-chat-dots','title' => __('daisy::chat.select_conversation'),'size' => 'md']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.feedback.empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi-chat-dots','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::chat.select_conversation')),'size' => 'md']); ?>
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
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/communication/conversation-view.blade.php ENDPATH**/ ?>