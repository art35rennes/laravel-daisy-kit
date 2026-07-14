@props([
    'provider' => 'cally',
    'mode' => 'date',
    'months' => 1,
    'showPrevNext' => true,
    'inputId' => null,
    'value' => null,
    'min' => null,
    'max' => null,
    'locale' => null,
    'placeholder' => null,
    'firstDay' => 1,
    'type' => null,
    'options' => [],
    'valueSeparator' => ',',
])

@if($provider === 'native')
    <x-daisy::ui.advanced.calendar-native
        :inputId="$inputId"
        :value="$value"
        :min="$min"
        :max="$max"
        :placeholder="$placeholder"
        {{ $attributes }}
    />
@elseif($provider === 'vanilla')
    <x-daisy::ui.advanced.calendar-vanilla
        :inputId="$inputId"
        :mode="$mode"
        :months="$months"
        :showPrevNext="$showPrevNext"
        :value="$value"
        :min="$min"
        :max="$max"
        :locale="$locale"
        :firstDay="$firstDay"
        :type="$type"
        :options="$options"
        :valueSeparator="$valueSeparator"
        {{ $attributes }}
    />
@else
    <x-daisy::ui.advanced.calendar-cally
        :mode="$mode"
        :months="$months"
        :showPrevNext="$showPrevNext"
        :value="$value"
        :min="$min"
        :max="$max"
        :locale="$locale"
        {{ $attributes }}
    >
        @isset($previous)
            <x-slot:previous>{{ $previous }}</x-slot:previous>
        @endisset
        @isset($next)
            <x-slot:next>{{ $next }}</x-slot:next>
        @endisset
        @isset($heading)
            <x-slot:heading>{{ $heading }}</x-slot:heading>
        @endisset
        {{ $slot }}
    </x-daisy::ui.advanced.calendar-cally>
@endif
