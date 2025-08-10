@props([
    'name' => 'filter',
    'items' => [], // array of labels or [['label' => 'Tab 1', 'checked' => false]]
    'useForm' => true,
])

@php
    $WrapperTag = $useForm ? 'form' : 'div';
@endphp

<{{ $WrapperTag }} {{ $attributes->merge(['class' => 'filter']) }}>
    @if($useForm)
        <input class="btn btn-square" type="reset" value="×" />
    @else
        <input class="btn filter-reset" type="radio" name="{{ $name }}" aria-label="×" />
    @endif

    @foreach($items as $i => $item)
        @php
            $label = is_array($item) ? ($item['label'] ?? 'Option '.$i+1) : $item;
            $checked = is_array($item) ? ($item['checked'] ?? false) : false;
        @endphp
        <input class="btn" type="radio" name="{{ $name }}" aria-label="{{ $label }}" @checked($checked) />
    @endforeach
</{{ $WrapperTag }}>
