<x-filament-panels::page>
    <div class="mb-4">
        <p class="text-gray-600 dark:text-gray-400">
            Przeglądaj logi systemowe aplikacji. Możesz filtrować logi według poziomu (ERROR, WARNING, INFO, DEBUG).
        </p>
    </div>
    
    {{ $this->table }}
</x-filament-panels::page> 