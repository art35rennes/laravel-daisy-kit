<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => __('daisy::chat.messages'),
    'theme' => null,
    'conversation' => null,
    'conversations' => [],
    'messages' => [],
    'currentUser' => null,
    'currentUserId' => null,
    'showSidebar' => true,
    'showUserList' => true,
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
    'conversationsUrl' => null,
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
    'conversationLastMessageKey' => 'lastMessage',
    'conversationUnreadCountKey' => 'unreadCount',
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
    'title' => __('daisy::chat.messages'),
    'theme' => null,
    'conversation' => null,
    'conversations' => [],
    'messages' => [],
    'currentUser' => null,
    'currentUserId' => null,
    'showSidebar' => true,
    'showUserList' => true,
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
    'conversationsUrl' => null,
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
    'conversationLastMessageKey' => 'lastMessage',
    'conversationUnreadCountKey' => 'unreadCount',
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

<?php if (isset($component)) { $__componentOriginala7bea3f816103b034498a0cafca82f36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala7bea3f816103b034498a0cafca82f36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.layout.app','data' => ['title' => $title,'theme' => $theme,'container' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::layout.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title),'theme' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($theme),'container' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
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
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showSidebar && !empty($conversations)): ?>
        <?php if (isset($component)) { $__componentOriginala94521fae691f5a72823b6a7ccfb859d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala94521fae691f5a72823b6a7ccfb859d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.overlay.drawer','data' => ['id' => 'chat-sidebar-drawer','responsiveOpen' => 'lg','sideIsMenu' => false,'sideClass' => 'w-80','fullHeight' => false,'dataModule' => 'chat-drawer','dataDrawerId' => 'chat-sidebar-drawer']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.overlay.drawer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'chat-sidebar-drawer','responsiveOpen' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('lg'),'sideIsMenu' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'sideClass' => 'w-80','fullHeight' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'data-module' => 'chat-drawer','data-drawer-id' => 'chat-sidebar-drawer']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

             <?php $__env->slot('content', null, []); ?> 
                <div class="flex h-screen overflow-hidden">
                    <?php if (isset($component)) { $__componentOriginalfda422a0ed29011853fbb7b775b0dba3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfda422a0ed29011853fbb7b775b0dba3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.communication.conversation-view','data' => ['conversation' => $conversation,'messages' => $messages,'currentUserId' => $currentUserId,'showBackButton' => $showBackButton,'backUrl' => $backUrl,'showTypingIndicator' => $showTypingIndicator,'typingUsers' => $typingUsers,'enableFileUpload' => $enableFileUpload,'maxFileSize' => $maxFileSize,'multipleFiles' => $multipleFiles,'showFilePreview' => $showFilePreview,'acceptedFileTypes' => $acceptedFileTypes,'placeholder' => $placeholder,'sendMessageUrl' => $sendMessageUrl,'typingUrl' => $typingUrl,'loadMessagesUrl' => $loadMessagesUrl,'useWebsockets' => $useWebSockets,'pollingInterval' => $pollingInterval,'autoReconnect' => $autoReconnect,'reconnectDelay' => $reconnectDelay,'conversationIdKey' => $conversationIdKey,'conversationNameKey' => $conversationNameKey,'conversationAvatarKey' => $conversationAvatarKey,'conversationIsOnlineKey' => $conversationIsOnlineKey,'messageIdKey' => $messageIdKey,'messageUserIdKey' => $messageUserIdKey,'messageContentKey' => $messageContentKey,'messageCreatedAtKey' => $messageCreatedAtKey,'messageUserNameKey' => $messageUserNameKey,'messageUserAvatarKey' => $messageUserAvatarKey]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.communication.conversation-view'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['conversation' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversation),'messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messages),'current-user-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentUserId),'show-back-button' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showBackButton),'back-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($backUrl),'show-typing-indicator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showTypingIndicator),'typing-users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($typingUsers),'enable-file-upload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($enableFileUpload),'max-file-size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($maxFileSize),'multiple-files' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($multipleFiles),'show-file-preview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showFilePreview),'accepted-file-types' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($acceptedFileTypes),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($placeholder),'send-message-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sendMessageUrl),'typing-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($typingUrl),'load-messages-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($loadMessagesUrl),'use-websockets' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($useWebSockets),'polling-interval' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pollingInterval),'auto-reconnect' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($autoReconnect),'reconnect-delay' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($reconnectDelay),'conversation-id-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversationIdKey),'conversation-name-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversationNameKey),'conversation-avatar-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversationAvatarKey),'conversation-is-online-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversationIsOnlineKey),'message-id-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messageIdKey),'message-user-id-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messageUserIdKey),'message-content-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messageContentKey),'message-created-at-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messageCreatedAtKey),'message-user-name-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messageUserNameKey),'message-user-avatar-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messageUserAvatarKey)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfda422a0ed29011853fbb7b775b0dba3)): ?>
<?php $attributes = $__attributesOriginalfda422a0ed29011853fbb7b775b0dba3; ?>
<?php unset($__attributesOriginalfda422a0ed29011853fbb7b775b0dba3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfda422a0ed29011853fbb7b775b0dba3)): ?>
<?php $component = $__componentOriginalfda422a0ed29011853fbb7b775b0dba3; ?>
<?php unset($__componentOriginalfda422a0ed29011853fbb7b775b0dba3); ?>
<?php endif; ?>
                </div>
             <?php $__env->endSlot(); ?>
             <?php $__env->slot('side', null, []); ?> 
                
                <?php if (isset($component)) { $__componentOriginal7b73de764c489d1e166e95075a6aa1b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7b73de764c489d1e166e95075a6aa1b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.communication.chat-sidebar','data' => ['conversations' => $conversations,'currentConversationId' => data_get($conversation, $conversationIdKey),'showUserList' => $showUserList,'conversationsUrl' => $conversationsUrl,'conversationIdKey' => $conversationIdKey,'conversationNameKey' => $conversationNameKey,'conversationAvatarKey' => $conversationAvatarKey,'conversationLastMessageKey' => $conversationLastMessageKey,'conversationUnreadCountKey' => $conversationUnreadCountKey,'conversationIsOnlineKey' => $conversationIsOnlineKey]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.communication.chat-sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['conversations' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversations),'current-conversation-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(data_get($conversation, $conversationIdKey)),'show-user-list' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showUserList),'conversations-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversationsUrl),'conversation-id-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversationIdKey),'conversation-name-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversationNameKey),'conversation-avatar-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversationAvatarKey),'conversation-last-message-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversationLastMessageKey),'conversation-unread-count-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversationUnreadCountKey),'conversation-is-online-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversationIsOnlineKey)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7b73de764c489d1e166e95075a6aa1b4)): ?>
<?php $attributes = $__attributesOriginal7b73de764c489d1e166e95075a6aa1b4; ?>
<?php unset($__attributesOriginal7b73de764c489d1e166e95075a6aa1b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7b73de764c489d1e166e95075a6aa1b4)): ?>
<?php $component = $__componentOriginal7b73de764c489d1e166e95075a6aa1b4; ?>
<?php unset($__componentOriginal7b73de764c489d1e166e95075a6aa1b4); ?>
<?php endif; ?>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala94521fae691f5a72823b6a7ccfb859d)): ?>
<?php $attributes = $__attributesOriginala94521fae691f5a72823b6a7ccfb859d; ?>
<?php unset($__attributesOriginala94521fae691f5a72823b6a7ccfb859d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala94521fae691f5a72823b6a7ccfb859d)): ?>
<?php $component = $__componentOriginala94521fae691f5a72823b6a7ccfb859d; ?>
<?php unset($__componentOriginala94521fae691f5a72823b6a7ccfb859d); ?>
<?php endif; ?>
    <?php else: ?>
        <div class="flex h-screen overflow-hidden">
            <?php if (isset($component)) { $__componentOriginalfda422a0ed29011853fbb7b775b0dba3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfda422a0ed29011853fbb7b775b0dba3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.communication.conversation-view','data' => ['conversation' => $conversation,'messages' => $messages,'currentUserId' => $currentUserId,'showBackButton' => $showBackButton,'backUrl' => $backUrl,'showTypingIndicator' => $showTypingIndicator,'typingUsers' => $typingUsers,'enableFileUpload' => $enableFileUpload,'maxFileSize' => $maxFileSize,'multipleFiles' => $multipleFiles,'showFilePreview' => $showFilePreview,'acceptedFileTypes' => $acceptedFileTypes,'placeholder' => $placeholder,'sendMessageUrl' => $sendMessageUrl,'typingUrl' => $typingUrl,'loadMessagesUrl' => $loadMessagesUrl,'useWebsockets' => $useWebSockets,'pollingInterval' => $pollingInterval,'autoReconnect' => $autoReconnect,'reconnectDelay' => $reconnectDelay,'conversationIdKey' => $conversationIdKey,'conversationNameKey' => $conversationNameKey,'conversationAvatarKey' => $conversationAvatarKey,'conversationIsOnlineKey' => $conversationIsOnlineKey,'messageIdKey' => $messageIdKey,'messageUserIdKey' => $messageUserIdKey,'messageContentKey' => $messageContentKey,'messageCreatedAtKey' => $messageCreatedAtKey,'messageUserNameKey' => $messageUserNameKey,'messageUserAvatarKey' => $messageUserAvatarKey]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.communication.conversation-view'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['conversation' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversation),'messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messages),'current-user-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentUserId),'show-back-button' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showBackButton),'back-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($backUrl),'show-typing-indicator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showTypingIndicator),'typing-users' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($typingUsers),'enable-file-upload' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($enableFileUpload),'max-file-size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($maxFileSize),'multiple-files' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($multipleFiles),'show-file-preview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showFilePreview),'accepted-file-types' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($acceptedFileTypes),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($placeholder),'send-message-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sendMessageUrl),'typing-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($typingUrl),'load-messages-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($loadMessagesUrl),'use-websockets' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($useWebSockets),'polling-interval' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pollingInterval),'auto-reconnect' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($autoReconnect),'reconnect-delay' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($reconnectDelay),'conversation-id-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversationIdKey),'conversation-name-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversationNameKey),'conversation-avatar-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversationAvatarKey),'conversation-is-online-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($conversationIsOnlineKey),'message-id-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messageIdKey),'message-user-id-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messageUserIdKey),'message-content-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messageContentKey),'message-created-at-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messageCreatedAtKey),'message-user-name-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messageUserNameKey),'message-user-avatar-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messageUserAvatarKey)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfda422a0ed29011853fbb7b775b0dba3)): ?>
<?php $attributes = $__attributesOriginalfda422a0ed29011853fbb7b775b0dba3; ?>
<?php unset($__attributesOriginalfda422a0ed29011853fbb7b775b0dba3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfda422a0ed29011853fbb7b775b0dba3)): ?>
<?php $component = $__componentOriginalfda422a0ed29011853fbb7b775b0dba3; ?>
<?php unset($__componentOriginalfda422a0ed29011853fbb7b775b0dba3); ?>
<?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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

<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/templates/communication/chat.blade.php ENDPATH**/ ?>