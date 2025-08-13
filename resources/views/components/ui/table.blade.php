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
    // Sélection: 'none' | 'single' | 'multiple'
    'selection' => 'none',
    // Gestion multipage: total items et offset (1-based index de la première ligne affichée)
    'total' => null,
    'offset' => 1,
    // Afficher numéros de ligne
    'showRowNumbers' => false,
    // Overflow
    'overflowX' => true,
    'overflowY' => false,
])

@php
    $classes = 'table';
    if ($zebra) $classes .= ' table-zebra';
    if ($size) $classes .= ' table-'.$size;
    if ($pinRows) $classes .= ' table-pin-rows';
    if ($pinCols) $classes .= ' table-pin-cols';
@endphp

@php
    $outerOverflow = '';
    if ($overflowX) $outerOverflow .= ' overflow-x-auto';
    if ($overflowY) $outerOverflow .= ' overflow-y-auto max-h-full';

    $withSelection = in_array($selection, ['single','multiple'], true);
    $hasNumberCol = $showRowNumbers || $withSelection;
@endphp

<div class="{{ trim($outerOverflow) }} {{ $containerClass }}">
    <table {{ $attributes->merge(['class' => $classes]) }} data-table-select="{{ $selection }}">
        @if($caption)
            <caption class="text-start text-xs opacity-70 mb-2">{{ $caption }}</caption>
        @endif
        @if(!empty($headers))
            <thead>
                <tr>
                    @if($hasNumberCol)
                        <th class="w-0"></th>
                    @endif
                    @foreach($headers as $h)
                        <th>{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
        @endif
        @if(!empty($rows))
            <tbody>
                @foreach($rows as $rIndex => $row)
                    <tr>
                        @if($hasNumberCol)
                            <th class="w-0">
                                <div class="flex items-center gap-2">
                                    @if($showRowNumbers)
                                        <span class="text-xs opacity-70" data-row-number>{{ is_null($total) ? ($offset + $rIndex) : ($offset + $rIndex) }}</span>
                                    @endif
                                    @if($selection === 'single')
                                        <input type="radio" name="tbl_select" class="radio radio-sm" data-row-select>
                                    @elseif($selection === 'multiple')
                                        <input type="checkbox" class="checkbox checkbox-sm" data-row-select>
                                    @endif
                                </div>
                            </th>
                        @endif
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
                        @if($hasNumberCol)
                            <th></th>
                        @endif
                        @foreach($footer as $f)
                            <th>{!! $f !!}</th>
                        @endforeach
                    </tr>
                </tfoot>
            @endif
        @else
            <tbody>
                @if(trim($slot) !== '')
                    {{ $slot }}
                @else
                    <tr><td class="text-center opacity-70">No data</td></tr>
                @endif
            </tbody>
        @endif
    </table>
</div>

@include('daisy::components.partials.assets')
