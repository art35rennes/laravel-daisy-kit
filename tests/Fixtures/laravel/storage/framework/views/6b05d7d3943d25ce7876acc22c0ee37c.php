<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'messages' => [],
    'currentUserId' => null,
    'showTypingIndicator' => true,
    'typingUsers' => [],
    'loadMessagesUrl' => null,
    // Options REST/WebSocket
    'useWebSockets' => false,
    'pollingInterval' => 3000,
    'autoReconnect' => true,
    'reconnectDelay' => 5000,
    // Data accessors
    'messageIdKey' => 'id',
    'messageUserIdKey' => 'user_id',
    'messageContentKey' => 'content',
    'messageCreatedAtKey' => 'created_at',
    'messageUserNameKey' => 'user_name',
    'messageUserAvatarKey' => 'user_avatar',
    'messageAttachmentKey' => 'attachment',
    'messageAttachmentsKey' => 'attachments',
    // Module override
    'module' => 'chat-messages',
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
    'messages' => [],
    'currentUserId' => null,
    'showTypingIndicator' => true,
    'typingUsers' => [],
    'loadMessagesUrl' => null,
    // Options REST/WebSocket
    'useWebSockets' => false,
    'pollingInterval' => 3000,
    'autoReconnect' => true,
    'reconnectDelay' => 5000,
    // Data accessors
    'messageIdKey' => 'id',
    'messageUserIdKey' => 'user_id',
    'messageContentKey' => 'content',
    'messageCreatedAtKey' => 'created_at',
    'messageUserNameKey' => 'user_name',
    'messageUserAvatarKey' => 'user_avatar',
    'messageAttachmentKey' => 'attachment',
    'messageAttachmentsKey' => 'attachments',
    // Module override
    'module' => 'chat-messages',
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

    $loadMessagesUrl = $loadMessagesUrl ?? (Route::has('chat.messages') ? route('chat.messages', ':conversationId') : '#');
    $loadMessagesUrl = $normalizeEndpoint($loadMessagesUrl);
?>

<div 
    <?php echo e($attributes->merge(['class' => 'chat-messages flex flex-col overflow-y-auto p-3 sm:p-4 space-y-3 sm:space-y-4'])); ?>

    data-module="<?php echo e($module); ?>"
    data-load-messages-url="<?php echo e($loadMessagesUrl !== '#' ? $loadMessagesUrl : ''); ?>"
    data-current-user-id="<?php echo e($currentUserId); ?>"
    data-use-websockets="<?php echo e($useWebSockets ? 'true' : 'false'); ?>"
    data-polling-interval="<?php echo e($pollingInterval); ?>"
    data-auto-reconnect="<?php echo e($autoReconnect ? 'true' : 'false'); ?>"
    data-reconnect-delay="<?php echo e($reconnectDelay); ?>"
    data-message-id-key="<?php echo e($messageIdKey); ?>"
    data-message-user-id-key="<?php echo e($messageUserIdKey); ?>"
    data-message-content-key="<?php echo e($messageContentKey); ?>"
    data-message-created-at-key="<?php echo e($messageCreatedAtKey); ?>"
    data-message-user-name-key="<?php echo e($messageUserNameKey); ?>"
    data-message-user-avatar-key="<?php echo e($messageUserAvatarKey); ?>"
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($messages)): ?>
        <?php if (isset($component)) { $__componentOriginal35ede04184a85d1a23bc936778c668e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal35ede04184a85d1a23bc936778c668e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.feedback.empty-state','data' => ['icon' => 'bi-chat-dots','title' => __('daisy::chat.no_messages'),'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.feedback.empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'bi-chat-dots','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::chat.no_messages')),'size' => 'sm']); ?>
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
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                // Extraction des propriétés du message via data_get (support des clés personnalisables).
                $messageId = data_get($message, $messageIdKey);
                $messageUserId = data_get($message, $messageUserIdKey);
                $messageContent = data_get($message, $messageContentKey, '');
                $messageCreatedAt = data_get($message, $messageCreatedAtKey);
                $messageUserName = data_get($message, $messageUserNameKey, '');
                $messageUserAvatar = data_get($message, $messageUserAvatarKey);
                
                // Récupération des pièces jointes : support d'une pièce unique (attachment) ou multiple (attachments).
                $attachment = data_get($message, $messageAttachmentKey);
                $attachments = data_get($message, $messageAttachmentsKey, []);
                // Normalisation : si une seule pièce est fournie, la convertir en tableau.
                if ($attachment && !$attachments) {
                    $attachments = [$attachment];
                }
                
                // Détermination de l'alignement : messages de l'utilisateur courant à droite (end), autres à gauche (start).
                $isCurrentUser = $currentUserId && (string) $messageUserId === (string) $currentUserId;
                $align = $isCurrentUser ? 'end' : 'start';
                
                // Formatage de la date : conversion en Carbon si string, formatage en H:i (ex: "14:30").
                $dateFormatted = null;
                if ($messageCreatedAt) {
                    try {
                        $date = is_string($messageCreatedAt) ? \Carbon\Carbon::parse($messageCreatedAt) : $messageCreatedAt;
                        $dateFormatted = $date->format('H:i');
                    } catch (\Exception $e) {
                        // Fallback : utiliser la valeur brute si le parsing échoue.
                        $dateFormatted = $messageCreatedAt;
                    }
                }
            ?>

            <?php if (isset($component)) { $__componentOriginal0b4d03bc26b7f1311a48239e06692eea = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0b4d03bc26b7f1311a48239e06692eea = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.communication.chat-bubble','data' => ['align' => $align,'name' => $isCurrentUser ? null : $messageUserName,'time' => $dateFormatted,'color' => $isCurrentUser ? 'primary' : null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.communication.chat-bubble'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['align' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($align),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isCurrentUser ? null : $messageUserName),'time' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dateFormatted),'color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isCurrentUser ? 'primary' : null)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                 <?php $__env->slot('avatar', null, []); ?> 
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($messageUserAvatar || $messageUserName): ?>
                        <?php if (isset($component)) { $__componentOriginalc4b515aecc51170d16885bb5fad2ac22 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc4b515aecc51170d16885bb5fad2ac22 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.avatar','data' => ['src' => $messageUserAvatar,'placeholder' => $messageUserName ? substr($messageUserName, 0, 1) : 'U','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['src' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messageUserAvatar),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($messageUserName ? substr($messageUserName, 0, 1) : 'U'),'size' => 'sm']); ?>
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
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                 <?php $__env->endSlot(); ?>

                <div class="space-y-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($attachments)): ?>
                        <div class="space-y-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php
                                    $attUrl = is_array($att) ? data_get($att, 'url') : $att;
                                    $attName = is_array($att) ? data_get($att, 'name') : null;
                                    $attType = is_array($att) ? data_get($att, 'type') : null;
                                    $attSize = is_array($att) ? data_get($att, 'size') : null;
                                ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attUrl): ?>
                                    <?php if (isset($component)) { $__componentOriginala4dfa6a94ef363608092170c3b3bbfe9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala4dfa6a94ef363608092170c3b3bbfe9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.file-preview','data' => ['url' => $attUrl,'name' => $attName,'type' => $attType,'fileSize' => $attSize,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.file-preview'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attUrl),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attName),'type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attType),'file-size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attSize),'size' => 'sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala4dfa6a94ef363608092170c3b3bbfe9)): ?>
<?php $attributes = $__attributesOriginala4dfa6a94ef363608092170c3b3bbfe9; ?>
<?php unset($__attributesOriginala4dfa6a94ef363608092170c3b3bbfe9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala4dfa6a94ef363608092170c3b3bbfe9)): ?>
<?php $component = $__componentOriginala4dfa6a94ef363608092170c3b3bbfe9; ?>
<?php unset($__componentOriginala4dfa6a94ef363608092170c3b3bbfe9); ?>
<?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($messageContent): ?>
                        <div><?php echo e($messageContent); ?></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0b4d03bc26b7f1311a48239e06692eea)): ?>
<?php $attributes = $__attributesOriginal0b4d03bc26b7f1311a48239e06692eea; ?>
<?php unset($__attributesOriginal0b4d03bc26b7f1311a48239e06692eea); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0b4d03bc26b7f1311a48239e06692eea)): ?>
<?php $component = $__componentOriginal0b4d03bc26b7f1311a48239e06692eea; ?>
<?php unset($__componentOriginal0b4d03bc26b7f1311a48239e06692eea); ?>
<?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showTypingIndicator && !empty($typingUsers)): ?>
        <div class="chat-typing-indicator flex items-center gap-2 text-sm text-base-content opacity-70">
            <?php if (isset($component)) { $__componentOriginald4af959213ada05dacebaae2eb0906c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4af959213ada05dacebaae2eb0906c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.feedback.loading','data' => ['shape' => 'dots','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.feedback.loading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['shape' => 'dots','size' => 'sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4af959213ada05dacebaae2eb0906c2)): ?>
<?php $attributes = $__attributesOriginald4af959213ada05dacebaae2eb0906c2; ?>
<?php unset($__attributesOriginald4af959213ada05dacebaae2eb0906c2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4af959213ada05dacebaae2eb0906c2)): ?>
<?php $component = $__componentOriginald4af959213ada05dacebaae2eb0906c2; ?>
<?php unset($__componentOriginald4af959213ada05dacebaae2eb0906c2); ?>
<?php endif; ?>
            <span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $typingUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php echo e($index > 0 ? ', ' : ''); ?><?php echo e(data_get($user, 'name', 'Quelqu\'un')); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php echo e(__('daisy::chat.typing', ['name' => ''])); ?>

            </span>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/communication/chat-messages.blade.php ENDPATH**/ ?>