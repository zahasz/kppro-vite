<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Logs extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bug-ant';
    protected static ?string $navigationLabel = 'Logi systemowe';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 101;
    protected static ?string $title = 'Logi systemowe';
    
    protected static string $view = 'filament.pages.logs';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                $this->getLogsQuery()
            )
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Data')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('level')
                    ->label('Poziom')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ERROR' => 'danger',
                        'WARNING' => 'warning',
                        'INFO' => 'info',
                        'DEBUG' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('message')
                    ->label('Wiadomość')
                    ->wrap()
                    ->limit(100)
                    ->searchable(),
                Tables\Columns\TextColumn::make('file')
                    ->label('Plik')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('level')
                    ->label('Poziom')
                    ->options([
                        'ERROR' => 'ERROR',
                        'WARNING' => 'WARNING',
                        'INFO' => 'INFO',
                        'DEBUG' => 'DEBUG',
                    ]),
            ]);
    }
    
    protected function getLogsQuery()
    {
        // To jest symulacja logów - w rzeczywistej aplikacji pobieralibyśmy prawdziwe dane z pliku logów
        $logs = collect([
            [
                'id' => 1,
                'date' => now()->subHours(1)->format('Y-m-d H:i:s'),
                'level' => 'INFO',
                'message' => 'Użytkownik administrator zalogował się do systemu',
                'file' => 'app/Http/Controllers/Auth/LoginController.php:45',
            ],
            [
                'id' => 2,
                'date' => now()->subHours(2)->format('Y-m-d H:i:s'),
                'level' => 'WARNING',
                'message' => 'Próba nieudanego logowania dla konta admin@kppro.pl',
                'file' => 'app/Http/Controllers/Auth/LoginController.php:67',
            ],
            [
                'id' => 3,
                'date' => now()->subHours(3)->format('Y-m-d H:i:s'),
                'level' => 'ERROR',
                'message' => 'Błąd podczas próby połączenia z bazą danych: SQLSTATE[HY000] [2002] Connection refused',
                'file' => 'app/Exceptions/Handler.php:47',
            ],
            [
                'id' => 4,
                'date' => now()->subHours(4)->format('Y-m-d H:i:s'),
                'level' => 'INFO',
                'message' => 'Wykonano kopię zapasową bazy danych',
                'file' => 'app/Console/Commands/BackupDatabase.php:24',
            ],
            [
                'id' => 5,
                'date' => now()->subHours(5)->format('Y-m-d H:i:s'),
                'level' => 'DEBUG',
                'message' => 'Debugowanie zapytania SQL: SELECT * FROM users WHERE email = ? LIMIT 1',
                'file' => 'app/Models/User.php:156',
            ],
        ]);
        
        return new \Illuminate\Database\Eloquent\Collection($logs);
    }
    
    public function clearLogs()
    {
        // W rzeczywistym projekcie wyczyścilibyśmy prawdziwe pliki logów
        
        Notification::make()
            ->title('Logi zostały wyczyszczone')
            ->success()
            ->send();
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Forms\Actions\Action::make('clearLogs')
                ->label('Wyczyść logi')
                ->action('clearLogs')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation(),
        ];
    }
} 