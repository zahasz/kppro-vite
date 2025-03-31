<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentGateway extends Model
{
    use HasFactory;

    /**
     * Atrybuty, które można masowo przypisywać.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'class_name',
        'description',
        'is_active',
        'test_mode',
        'display_order',
        'logo',
        'config'
    ];

    /**
     * Atrybuty, które powinny być rzutowane na typy.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'test_mode' => 'boolean',
        'display_order' => 'integer',
        'config' => 'array'
    ];

    /**
     * Relacja do transakcji
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'gateway_code', 'code');
    }

    /**
     * Pobiera konfigurację dla określonego klucza
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getConfig($key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Ustawia wartość konfiguracji dla określonego klucza
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setConfig($key, $value)
    {
        $config = $this->config ?? [];
        $config[$key] = $value;
        $this->config = $config;
        
        return $this;
    }

    /**
     * Sprawdza, czy bramka jest aktywna
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Sprawdza, czy bramka jest w trybie testowym
     *
     * @return bool
     */
    public function isTestMode()
    {
        return $this->test_mode;
    }

    /**
     * Pobiera instancję klasy bramki płatności
     *
     * @return mixed
     * @throws \Exception
     */
    public function getGatewayInstance()
    {
        if (!class_exists($this->class_name)) {
            throw new \Exception("Klasa bramki płatności {$this->class_name} nie istnieje.");
        }
        
        return new $this->class_name($this);
    }
} 