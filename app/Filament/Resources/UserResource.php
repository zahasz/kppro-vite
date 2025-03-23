<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Validation\Rules\Password;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Użytkownicy';
    protected static ?string $modelLabel = 'Użytkownik';
    protected static ?string $pluralModelLabel = 'Użytkownicy';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informacje podstawowe')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->label('Zdjęcie profilowe')
                            ->image()
                            ->imagePreviewHeight('100')
                            ->directory('avatars')
                            ->visibility('public')
                            ->disk('public')
                            ->maxSize(1024)
                            ->helperText('Maksymalny rozmiar: 1MB. Dozwolone formaty: jpg, jpeg, png, gif.')
                            ->circleCropper(),
                        TextInput::make('name')
                            ->label('Nazwa')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('first_name')
                            ->label('Imię')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->label('Nazwisko')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('phone')
                            ->label('Telefon')
                            ->tel()
                            ->maxLength(20),
                        TextInput::make('position')
                            ->label('Stanowisko')
                            ->maxLength(100),
                    ])->columns(2),
                
                Forms\Components\Section::make('Uwierzytelnianie')
                    ->schema([
                        TextInput::make('password')
                            ->label('Hasło')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->rule(Password::default())
                            ->autocomplete('new-password')
                            ->confirmed(),
                        TextInput::make('password_confirmation')
                            ->label('Potwierdź hasło')
                            ->password()
                            ->autocomplete('new-password')
                            ->dehydrated(false)
                            ->required(fn (string $context): bool => $context === 'create'),
                        Toggle::make('is_active')
                            ->label('Aktywny')
                            ->default(true)
                            ->helperText('Dezaktywacja konta uniemożliwi użytkownikowi logowanie się do systemu'),
                        Toggle::make('two_factor_enabled')
                            ->label('Dwuskładnikowe uwierzytelnianie')
                            ->default(false)
                            ->helperText('Zwiększa bezpieczeństwo konta użytkownika'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Role i uprawnienia')
                    ->schema([
                        Select::make('roles')
                            ->label('Role')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable()
                            ->helperText('Wybierz role, które określają uprawnienia użytkownika w systemie'),
                    ]),
                
                Forms\Components\Section::make('Preferencje')
                    ->schema([
                        Select::make('language')
                            ->label('Język')
                            ->options([
                                'pl' => 'Polski',
                                'en' => 'Angielski',
                                'de' => 'Niemiecki',
                            ])
                            ->default('pl'),
                        Select::make('timezone')
                            ->label('Strefa czasowa')
                            ->options([
                                'Europe/Warsaw' => 'Europa/Warszawa (UTC+1/+2)',
                                'Europe/London' => 'Europa/Londyn (UTC+0/+1)',
                                'UTC' => 'UTC',
                                'America/New_York' => 'Ameryka/Nowy Jork (UTC-5/-4)',
                            ])
                            ->default('Europe/Warsaw'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nazwa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color('primary')
                    ->searchable(),
                TextColumn::make('position')
                    ->label('Stanowisko')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Aktywny')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('last_login_at')
                    ->label('Ostatnie logowanie')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Data utworzenia')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('role')
                    ->label('Rola')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Wszystkie')
                    ->trueLabel('Tylko aktywni')
                    ->falseLabel('Tylko nieaktywni'),
                Tables\Filters\Filter::make('last_login_at')
                    ->label('Ostatnie logowanie')
                    ->form([
                        Forms\Components\DatePicker::make('login_from')
                            ->label('Od'),
                        Forms\Components\DatePicker::make('login_until')
                            ->label('Do'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['login_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('last_login_at', '>=', $date),
                            )
                            ->when(
                                $data['login_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('last_login_at', '<=', $date),
                            );
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\BulkAction::make('activateSelected')
                        ->label('Aktywuj zaznaczonych')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = $records->count();
                            $records->each(function ($record) {
                                $record->update(['is_active' => true]);
                            });
                            Notification::make()
                                ->title("Aktywowano $count użytkowników")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('deactivateSelected')
                        ->label('Deaktywuj zaznaczonych')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = $records->count();
                            $records->each(function ($record) {
                                $record->update(['is_active' => false]);
                            });
                            Notification::make()
                                ->title("Deaktywowano $count użytkowników")
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            RelationManagers\RolesRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'import' => Pages\ImportUsers::route('/import'),
            'export' => Pages\ExportUsers::route('/export'),
            'login-history' => Pages\LoginHistory::route('/{record}/login-history'),
        ];
    }    
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
    
    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    
    protected static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'first_name', 'last_name', 'email', 'position'];
    }
    
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Email' => $record->email,
            'Role' => $record->roles->pluck('name')->join(', '),
        ];
    }
} 