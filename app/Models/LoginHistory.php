<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    use HasFactory;

    /**
     * Atrybuty, które można masowo przypisywać.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'status',
        'details',
    ];

    /**
     * Pobierz użytkownika powiązanego z historią logowania.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Przetwórz informacje o przeglądarce użytkownika z ciągu user_agent.
     *
     * @return string
     */
    public function getBrowserInfoAttribute()
    {
        $userAgent = $this->user_agent;
        
        if (empty($userAgent)) {
            return 'Nieznany';
        }
        
        // Podstawowe wykrywanie przeglądarek
        $browser = 'Nieznany';
        
        if (strpos($userAgent, 'Chrome') !== false) {
            $browser = 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            $browser = 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false || strpos($userAgent, 'Edg') !== false) {
            $browser = 'Edge';
        } elseif (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false) {
            $browser = 'Internet Explorer';
        } elseif (strpos($userAgent, 'Opera') !== false || strpos($userAgent, 'OPR') !== false) {
            $browser = 'Opera';
        }
        
        return $browser;
    }

    /**
     * Pomocnicza metoda do tworzenia wpisu historii logowania.
     *
     * @param int $userId
     * @param string $status
     * @param string|null $details
     * @return self
     */
    public static function addEntry($userId, $status = 'success', $details = null)
    {
        return self::create([
            'user_id' => $userId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => $status,
            'details' => $details,
        ]);
    }
} 