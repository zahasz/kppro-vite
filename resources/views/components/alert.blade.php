@props([
    'type' => 'info',
    'dismissible' => false,
    'icon' => null,
    'title' => null,
    'size' => 'md',
])

@php
$typeClasses = [
    'success' => 'bg-emerald-50 dark:bg-emerald-900/30 border-emerald-400 text-emerald-700 dark:text-emerald-300',
    'info' => 'bg-blue-50 dark:bg-blue-900/30 border-blue-400 text-blue-700 dark:text-blue-300',
    'warning' => 'bg-amber-50 dark:bg-amber-900/30 border-amber-400 text-amber-700 dark:text-amber-300',
    'error' => 'bg-red-50 dark:bg-red-900/30 border-red-400 text-red-700 dark:text-red-300',
];

$icons = [
    'success' => '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
    'info' => '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>',
    'warning' => '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
    'error' => '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
];

$sizeClasses = [
    'sm' => 'p-2 text-sm',
    'md' => 'p-4',
    'lg' => 'p-5 text-lg',
];

$alertClasses = $typeClasses[$type] ?? $typeClasses['info'];
$alertIcon = $icon ?? $icons[$type] ?? $icons['info'];
$sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div 
    x-data="{ open: true }" 
    x-show="open" 
    x-transition:enter="transition ease-out duration-300" 
    x-transition:enter-start="opacity-0 transform -translate-y-2" 
    x-transition:enter-end="opacity-100 transform translate-y-0" 
    x-transition:leave="transition ease-in duration-200" 
    x-transition:leave-start="opacity-100 transform translate-y-0" 
    x-transition:leave-end="opacity-0 transform -translate-y-2" 
    class="border-l-4 {{ $sizeClass }} mb-4 rounded shadow-sm {{ $alertClasses }}" 
    role="alert"
>
    <div class="flex items-start">
        @if($alertIcon)
            <div class="flex-shrink-0 mr-3">
                {!! $alertIcon !!}
            </div>
        @endif
        
        <div class="flex-1">
            @if($title)
                <h3 class="font-semibold mb-1">{{ $title }}</h3>
            @endif
            {{ $slot }}
        </div>
        
        @if($dismissible)
        <div class="ml-auto pl-3">
            <div class="-mx-1.5 -my-1.5">
                <button 
                    @click="open = false" 
                    class="{{ $typeClasses[$type] ?? $typeClasses['info'] }} rounded-md p-1.5 inline-flex focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-{{ $type }}-50 focus:ring-{{ $type }}-600 dark:focus:ring-offset-{{ $type }}-900 dark:focus:ring-{{ $type }}-400"
                >
                    <span class="sr-only">Zamknij</span>
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
        @endif
    </div>
</div> 