<?php extract((new \Illuminate\Support\Collection($attributes->getAttributes()))->mapWithKeys(function ($value, $key) { return [Illuminate\Support\Str::camel(str_replace([':', '.'], ' ', $key)) => $value]; })->all(), EXTR_SKIP); ?>

<x-bi-chevron-double-right  {{ $attributes }}>

{{ $slot ?? "" }}
</x-bi-chevron-double-right>