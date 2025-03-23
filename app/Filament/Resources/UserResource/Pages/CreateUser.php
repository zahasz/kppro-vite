<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Models\CompanyProfile;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function afterCreate(): void
    {
        $user = $this->record;
        
        // Tworzenie profilu firmowego dla nowego użytkownika
        if (!$user->companyProfile) {
            CompanyProfile::create([
                'user_id' => $user->id,
                'company_name' => 'Firma ' . $user->name,
                'tax_number' => '0000000000',
                'street' => 'Ulica',
                'city' => 'Miasto',
                'postal_code' => '00-000',
                'country' => 'Polska',
                'phone' => '000000000',
                'email' => $user->email,
                'bank_name' => 'Bank',
                'bank_account' => '00000000000000000000000000',
            ]);
        }
        
        // Wysyłanie powiadomienia
        Notification::make()
            ->title('Użytkownik został utworzony')
            ->success()
            ->persistent()
            ->seconds(5)
            ->icon('heroicon-o-user-plus')
            ->body('Nowy użytkownik ' . $user->name . ' został pomyślnie utworzony.')
            ->send();
    }
} 