@props([
    'id' => null,
    'title' => 'Confirmer cette action',
    'message' => 'Cette action est irréversible.',
    'detail' => null,
    'color' => 'error',
    'icon' => null,
    'cancelText' => 'Annuler',
    'confirmText' => 'Confirmer',
    'confirmVariant' => 'solid',
    'cancelVariant' => 'outline',
    'module' => null,
])

@php
    $id ??= 'destructive-confirm-'.\Illuminate\Support\Str::uuid();
    $resolvedColor = in_array($color, ['warning', 'error'], true) ? $color : 'error';
    $iconName = $icon ?: ($resolvedColor === 'warning' ? 'bi-exclamation-triangle' : 'bi-exclamation-octagon');
    $toneClasses = $resolvedColor === 'warning'
        ? 'text-warning bg-warning/10'
        : 'text-error bg-error/10';
    $confirmClass = trim('btn btn-'.$resolvedColor.' '.($confirmVariant === 'outline' ? 'btn-outline' : ''));
    $cancelClass = trim('btn btn-neutral '.($cancelVariant === 'solid' ? '' : 'btn-outline'));
@endphp

<span {{ $attributes->class('relative inline-block')->merge(['data-module' => ($module ?? 'popconfirm')]) }}>
    <span class="popconfirm-trigger" data-popconfirm-modal="{{ $id }}">
        {{ $trigger ?? $slot }}
    </span>

    <x-daisy::ui.overlay.modal :id="$id" :title="$title" :backdrop="true">
        <div class="flex items-start gap-3">
            <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full {{ $toneClasses }}">
                <x-icon :name="$iconName" class="size-5" />
            </span>
            <div class="min-w-0">
                <p class="text-sm font-medium text-base-content">{{ $message }}</p>
                @if($detail)
                    <p class="mt-2 text-sm text-base-content/70">{{ $detail }}</p>
                @endif
            </div>
        </div>

        <x-slot:actions>
            @if($cancelText !== '')
                <button type="button" class="{{ $cancelClass }}" data-popconfirm-action="cancel" data-popconfirm-modal-target="{{ $id }}">
                    {{ $cancelText }}
                </button>
            @endif
            @if($confirmText !== '')
                <button type="button" class="{{ $confirmClass }}" data-popconfirm-action="confirm" data-popconfirm-modal-target="{{ $id }}">
                    {{ $confirmText }}
                </button>
            @endif
        </x-slot:actions>
    </x-daisy::ui.overlay.modal>
</span>

@include('daisy::components.partials.assets')
