@props([
    'weekdays' => ['Lu','Ma','Me','Je','Ve','Sa','Di'],
    'days' => [], // array of ['label' => 1, 'muted' => false, 'active' => false]
])

<div class="grid grid-cols-7 gap-2">
    @foreach($weekdays as $wd)
        <div class="text-xs text-base-content/70 text-center">{{ $wd }}</div>
    @endforeach
    @foreach($days as $d)
        <button class="btn btn-sm {{ ($d['muted'] ?? false) ? 'btn-ghost text-base-content/50' : 'btn-ghost' }} {{ ($d['active'] ?? false) ? 'btn-active' : '' }}">{{ $d['label'] }}</button>
    @endforeach
</div>
