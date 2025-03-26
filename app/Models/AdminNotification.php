<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    use HasFactory;

    /**
     * Atrybuty, które można masowo przypisywać.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'title',
        'message',
        'link',
        'read',
        'admin_id',
        'data',
    ];

    /**
     * Atrybuty, które powinny być rzutowane na natywne typy.
     *
     * @var array
     */
    protected $casts = [
        'read' => 'boolean',
        'data' => 'array',
    ];

    /**
     * Tworzy nowe powiadomienie dotyczące faktury
     *
     * @param string $title
     * @param string $message
     * @param string|null $link
     * @param array $data
     * @return self
     */
    public static function createInvoiceNotification($title, $message, $link = null, $data = [])
    {
        return self::create([
            'type' => 'invoice_generated',
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'read' => false,
            'data' => $data,
        ]);
    }

    /**
     * Tworzy nowe powiadomienie dotyczące odnowienia subskrypcji
     *
     * @param string $title
     * @param string $message
     * @param string|null $link
     * @param array $data
     * @return self
     */
    public static function createRenewalNotification($title, $message, $link = null, $data = [])
    {
        return self::create([
            'type' => 'renewal_invoice_generated',
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'read' => false,
            'data' => $data,
        ]);
    }

    /**
     * Tworzy nowe powiadomienie dotyczące wygasającej subskrypcji
     *
     * @param string $title
     * @param string $message
     * @param string|null $link
     * @param array $data
     * @return self
     */
    public static function createExpiringSubscriptionNotification($title, $message, $link = null, $data = [])
    {
        return self::create([
            'type' => 'subscription_expiring',
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'read' => false,
            'data' => $data,
        ]);
    }

    /**
     * Tworzy nowe powiadomienie dotyczące wygenerowanego raportu
     *
     * @param string $title
     * @param string $message
     * @param string|null $link
     * @param array $data
     * @return self
     */
    public static function createReportNotification($title, $message, $link = null, $data = [])
    {
        return self::create([
            'type' => 'weekly_report_generated',
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'read' => false,
            'data' => $data,
        ]);
    }

    /**
     * Relacja do użytkownika (administratora)
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
} 