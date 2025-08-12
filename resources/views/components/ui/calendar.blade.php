@props([
    'provider' => 'cally', // cally | native
    // Props forwards
    'mode' => 'date',     // date | range (cally)
    'months' => 1,        // (cally)
    'showPrevNext' => true,
    'inputId' => null,
    'value' => null,
    'placeholder' => null,
])

@if($provider === 'native')
    <x-daisy::ui.calendar-native :inputId="$inputId" :value="$value" :placeholder="$placeholder" {{ $attributes }} />
@else
    <x-daisy::ui.calendar-cally :mode="$mode" :months="$months" :showPrevNext="$showPrevNext" {{ $attributes }}>
        {{ $slot }}
    </x-daisy::ui.calendar-cally>
@endif
