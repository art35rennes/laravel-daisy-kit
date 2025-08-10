@props([
    'items' => [], // [['label' => 'Home', 'href' => '/'], ...]
])

<div class="breadcrumbs text-sm">
    <ul>
        @foreach($items as $item)
            <li>
                @if(!empty($item['href']))
                    <a href="{{ $item['href'] }}">{{ $item['label'] }}</a>
                @else
                    <span class="font-medium">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ul>
 </div>


