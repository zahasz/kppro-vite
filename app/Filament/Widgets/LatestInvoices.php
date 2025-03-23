<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestInvoices extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Ostatnie faktury';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Invoice::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Numer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('issue_date')
                    ->label('Data wystawienia')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('contractor.name')
                    ->label('Kontrahent')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_net')
                    ->label('Netto')
                    ->money('PLN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_gross')
                    ->label('Brutto')
                    ->money('PLN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Termin płatności')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'overdue' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Podgląd')
                    ->url(fn (Invoice $record): string => route('admin.resources.invoices.edit', $record))
                    ->icon('heroicon-o-eye'),
            ]);
    }
} 