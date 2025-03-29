@props(['title' => null, 'header' => null])

<x-layouts.admin :header="$header ?? 'Panel administratora'">
    {{ $slot }}
</x-layouts.admin> 