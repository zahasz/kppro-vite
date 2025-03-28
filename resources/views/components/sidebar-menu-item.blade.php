@props(['route', 'icon', 'label', 'routeMatch'])

@php
    $isActive = request()->routeIs($routeMatch);
    $activeClass = $isActive ? 'bg-[#44546A]/80 text-white shadow-sm font-medium border-l-4 border-[#44546A]/50 pl-3' : 'text-white hover:bg-[#44546A]/40 hover:text-white';
    $iconClass = $isActive ? 'text-white' : 'text-white/70 group-hover:text-white';
    $textClass = $isActive ? 'font-semibold' : 'font-medium';
@endphp

<a href="{{ route($route) }}" 
   class="flex items-center px-4 py-2.5 rounded-md {{ $activeClass }} transition-all duration-200 group">
    <i class="{{ $icon }} w-5 h-5 text-center {{ $iconClass }}"></i>
    <span class="ml-3 text-sm {{ $textClass }}" x-show="sidebarOpen">{{ $label }}</span>
</a> 