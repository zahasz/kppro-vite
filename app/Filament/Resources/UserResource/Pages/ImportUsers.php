<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use League\Csv\Reader;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ImportUsers extends Page
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.user-resource.pages.import-users';
    
    public ?array $data = [];
    
    public function mount(): void
    {
        $this->form->fill();
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Import użytkowników')
                    ->description('Zaimportuj użytkowników z pliku CSV. Plik powinien zawierać nagłówki: name, first_name, last_name, email, password, position, phone.')
                    ->schema([
                        Forms\Components\FileUpload::make('csv_file')
                            ->label('Plik CSV')
                            ->required()
                            ->acceptedFileTypes(['text/csv', 'application/csv']),
                        Forms\Components\Toggle::make('has_header_row')
                            ->label('Plik zawiera wiersz nagłówkowy')
                            ->default(true),
                        Forms\Components\Toggle::make('send_invitation_emails')
                            ->label('Wyślij e-maile z zaproszeniem')
                            ->default(false)
                            ->hint('Opcja wysyłania e-maili do nowych użytkowników'),
                    ]),
            ])
            ->statePath('data');
    }
    
    public function importUsers()
    {
        $this->validate();
        
        try {
            $csvPath = storage_path('app/public/' . $this->data['csv_file']);
            $csv = Reader::createFromPath($csvPath, 'r');
            $csv->setDelimiter(',');
            
            if ($this->data['has_header_row']) {
                $csv->setHeaderOffset(0);
            }
            
            $records = $csv->getRecords();
            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            
            foreach ($records as $offset => $record) {
                $validator = Validator::make($record, [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email',
                    'password' => ['required', Password::defaults()],
                ]);
                
                if ($validator->fails()) {
                    $errorCount++;
                    $errors[] = "Wiersz " . ($offset + 1) . ": " . implode(', ', $validator->errors()->all());
                    continue;
                }
                
                try {
                    User::create([
                        'name' => $record['name'] ?? null,
                        'first_name' => $record['first_name'] ?? null,
                        'last_name' => $record['last_name'] ?? null,
                        'email' => $record['email'],
                        'password' => Hash::make($record['password']),
                        'position' => $record['position'] ?? null,
                        'phone' => $record['phone'] ?? null,
                        'is_active' => true,
                    ]);
                    
                    // Tutaj można dodać kod do wysyłania e-maili z zaproszeniem
                    // if ($this->data['send_invitation_emails']) { ... }
                    
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Wiersz " . ($offset + 1) . ": " . $e->getMessage();
                }
            }
            
            if ($successCount > 0) {
                Notification::make()
                    ->title("Zaimportowano $successCount użytkowników")
                    ->success()
                    ->send();
            }
            
            if ($errorCount > 0) {
                $this->addError('importErrors', "Wystąpiło $errorCount błędów podczas importu.");
                foreach ($errors as $error) {
                    $this->addError('importErrors', $error);
                }
            }
            
            return redirect()->to(UserResource::getUrl('index'));
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Błąd podczas importu: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    public function cancelImport()
    {
        return redirect()->to(UserResource::getUrl('index'));
    }
} 