<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Ustawienia systemu';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 100;
    protected static ?string $title = 'Ustawienia systemu';
    protected static string $view = 'filament.pages.settings';

    public ?array $formData = [];
    
    public function mount(): void
    {
        $this->formData = [
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'mail_from_address' => config('mail.from.address'),
            'mail_from_name' => config('mail.from.name'),
            'pagination_limit' => config('app.pagination_limit', 15),
            'timezone' => config('app.timezone'),
        ];
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Podstawowe ustawienia')
                    ->schema([
                        Forms\Components\TextInput::make('app_name')
                            ->label('Nazwa aplikacji')
                            ->required(),
                        Forms\Components\TextInput::make('app_url')
                            ->label('URL aplikacji')
                            ->required()
                            ->url(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Ustawienia poczty')
                    ->schema([
                        Forms\Components\TextInput::make('mail_from_address')
                            ->label('Adres e-mail nadawcy')
                            ->required()
                            ->email(),
                        Forms\Components\TextInput::make('mail_from_name')
                            ->label('Nazwa nadawcy')
                            ->required(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Inne ustawienia')
                    ->schema([
                        Forms\Components\TextInput::make('pagination_limit')
                            ->label('Limit paginacji')
                            ->numeric()
                            ->required(),
                        Forms\Components\Select::make('timezone')
                            ->label('Strefa czasowa')
                            ->options([
                                'Europe/Warsaw' => 'Europa/Warszawa',
                                'UTC' => 'UTC',
                                'Europe/London' => 'Europa/Londyn',
                                'America/New_York' => 'Ameryka/Nowy Jork',
                            ])
                            ->required(),
                    ])->columns(2),
            ])
            ->statePath('formData');
    }
    
    public function save(): void
    {
        // W rzeczywistym projekcie należałoby zapisać ustawienia do bazy danych
        // lub do pliku konfiguracyjnego
        // Tutaj tylko symulujemy zapis
        
        Notification::make()
            ->title('Ustawienia zapisane')
            ->success()
            ->send();
            
        // W rzeczywistym projekcie wywołalibyśmy np:
        // Artisan::call('config:cache');
    }
    
    public function clearCache(): void
    {
        Artisan::call('optimize:clear');
        
        Notification::make()
            ->title('Pamięć podręczna wyczyszczona')
            ->success()
            ->send();
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Forms\Actions\Action::make('save')
                ->label('Zapisz ustawienia')
                ->action('save')
                ->color('primary'),
                
            Forms\Actions\Action::make('clearCache')
                ->label('Wyczyść pamięć podręczną')
                ->action('clearCache')
                ->color('warning')
                ->icon('heroicon-o-trash'),
        ];
    }
} 