<?php

namespace App\Events;

use App\Models\UserSubscription;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionCancelled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Anulowana subskrypcja.
     *
     * @var UserSubscription
     */
    public $subscription;

    /**
     * Powód anulowania (opcjonalny).
     *
     * @var string|null
     */
    public $reason;

    /**
     * Czy anulowano automatycznie (np. po nieudanych płatnościach).
     *
     * @var bool
     */
    public $autoCancel;

    /**
     * Utwórz nową instancję zdarzenia.
     *
     * @param UserSubscription $subscription
     * @param string|null $reason
     * @param bool $autoCancel
     * @return void
     */
    public function __construct(UserSubscription $subscription, ?string $reason = null, bool $autoCancel = false)
    {
        $this->subscription = $subscription;
        $this->reason = $reason;
        $this->autoCancel = $autoCancel;
    }

    /**
     * Pobierz kanały, na których zdarzenie powinno być nadawane.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('subscription.' . $this->subscription->id),
        ];
    }
}
