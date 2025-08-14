@props([
    // Mode d'affichage: inline (ancré au déclencheur) ou modal (centre de l'écran)
    'mode' => 'inline', // inline | modal
    // Position pour le mode inline
    // pris en charge: top | right | bottom | left
    'position' => 'bottom',
    // Contenu du message
    'message' => 'Êtes-vous sûr ?'
        ,
    // Texte des boutons
    'okText' => 'OK',
    'cancelText' => 'Annuler',
    // Classes DaisyUI/Tailwind des boutons
    'okClass' => 'btn-primary',
    'cancelClass' => 'btn-secondary',
    // Largeur du panneau inline
    'panelClass' => 'w-72',
    // Id facultatif (utilisé surtout pour modal)
    'id' => null,
    // Titre de modal (mode modal)
    'title' => null,
])

@php
    $isModal = ($mode === 'modal');
    $rootAttrs = $attributes->class('relative inline-block');

    // Mapping des positions inline vers classes utilitaires
    $posMap = [
        'bottom' => 'top-full left-1/2 -translate-x-1/2 mt-2',
        'top' => 'bottom-full left-1/2 -translate-x-1/2 mb-2',
        'right' => 'left-full top-1/2 -translate-y-1/2 ml-2',
        'left' => 'right-full top-1/2 -translate-y-1/2 mr-2',
    ];
    $panelPos = $posMap[$position] ?? $posMap['bottom'];

    // Id auto si manquant en mode modal
    if ($isModal && empty($id)) {
        $id = 'popconfirm-modal-'.\Illuminate\Support\Str::uuid();
    }
@endphp

@if($isModal)
    <span {{ $rootAttrs }}>
        <span class="popconfirm-trigger" data-popconfirm-modal="{{ $id }}">
            {{ $trigger ?? $slot }}
        </span>

        <x-daisy::ui.modal :id="$id" :title="$title" :backdrop="true">
            <div class="flex items-start gap-3">
                @isset($icon)
                    <span class="mt-1 shrink-0">{{ $icon }}</span>
                @endisset
                <div class="grow">{!! $message !!}</div>
            </div>
            <x-slot:actions>
                @if($cancelText !== '')
                    <button type="button"
                        class="btn {{ $cancelClass }}"
                        data-popconfirm-action="cancel"
                        data-popconfirm-modal-target="{{ $id }}">
                        {{ $cancelText }}
                    </button>
                @endif
                @if($okText !== '')
                    <button type="button"
                        class="btn {{ $okClass }}"
                        data-popconfirm-action="confirm"
                        data-popconfirm-modal-target="{{ $id }}">
                        {{ $okText }}
                    </button>
                @endif
            </x-slot:actions>
        </x-daisy::ui.modal>
    </span>
@else
    <span {{ $rootAttrs->merge(['data-popconfirm' => true, 'data-position' => $position]) }}>
        <span class="popconfirm-trigger cursor-pointer select-none inline-flex items-center" tabindex="0">
            {{ $trigger ?? $slot }}
        </span>
        <div class="popconfirm-panel {{ $panelClass }} absolute z-50 {{ $panelPos }} hidden">
            <div class="rounded-box bg-base-100 shadow border border-base-200 p-4">
                <div class="flex items-start gap-3">
                    @isset($icon)
                        <span class="mt-1 shrink-0">{{ $icon }}</span>
                    @endisset
                    <div class="grow text-sm">{!! $message !!}</div>
                </div>
                <div class="mt-3 flex justify-end gap-2">
                    @if($cancelText !== '')
                        <button type="button" class="btn btn-sm {{ $cancelClass }}" data-popconfirm-action="cancel">{{ $cancelText }}</button>
                    @endif
                    @if($okText !== '')
                        <button type="button" class="btn btn-sm {{ $okClass }}" data-popconfirm-action="confirm">{{ $okText }}</button>
                    @endif
                </div>
            </div>
        </div>
    </span>
@endif


@include('daisy::components.partials.assets')
