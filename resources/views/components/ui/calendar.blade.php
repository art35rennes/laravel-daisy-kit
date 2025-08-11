@props([
    'provider' => 'cally', // cally | native
    // Props forwards
    'withMonth' => true,
    'showPrevNext' => true,
    'inputId' => null,
    'value' => null,
    'placeholder' => null,
])

@if($provider === 'native')
    <x-daisy::ui.calendar-native :inputId="$inputId" :value="$value" :placeholder="$placeholder" {{ $attributes }} />
@else
    <x-daisy::ui.calendar-cally :withMonth="$withMonth" :showPrevNext="$showPrevNext" {{ $attributes }}>
        {{ $slot }}
    </x-daisy::ui.calendar-cally>
@endif
