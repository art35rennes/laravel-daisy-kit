@props([
    'name' => 'filter',
    'items' => [], // array of labels or [['label' => 'Tab 1', 'checked' => false]]
    'useForm' => true,
    'resetLabel' => '×', // shown for form reset button
    'allLabel' => 'All', // aria-label for filter-reset when not using form
])

@php
    // Choix du tag wrapper : <form> si useForm (permet reset natif), sinon <div>.
    $WrapperTag = $useForm ? 'form' : 'div';
@endphp

{{-- Filter : groupe de radio buttons pour filtrer (pattern daisyUI) --}}
<{{ $WrapperTag }} {{ $attributes->merge(['class' => 'filter']) }}>
    @if($useForm)
        {{-- Mode form : bouton reset (×) pour réinitialiser tous les filtres --}}
        <input class="btn btn-square" type="reset" value="{{ $resetLabel }}" />
    @else
        {{-- Mode div : radio "All" pour réinitialiser (utilise filter-reset de daisyUI) --}}
        <input class="btn filter-reset" type="radio" name="{{ $name }}" aria-label="{{ $allLabel }}" />
    @endif

    {{-- Options de filtre : radio buttons stylisés comme des boutons --}}
    @foreach($items as $i => $item)
        @php
            // Extraction du label et de l'état checked (support array ou string simple).
            $label = is_array($item) ? ($item['label'] ?? 'Option '.($i+1)) : $item;
            $checked = is_array($item) ? ($item['checked'] ?? false) : false;
        @endphp
        <input class="btn" type="radio" name="{{ $name }}" aria-label="{{ $label }}" @checked($checked) />
    @endforeach
</{{ $WrapperTag }}>
