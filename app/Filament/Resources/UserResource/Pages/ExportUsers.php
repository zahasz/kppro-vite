<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Csv\Writer;

class ExportUsers extends Page
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.user-resource.pages.export-users';
    
    public ?array $data = [];
    
    public function mount(): void
    {
        $this->form->fill();
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Eksport użytkowników')
                    ->description('Wyeksportuj dane użytkowników do wybranego formatu pliku.')
                    ->schema([
                        Forms\Components\Select::make('export_format')
                            ->label('Format eksportu')
                            ->options([
                                'csv' => 'CSV',
                                'xlsx' => 'Excel (XLSX)',
                                'pdf' => 'PDF',
                            ])
                            ->default('csv')
                            ->required(),
                        Forms\Components\MultiSelect::make('user_statuses')
                            ->label('Status użytkowników')
                            ->options([
                                'active' => 'Aktywni',
                                'inactive' => 'Nieaktywni',
                                'deleted' => 'Usunięci',
                            ])
                            ->default(['active'])
                            ->required(),
                        Forms\Components\MultiSelect::make('columns')
                            ->label('Kolumny do eksportu')
                            ->options([
                                'id' => 'ID',
                                'name' => 'Nazwa',
                                'first_name' => 'Imię',
                                'last_name' => 'Nazwisko',
                                'email' => 'Email',
                                'position' => 'Stanowisko',
                                'phone' => 'Telefon',
                                'is_active' => 'Status',
                                'roles' => 'Role',
                                'last_login_at' => 'Ostatnie logowanie',
                                'created_at' => 'Data utworzenia',
                            ])
                            ->default(['name', 'first_name', 'last_name', 'email', 'position', 'roles'])
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }
    
    public function exportUsers()
    {
        $this->validate();
        
        try {
            // Przygotowanie zapytania
            $query = User::query();
            
            // Filtrowanie po statusie
            if (in_array('active', $this->data['user_statuses']) && !in_array('inactive', $this->data['user_statuses'])) {
                $query->where('is_active', true);
            } elseif (in_array('inactive', $this->data['user_statuses']) && !in_array('active', $this->data['user_statuses'])) {
                $query->where('is_active', false);
            }
            
            // Obsługa usuniętych użytkowników
            if (in_array('deleted', $this->data['user_statuses'])) {
                $query->withTrashed();
                
                if (!in_array('active', $this->data['user_statuses']) && !in_array('inactive', $this->data['user_statuses'])) {
                    $query->whereNotNull('deleted_at');
                }
            }
            
            // Pobranie użytkowników
            $users = $query->with('roles')->get();
            
            if ($users->isEmpty()) {
                Notification::make()
                    ->title('Brak użytkowników do eksportu')
                    ->warning()
                    ->send();
                
                return;
            }
            
            // Obsługa formatu CSV
            if ($this->data['export_format'] === 'csv') {
                $csv = Writer::createFromString('');
                
                // Dodanie nagłówków
                $headers = [];
                foreach ($this->data['columns'] as $column) {
                    $headers[] = match($column) {
                        'id' => 'ID',
                        'name' => 'Nazwa',
                        'first_name' => 'Imię',
                        'last_name' => 'Nazwisko',
                        'email' => 'Email',
                        'position' => 'Stanowisko',
                        'phone' => 'Telefon',
                        'is_active' => 'Status',
                        'roles' => 'Role',
                        'last_login_at' => 'Ostatnie logowanie',
                        'created_at' => 'Data utworzenia',
                        default => $column,
                    };
                }
                $csv->insertOne($headers);
                
                // Dodanie danych
                foreach ($users as $user) {
                    $row = [];
                    foreach ($this->data['columns'] as $column) {
                        $row[] = match($column) {
                            'id' => $user->id,
                            'name' => $user->name,
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                            'email' => $user->email,
                            'position' => $user->position,
                            'phone' => $user->phone,
                            'is_active' => $user->is_active ? 'Aktywny' : 'Nieaktywny',
                            'roles' => $user->roles->pluck('name')->join(', '),
                            'last_login_at' => $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : '',
                            'created_at' => $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '',
                            default => '',
                        };
                    }
                    $csv->insertOne($row);
                }
                
                $filename = 'users_export_' . date('Y-m-d_His') . '.csv';
                Storage::disk('public')->put($filename, $csv->toString());
                
                return response()->download(
                    storage_path("app/public/{$filename}"), 
                    $filename, 
                    ['Content-Type' => 'text/csv']
                )->deleteFileAfterSend();
            }
            
            // Dla formatów XLSX i PDF należałoby użyć bibliotek jak PhpSpreadsheet lub DomPDF
            // Poniżej jest symulacja tego procesu
            
            if ($this->data['export_format'] === 'xlsx' || $this->data['export_format'] === 'pdf') {
                $format = strtoupper($this->data['export_format']);
                Notification::make()
                    ->title("Eksport do formatu $format jest dostępny tylko w pełnej wersji aplikacji")
                    ->warning()
                    ->send();
                    
                return redirect()->to(UserResource::getUrl('index'));
            }
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Błąd podczas eksportu: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    public function cancelExport()
    {
        return redirect()->to(UserResource::getUrl('index'));
    }
} 