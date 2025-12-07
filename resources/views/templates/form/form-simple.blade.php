{{--
    Simple Form Template

    One-page form layout template for simple and inline form patterns.

    The template receives:
    - elements: Rendered HTML string of all form elements
    - title: Optional form title
    - action/method: Form submission target
    - submitText: Submit button label
    - showActions: Whether to show action buttons (default: true)
--}}

@props([
    'title' => null,
    'action' => '#',
    'method' => 'POST',
    'elements' => null,
    'submitText' => __('daisy::form.submit') ?? 'Submit',
])

@php
    // Génération d'un ID unique pour le formulaire si non fourni.
    $formId = $attributes->get('id') ?? 'form-simple-'.uniqid();
@endphp

<form
    id="{{ $formId }}"
    action="{{ $action }}"
    method="{{ strtoupper($method) }}"
    class="space-y-6"
>
    {{-- Protection CSRF : requise pour toutes les méthodes sauf GET --}}
    @if(strtoupper($method) !== 'GET')
        @csrf
    @endif

    {{-- Méthode spoofing : Laravel simule PUT/PATCH/DELETE via POST + @method --}}
    @if(strtoupper($method) !== 'GET' && strtoupper($method) !== 'POST')
        @method($method)
    @endif


    {{-- Titre optionnel du formulaire --}}
    @if($title)
        <h2 class="text-2xl font-semibold">{{ $title }}</h2>
    @endif

    {{-- Contenu du formulaire --}}
    <div class="space-y-4">
        {!! $elements !!}
    </div>

    {{-- Actions du formulaire : slot personnalisé ou bouton submit par défaut --}}
    @if(isset($actions) || ($showActions ?? true))
        <div class="flex items-center justify-end pt-4 border-t">
            @isset($actions)
                {!! $actions !!}
            @elseif($showActions ?? true)
                <x-daisy::ui.inputs.button type="submit" color="primary">
                    {{ $submitText }}
                </x-daisy::ui.inputs.button>
            @endisset
        </div>
    @endif
</form>


