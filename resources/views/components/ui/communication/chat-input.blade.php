@props([
    'sendMessageUrl' => null,
    'typingUrl' => null,
    'enableFileUpload' => false,
    'maxFileSize' => 5120, // KB
    'multipleFiles' => false,
    'showFilePreview' => true,
    'acceptedFileTypes' => 'image/*,application/pdf,.doc,.docx',
    'placeholder' => __('chat.type_message'),
    // Options REST/WebSocket
    'useWebSockets' => false,
    'autoReconnect' => true,
    // Data accessors
    'conversationIdKey' => 'id',
])

@php
    $sendMessageUrl = $sendMessageUrl ?? (Route::has('chat.send') ? route('chat.send') : '#');
    $typingUrl = $typingUrl ?? (Route::has('chat.typing') ? route('chat.typing') : '#');
@endphp

<div 
    {{ $attributes->merge(['class' => 'chat-input flex flex-col gap-2 p-3 sm:p-4 border-t bg-base-100']) }}
    data-module="chat-input"
    data-send-message-url="{{ $sendMessageUrl }}"
    data-typing-url="{{ $typingUrl !== '#' ? $typingUrl : '' }}"
    data-enable-file-upload="{{ $enableFileUpload ? 'true' : 'false' }}"
    data-max-file-size="{{ $maxFileSize }}"
    data-multiple-files="{{ $multipleFiles ? 'true' : 'false' }}"
    data-show-file-preview="{{ $showFilePreview ? 'true' : 'false' }}"
    data-use-websockets="{{ $useWebSockets ? 'true' : 'false' }}"
    data-auto-reconnect="{{ $autoReconnect ? 'true' : 'false' }}"
>
    @if($enableFileUpload && $showFilePreview)
        <div class="hidden flex flex-wrap gap-2 p-2 bg-base-200 rounded-box card-border" data-file-previews></div>
    @endif

    <div class="flex items-end gap-2">
        @if($enableFileUpload)
            <label class="btn btn-ghost btn-circle btn-sm relative">
                <x-icon name="bi-paperclip" class="w-5 h-5" />
                <input
                    type="file"
                    class="hidden"
                    data-file-input
                    @if($multipleFiles) multiple @endif
                    accept="{{ $acceptedFileTypes }}"
                />
                <span class="absolute -top-1 -right-1">
                    <x-daisy::ui.data-display.badge
                        size="xs"
                        color="primary"
                        class="hidden"
                        data-file-count-badge
                    >
                        0
                    </x-daisy::ui.data-display.badge>
                </span>
            </label>
        @endif

        <div class="flex-1">
            <x-daisy::ui.inputs.textarea
                rows="1"
                :placeholder="$placeholder"
                class="resize-none"
                data-message-input
            />
        </div>

        <x-daisy::ui.inputs.button
            type="button"
            variant="solid"
            color="primary"
            size="md"
            circle
            data-send-button
        >
            <x-icon name="bi-send" class="w-5 h-5" />
        </x-daisy::ui.inputs.button>
    </div>
</div>

