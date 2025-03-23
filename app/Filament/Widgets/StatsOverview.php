<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Contractor;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';
    
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $users = User::count();
        $roles = Role::count();
        $permissions = Permission::count();
        $contractors = Contractor::count();
        $invoices = Invoice::count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();
        $recentlyLoggedUsers = User::whereNotNull('last_login_at')
            ->where('last_login_at', '>=', now()->subDays(7))
            ->count();

        return [
            Card::make('Użytkownicy', $users)
                ->description('Wszyscy użytkownicy')
                ->descriptionIcon('heroicon-m-user')
                ->chart([7, 2, 10, 3, 15, 4, $users])
                ->color('primary'),
            Card::make('Role', $roles)
                ->description('Zdefiniowane role')
                ->descriptionIcon('heroicon-m-shield-check')
                ->chart([2, 3, 5, 4, 6, 5, $roles])
                ->color('success'),
            Card::make('Uprawnienia', $permissions)
                ->description('Zdefiniowane uprawnienia')
                ->descriptionIcon('heroicon-m-key')
                ->chart([10, 15, 20, 18, 25, 22, $permissions])
                ->color('warning'),
            Card::make('Kontrahenci', $contractors)
                ->description('Wszyscy kontrahenci')
                ->descriptionIcon('heroicon-m-building-office')
                ->chart([5, 10, 15, 20, 25, 30, $contractors])
                ->color('danger'),
            Card::make('Faktury', $invoices)
                ->description('Wszystkie faktury')
                ->descriptionIcon('heroicon-m-document-text')
                ->chart([0, 3, 6, 9, 12, 15, $invoices])
                ->color('gray'),
            Card::make('Aktywni użytkownicy', $activeUsers)
                ->description($activeUsers . ' z ' . $users . ' użytkowników')
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([$activeUsers, $inactiveUsers])
                ->color('success'),
            Card::make('Ostatnio aktywni', $recentlyLoggedUsers)
                ->description('Aktywni w ciągu 7 dni')
                ->descriptionIcon('heroicon-m-clock')
                ->chart([0, 0, 0, 2, 4, 6, $recentlyLoggedUsers])
                ->color('primary'),
        ];
    }
} 