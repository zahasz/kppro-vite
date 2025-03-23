<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserActivityLog extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Aktywność użytkowników';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->whereNotNull('last_login_at')
                    ->orderBy('last_login_at', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nazwa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Ostatnie logowanie')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_login_ip')
                    ->label('IP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('login_count')
                    ->label('Liczba logowań')
                    ->getStateUsing(function ($record) {
                        // W rzeczywistym projekcie byłaby tutaj prawdziwa logika liczenia logowań
                        return rand(1, 100);
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        return $record->is_active ? 'Aktywny' : 'Nieaktywny';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Aktywny' => 'success',
                        'Nieaktywny' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Podgląd')
                    ->url(fn (User $record): string => route('filament.admin.resources.users.edit', $record))
                    ->icon('heroicon-o-eye'),
            ]);
    }
} 