<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class UserProfile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Mój profil';
    protected static ?string $navigationGroup = null;
    protected static ?int $navigationSort = 99;
    protected static ?string $slug = 'moj-profil';
    protected static ?string $title = 'Mój profil';
    
    protected static string $view = 'filament.pages.user-profile';
    
    public ?array $userData = [];
    public ?array $passwordData = [];
    public ?array $preferencesData = [];
    public $avatarData = [];
    
    public function mount(): void
    {
        $user = auth()->user();
        $this->userData = [
            'name' => $user->name,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'position' => $user->position,
            'language' => $user->language,
            'timezone' => $user->timezone,
            'avatar' => $user->avatar,
        ];
        
        $this->preferencesData = [
            'language' => $user->language ?? 'pl',
            'timezone' => $user->timezone ?? 'Europe/Warsaw',
        ];
        
        $this->passwordData = [
            'current_password' => '',
            'password' => '',
            'password_confirmation' => '',
        ];
        
        $this->avatarData = [
            'avatar' => null,
        ];
        
        $this->userProfileForm = $this->makeUserProfileForm();
        $this->passwordForm = $this->makePasswordForm();
        $this->preferencesForm = $this->makePreferencesForm();
        $this->avatarForm = $this->makeAvatarForm();
    }
    
    public function userProfileForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dane osobowe')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('userData.first_name')
                                    ->label('Imię')
                                    ->required(),
                                TextInput::make('userData.last_name')
                                    ->label('Nazwisko')
                                    ->required(),
                            ]),

                        TextInput::make('userData.name')
                            ->label('Nazwa użytkownika')
                            ->required(),

                        TextInput::make('userData.email')
                            ->label('Email')
                            ->email()
                            ->required(),

                        TextInput::make('userData.position')
                            ->label('Stanowisko'),
                    ]),

                Section::make('Preferencje')
                    ->schema([
                        Grid::make()
                            ->schema([
                                Select::make('userData.language')
                                    ->label('Język')
                                    ->options([
                                        'pl' => 'Polski',
                                        'en' => 'Angielski',
                                        'de' => 'Niemiecki',
                                    ])
                                    ->default('pl'),

                                Select::make('userData.timezone')
                                    ->label('Strefa czasowa')
                                    ->options([
                                        'Europe/Warsaw' => 'Europa/Warszawa (UTC+1/+2)',
                                        'Europe/London' => 'Europa/Londyn (UTC+0/+1)',
                                        'America/New_York' => 'Ameryka/Nowy Jork (UTC-5/-4)',
                                    ])
                                    ->default('Europe/Warsaw'),
                            ]),

                        FileUpload::make('userData.avatar')
                            ->label('Awatar')
                            ->image()
                            ->directory('avatars')
                            ->preserveFilenames()
                            ->maxSize(1024)
                            ->imagePreviewHeight('100')
                            ->circleCropper(),
                    ]),
            ])
            ->statePath('userData');
    }
    
    public function passwordForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Zmiana hasła')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Aktualne hasło')
                            ->password()
                            ->required()
                            ->rule('current_password'),

                        TextInput::make('new_password')
                            ->label('Nowe hasło')
                            ->password()
                            ->required()
                            ->rule(Password::defaults())
                            ->same('new_password_confirmation'),

                        TextInput::make('new_password_confirmation')
                            ->label('Potwierdź nowe hasło')
                            ->password()
                            ->required(),
                    ]),
            ])
            ->statePath('passwordData');
    }
    
    public function preferencesForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Preferencje')
                    ->schema([
                        Forms\Components\Select::make('language')
                            ->label('Język')
                            ->options([
                                'pl' => 'Polski',
                                'en' => 'Angielski',
                            ])
                            ->default('pl'),
                        Forms\Components\Select::make('timezone')
                            ->label('Strefa czasowa')
                            ->options([
                                'Europe/Warsaw' => 'Europa/Warszawa',
                                'UTC' => 'UTC',
                                'Europe/London' => 'Europa/Londyn',
                                'America/New_York' => 'Ameryka/Nowy Jork',
                            ])
                            ->default('Europe/Warsaw'),
                    ])->columns(2),
            ])
            ->statePath('preferencesData');
    }
    
    public function avatarForm(Form $form): Form
    {
        $user = Auth::user();
        
        return $form
            ->schema([
                Forms\Components\Section::make('Zdjęcie profilowe')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->label('Awatar')
                            ->image()
                            ->imagePreviewHeight('150')
                            ->directory('avatars')
                            ->visibility('public')
                            ->disk('public')
                            ->maxSize(1024)
                            ->helperText('Maksymalny rozmiar: 1MB. Dozwolone formaty: jpg, jpeg, png, gif.')
                            ->placeholder('Przenieś lub kliknij, aby dodać zdjęcie')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('200')
                            ->imageResizeTargetHeight('200')
                            ->imageEditor()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, ?TemporaryUploadedFile $state): void {
                                if (!$state) return;
                                
                                $set('has_custom_avatar', true);
                            }),
                        Forms\Components\Hidden::make('has_custom_avatar')
                            ->default(false),
                    ]),
            ])
            ->statePath('avatarData');
    }
    
    public function makeUserProfileForm(): Form
    {
        return $this->makeForm(
            'userProfileForm',
            'userData'
        );
    }
    
    public function makePasswordForm(): Form
    {
        return $this->makeForm(
            'passwordForm',
            'passwordData'
        );
    }
    
    public function makePreferencesForm(): Form
    {
        return $this->makeForm(
            'preferencesForm',
            'preferencesData'
        );
    }
    
    public function makeAvatarForm(): Form
    {
        return $this->makeForm(
            'avatarForm',
            'avatarData'
        );
    }
    
    protected function makeForm(string $name, string $statePath): Form
    {
        return $this->{$name}(Form::make($this))
            ->statePath($statePath);
    }
    
    public function updateProfile()
    {
        $data = $this->userProfileForm->getState();
        
        $user = auth()->user();
        
        $user->update($data);
        
        // Jeśli użytkownik nie ma jeszcze profilu firmy, tworzymy go z minimalnymi wymaganymi danymi
        if (!$user->companyProfile) {
            $user->companyProfile()->create([
                'company_name' => 'Moja Firma',
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
        
        Notification::make()
            ->title('Profil został zaktualizowany')
            ->success()
            ->persistent()
            ->seconds(5)
            ->icon('heroicon-o-check-circle')
            ->send();
    }
    
    public function updatePassword()
    {
        $this->passwordForm->validate();
        
        $data = $this->passwordData;

        /** @var User $user */
        $user = auth()->user();

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        $this->passwordData = [];
        
        Notification::make()
            ->title('Hasło zostało zmienione')
            ->success()
            ->persistent()
            ->seconds(5)
            ->icon('heroicon-o-check-circle')
            ->send();
    }
    
    public function updatePreferences()
    {
        $data = $this->preferencesForm->getState();
        
        $user = Auth::user();
        
        $user->update($data);
        
        Notification::make()
            ->title('Preferencje zostały zaktualizowane')
            ->success()
            ->persistent()
            ->seconds(5)
            ->icon('heroicon-o-check-circle')
            ->send();
    }
    
    public function updateAvatar()
    {
        $data = $this->avatarForm->getState();
        
        $user = Auth::user();
        
        // Usuń stary avatar, jeśli istnieje i nie jest domyślny
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        $user->update([
            'avatar' => $data['avatar']
        ]);
        
        Notification::make()
            ->title('Zdjęcie profilowe zostało zaktualizowane')
            ->success()
            ->persistent()
            ->seconds(5)
            ->icon('heroicon-o-check-circle')
            ->send();
    }
} 