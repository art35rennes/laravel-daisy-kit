@props([
    'headers' => [],
    'rows' => [], // array of arrays
    'zebra' => false,
    'size' => null, // xs|sm|md|lg
    'pinRows' => false,
    'pinCols' => false,
    'rowHeaders' => true, // use <th> for first cell in each row (recommended by daisyUI)
])

@php
    $classes = 'table';
    if ($zebra) $classes .= ' table-zebra';
    if ($size) $classes .= ' table-'.$size;
    if ($pinRows) $classes .= ' table-pin-rows';
    if ($pinCols) $classes .= ' table-pin-cols';
@endphp

<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => $classes]) }}>
        @if(!empty($headers))
            <thead>
                <tr>
                    @foreach($headers as $h)
                        <th>{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
        @endif
        @if(!empty($rows))
            <tbody>
                @foreach($rows as $row)
                    <tr>
                        @foreach($row as $cellIndex => $cell)
                            @if($rowHeaders && $cellIndex === 0)
                                <th>{!! $cell !!}</th>
                            @else
                                <td>{!! $cell !!}</td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        @else
            <tbody>{{ $slot }}</tbody>
        @endif
    </table>
</div>
