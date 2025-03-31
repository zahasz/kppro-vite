<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        
        // Zdarzenia związane z subskrypcjami
        \App\Events\SubscriptionCancelled::class => [
            \App\Listeners\LogSubscriptionCancellation::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Rejestrowanie udanych logowań
        Event::listen(Login::class, function (Login $event) {
            \App\Models\LoginHistory::addEntry(
                $event->user->id,
                'success'
            );
            
            // Aktualizacja czasu ostatniego logowania
            $event->user->recordSuccessfulLogin();
        });

        // Rejestrowanie nieudanych logowań
        Event::listen(Failed::class, function (Failed $event) {
            if (isset($event->user) && $event->user) {
                \App\Models\LoginHistory::addEntry(
                    $event->user->id,
                    'failed',
                    'Niepoprawne hasło'
                );
                
                // Inkrementacja liczby nieudanych logowań
                $event->user->incrementFailedLoginAttempts();
            }
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
} 