@props(['route', 'icon', 'label', 'routeMatch'])

@php
    $isActive = request()->routeIs($routeMatch);
    $activeClass = $isActive ? 'bg-steel-blue-800 text-white dark:bg-gray-800 dark:text-white' : 'text-slate-300 hover:bg-steel-blue-800 hover:text-white dark:hover:bg-gray-800';
    $iconClass = $isActive ? 'text-white' : 'text-slate-400 group-hover:text-slate-300';
    $textClass = $isActive ? 'font-semibold' : 'font-medium';
@endphp

<a href="{{ route($route) }}" 
   data-turbo="true"
   @if($isActive) aria-current="page" @endif
   class="flex items-center px-2 py-1.5 rounded-md {{ $activeClass }} transition-all duration-200 group">
    <i class="{{ $icon }} w-5 h-5 text-center {{ $iconClass }}"></i>
    <span class="ml-3 text-sm {{ $textClass }}" x-show="sidebarOpen">{{ $label }}</span>
</a> 