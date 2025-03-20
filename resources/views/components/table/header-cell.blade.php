@props(['align' => 'left'])

@php
$classes = 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider';
@endphp

<th {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</th> 