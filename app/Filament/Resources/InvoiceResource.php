<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Faktury';
    protected static ?string $modelLabel = 'Faktura';
    protected static ?string $pluralModelLabel = 'Faktury';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dane faktury')
                    ->schema([
                        Forms\Components\TextInput::make('number')
                            ->label('Numer faktury')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\DatePicker::make('issue_date')
                            ->label('Data wystawienia')
                            ->required(),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Data płatności')
                            ->required(),
                        Forms\Components\Select::make('contractor_id')
                            ->label('Kontrahent')
                            ->relationship('contractor', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('payment_method')
                            ->label('Metoda płatności')
                            ->options([
                                'transfer' => 'Przelew',
                                'cash' => 'Gotówka',
                                'card' => 'Karta',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('payment_days')
                            ->label('Dni na płatność')
                            ->numeric()
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Uwagi')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pozycje faktury')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->label('Pozycje')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nazwa')
                                    ->required(),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Ilość')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('unit')
                                    ->label('J.m.')
                                    ->required(),
                                Forms\Components\TextInput::make('net_price')
                                    ->label('Cena netto')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('vat_rate')
                                    ->label('VAT %')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->columns(5)
                            ->defaultItems(1)
                            ->addActionLabel('Dodaj pozycję')
                            ->reorderable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Numer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contractor.name')
                    ->label('Kontrahent')
                    ->searchable(),
                Tables\Columns\TextColumn::make('issue_date')
                    ->label('Data wystawienia')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Data płatności')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_net')
                    ->label('Wartość netto')
                    ->money('PLN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_vat')
                    ->label('VAT')
                    ->money('PLN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_gross')
                    ->label('Wartość brutto')
                    ->money('PLN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'warning',
                        'paid' => 'success',
                        'overdue' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Szkic',
                        'sent' => 'Wysłana',
                        'paid' => 'Opłacona',
                        'overdue' => 'Zaległa',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
