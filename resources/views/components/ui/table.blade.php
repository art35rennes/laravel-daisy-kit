@props([
    'headers' => [],
    'rows' => [], // array of arrays
    'zebra' => false,
    'size' => null, // xs|sm|md|lg|xl
    'pinRows' => false,
    'pinCols' => false,
    'rowHeaders' => true, // use <th> for first cell in each row (recommended by daisyUI)
    // Footer row (array of cells) → renders <tfoot>
    'footer' => [],
    // Optional caption for <table>
    'caption' => null,
    // Extra classes for the outer container div
    'containerClass' => '',
])

@php
    $classes = 'table';
    if ($zebra) $classes .= ' table-zebra';
    if ($size) $classes .= ' table-'.$size;
    if ($pinRows) $classes .= ' table-pin-rows';
    if ($pinCols) $classes .= ' table-pin-cols';
@endphp

<div class="overflow-x-auto {{ $containerClass }}">
    <table {{ $attributes->merge(['class' => $classes]) }}>
        @if($caption)
            <caption class="text-start text-xs opacity-70 mb-2">{{ $caption }}</caption>
        @endif
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
            @if(!empty($footer))
                <tfoot>
                    <tr>
                        @foreach($footer as $f)
                            <th>{!! $f !!}</th>
                        @endforeach
                    </tr>
                </tfoot>
            @endif
        @else
            <tbody>{{ $slot }}</tbody>
        @endif
    </table>
</div>
