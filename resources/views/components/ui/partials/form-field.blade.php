@props([
    'name' => null,          // Required to bind errors/old values
    'label' => null,         // Label text (can be overridden by slot: label)
    'labelClass' => null,    // Extra classes on label
    'error' => null,         // Force an error message (overrides Laravel $errors)
    'hint' => null,          // Help text (can be overridden by slot: hint)
    'required' => false,     // Display required asterisk on label
    'srOnly' => false,       // Screen-reader only label
    'as' => 'div',           // Wrapper tag
    'full' => true,          // Apply w-full on wrapper
    'class' => '',           // Extra classes on wrapper
])

@php
    // Determine final wrapper classes.
    $wrapperClasses = trim(($full ? 'w-full ' : '').'form-control '.$class);

    // Resolve message and state from Laravel validation bag if name is provided.
    $laravelMessage = null;
    if ($name) {
        $laravelMessage = $errors->first($name);
    }
    $message = $error ?? $laravelMessage;
    $hasError = filled($message);

    // Expose commonly used values to the slot content.
    // - $hasError: boolean for conditional classes
    // - $errorMessage: string|null
    // - $oldValue: previous submitted value for this field
    // - $errorClassInput: class string for input error (empty if no error)
    // - $errorClassSelect: class string for select error (empty if no error)
    // - $errorClassTextarea: class string for textarea error (empty if no error)
    $errorMessage = $message;
    $oldValue = $name ? old($name) : null;
    $errorClassInput = $hasError ? 'input-error' : '';
    $errorClassSelect = $hasError ? 'select-error' : '';
    $errorClassTextarea = $hasError ? 'textarea-error' : '';
@endphp

<{{ $as }} {{ $attributes->merge(['class' => $wrapperClasses]) }}>
    @if($label || isset($labelSlot))
        <x-daisy::ui.advanced.label
            :for="$name"
            :srOnly="$srOnly"
            class="{{ $labelClass }}"
        >
            @php($labelText = $label)
            @isset($labelSlot)
                {{ $labelSlot }}
            @else
                {{ $labelText }}
                @if($required)
                    <span aria-hidden="true" class="text-error ml-1">*</span>
                @endif
            @endisset
        </x-daisy::ui.advanced.label>
    @endif

    {{-- Control slot: templates should use old($name) and $errors->has($name) directly --}}
    {{-- The $name prop is available in the component context for templates to use --}}
    {{ $slot }}

    @if(isset($hintSlot) || $hint)
        <p class="label-text-alt mt-1 text-base-content/70">
            @isset($hintSlot)
                {{ $hintSlot }}
            @else
                {{ $hint }}
            @endisset
        </p>
    @endif

    {{-- Validation message using validator component --}}
    @if($errorMessage)
        <x-daisy::ui.advanced.validator state="error" :message="$errorMessage" :full="false" as="div" class="mt-1" />
    @endif
</{{ $as }}>

@once
    @push('styles')
        {{-- No custom CSS – rely only on Tailwind v4 + daisyUI v5 as per guidelines. --}}
    @endpush
@endonce

