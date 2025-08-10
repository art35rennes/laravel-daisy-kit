@props([
    'values' => [], // e.g. ['days' => 15, 'hours' => 10, 'min' => 24, 'sec' => 39]
])

<div class="grid grid-flow-col gap-5 text-center auto-cols-max">
    @foreach($values as $label => $val)
        <div class="flex flex-col">
            <span class="countdown font-mono text-4xl">
                <span style="--value: {{ (int) $val }};"></span>
            </span>
            <span class="text-xs uppercase">{{ $label }}</span>
        </div>
    @endforeach
</div>
