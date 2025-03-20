<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\LatestInvoices;
use App\Filament\Widgets\LatestEstimates;
use App\Filament\Widgets\LatestTasks;
use App\Filament\Widgets\LatestContracts;
use App\Filament\Widgets\LatestWarehouseMaterials;
use App\Filament\Widgets\LatestWarehouseEquipment;
use App\Filament\Widgets\LatestWarehouseTools;
use App\Filament\Widgets\LatestWarehouseVehicles;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    public function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            LatestInvoices::class,
            LatestEstimates::class,
            LatestTasks::class,
            LatestContracts::class,
            LatestWarehouseMaterials::class,
            LatestWarehouseEquipment::class,
            LatestWarehouseTools::class,
            LatestWarehouseVehicles::class,
        ];
    }
} 