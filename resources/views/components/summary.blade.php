@props([
    'title' => null,
    'description' => null,
    'icon' => null,
    'variant' => 'primary', // primary, success, warning, danger, info
    'cardStyle' => true,
])

@php
    $variantClasses = [
        'primary' => 'bg-white dark:bg-gray-800 text-steel-blue-700 dark:text-steel-blue-300',
        'success' => 'bg-white dark:bg-gray-800 text-emerald-700 dark:text-emerald-300',
        'warning' => 'bg-white dark:bg-gray-800 text-amber-700 dark:text-amber-300',
        'danger' => 'bg-white dark:bg-gray-800 text-red-700 dark:text-red-300',
        'info' => 'bg-white dark:bg-gray-800 text-blue-700 dark:text-blue-300',
    ];
    
    $borderClasses = [
        'primary' => 'border-steel-blue-200 dark:border-steel-blue-900',
        'success' => 'border-emerald-200 dark:border-emerald-900',
        'warning' => 'border-amber-200 dark:border-amber-900',
        'danger' => 'border-red-200 dark:border-red-900',
        'info' => 'border-blue-200 dark:border-blue-900',
    ];
    
    $variantClass = $variantClasses[$variant] ?? $variantClasses['primary'];
    $borderClass = $borderClasses[$variant] ?? $borderClasses['primary'];
@endphp

<div class="{{ $cardStyle ? 'rounded-lg shadow-sm border ' . $borderClass : '' }} {{ $variantClass }} overflow-hidden">
    <div class="p-5">
        @if($title)
            <div class="flex items-center mb-3">
                @if($icon)
                    <div class="mr-3 flex-shrink-0">
                        {!! $icon !!}
                    </div>
                @endif
                <h3 class="text-lg font-semibold">{{ $title }}</h3>
            </div>
        @endif
        
        @if($description)
            <p class="text-sm mb-4 opacity-90">{{ $description }}</p>
        @endif
        
        <div class="{{ ($title || $description) ? 'mt-4' : '' }}">
            {{ $slot }}
        </div>
    </div>
</div> 