<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\Page;
use App\Models\User;

class LoginHistory extends Page
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.user-resource.pages.login-history';
    
    public ?User $user = null;
    
    public function mount(User $record): void
    {
        $this->user = $record;
    }
} 