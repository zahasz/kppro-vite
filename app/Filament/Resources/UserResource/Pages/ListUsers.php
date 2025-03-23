<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Dodaj użytkownika'),
            Actions\Action::make('import')
                ->label('Importuj')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->url(fn () => UserResource::getUrl('import')),
            Actions\Action::make('export')
                ->label('Eksportuj')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->url(fn () => UserResource::getUrl('export')),
        ];
    }
    
    protected function getTableActions(): array
    {
        return [
            Action::make('login-history')
                ->label('Historia logowań')
                ->icon('heroicon-o-clock')
                ->color('info')
                ->url(fn ($record) => UserResource::getUrl('login-history', ['record' => $record])),
            Action::make('edit')
                ->label('Edytuj')
                ->icon('heroicon-o-pencil')
                ->url(fn ($record) => UserResource::getUrl('edit', ['record' => $record])),
            Action::make('activate')
                ->label('Aktywuj')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn ($record) => !$record->is_active)
                ->action(function ($record) {
                    $record->update(['is_active' => true]);
                    \Filament\Notifications\Notification::make()
                        ->title('Użytkownik został aktywowany')
                        ->success()
                        ->send();
                }),
            Action::make('deactivate')
                ->label('Deaktywuj')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn ($record) => $record->is_active)
                ->action(function ($record) {
                    $record->update(['is_active' => false]);
                    \Filament\Notifications\Notification::make()
                        ->title('Użytkownik został deaktywowany')
                        ->success()
                        ->send();
                }),
        ];
    }
} 