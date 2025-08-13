@props([
    'provider' => 'cally', // cally | native
    // Props forwards
    'mode' => 'date',     // date | range | multi (cally)
    'months' => 1,        // (cally)
    'showPrevNext' => true,
    'inputId' => null,
    'value' => null,
    'min' => null,
    'max' => null,
    'locale' => null,
    'placeholder' => null,
])

@if($provider === 'native')
    <x-daisy::ui.calendar-native :inputId="$inputId" :value="$value" :placeholder="$placeholder" {{ $attributes }} />
@else
    <x-daisy::ui.calendar-cally
        :mode="$mode"
        :months="$months"
        :showPrevNext="$showPrevNext"
        :value="$value"
        :min="$min"
        :max="$max"
        :locale="$locale"
        {{ $attributes }}
    >
        {{ $slot }}
    </x-daisy::ui.calendar-cally>
@endif
