<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Usuń')
                ->modalHeading('Usuń użytkownika')
                ->modalDescription('Czy na pewno chcesz usunąć tego użytkownika? Ta operacja jest nieodwracalna.')
                ->modalSubmitActionLabel('Tak, usuń')
                ->successNotificationTitle('Użytkownik został usunięty'),
            Actions\ForceDeleteAction::make()
                ->label('Usuń trwale')
                ->modalHeading('Trwale usuń użytkownika')
                ->modalDescription('Czy na pewno chcesz trwale usunąć tego użytkownika? Ta operacja jest nieodwracalna.')
                ->modalSubmitActionLabel('Tak, usuń trwale')
                ->successNotificationTitle('Użytkownik został trwale usunięty'),
            Actions\RestoreAction::make()
                ->label('Przywróć')
                ->successNotificationTitle('Użytkownik został przywrócony'),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function afterSave(): void
    {
        $user = $this->record;
        
        Notification::make()
            ->title('Użytkownik został zaktualizowany')
            ->success()
            ->persistent()
            ->seconds(5)
            ->icon('heroicon-o-user')
            ->body('Dane użytkownika ' . $user->name . ' zostały pomyślnie zaktualizowane.')
            ->send();
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Jeśli hasło jest puste, usuń je z danych, aby nie zostało zaktualizowane
        if (empty($data['password'])) {
            unset($data['password']);
        }
        
        return $data;
    }
} 