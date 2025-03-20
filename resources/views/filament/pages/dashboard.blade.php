<x-filament-panels::page>
    <x-filament-widgets::widgets
        :columns="[
            'default' => 1,
            'sm' => 2,
            'md' => 3,
            'lg' => 4,
        ]"
        :widgets="$this->getWidgets()"
        :data="$this->getWidgetData()"
    />
</x-filament-panels::page> 