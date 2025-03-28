<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @property string $position
 * @property string $avatar
 * @property string $password
 * @property bool $is_active
 * @property string|null $language
 * @property string|null $timezone
 * @property array|null $preferences
 * @property bool $two_factor_enabled
 * @property string|null $two_factor_secret
 * @property \Carbon\Carbon|null $email_verified_at
 * @property \Carbon\Carbon|null $last_login_at
 * @property string|null $last_login_ip
 * @property int $failed_login_attempts
 * @property \Carbon\Carbon|null $locked_until
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property \Carbon\Carbon|null $last_active_at
 * @property \Carbon\Carbon|null $last_seen_at
 * 
 * @method static \Database\Factories\UserFactory factory()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method \Laravel\Sanctum\PersonalAccessToken[] tokens()
 * @method \Illuminate\Database\Eloquent\Collection roles()
 * @method \Illuminate\Database\Eloquent\Collection permissions()
 * @method bool hasRole($roles, $guard = null)
 * @method bool hasPermissionTo($permission, $guard = null)
 * 
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Eloquent\Model
 * @mixin \Laravel\Sanctum\HasApiTokens
 * @mixin \Illuminate\Database\Eloquent\Factories\HasFactory
 * @mixin \Illuminate\Notifications\Notifiable
 * @mixin \Illuminate\Database\Eloquent\SoftDeletes
 * @mixin \Spatie\Permission\Traits\HasRoles
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'position',
        'avatar',
        'company_id',
        'language',
        'timezone',
        'preferences',
        'is_active',
        'two_factor_enabled',
        'two_factor_secret',
        'last_active_at',
        'failed_login_attempts',
        'locked_until',
        'last_seen_at',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'preferences' => 'array',
        'is_active' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'last_login_at' => 'datetime',
        'last_active_at' => 'datetime',
        'locked_until' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function getUserRolesAttribute()
    {
        return $this->getRoleNames();
    }

    public function getUserPermissionsAttribute()
    {
        return $this->getAllPermissions()->pluck('name');
    }

    public function hasAdminAccess(): bool
    {
        return $this->hasRole('admin') || $this->hasPermissionTo('access admin panel');
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Pobiera profil firmy użytkownika
     *
     * @return HasOne
     */
    public function companyProfile()
    {
        return $this->hasOne(CompanyProfile::class);
    }

    public function incrementFailedLoginAttempts()
    {
        $this->failed_login_attempts++;
        if ($this->failed_login_attempts >= 5) {
            $this->locked_until = Carbon::now()->addMinutes(30);
        }
        $this->save();
    }

    public function resetFailedLoginAttempts()
    {
        $this->failed_login_attempts = 0;
        $this->locked_until = null;
        $this->save();
    }

    public function isLocked()
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function recordSuccessfulLogin(): bool
    {
        \Log::info('User::recordSuccessfulLogin - Rejestrowanie udanego logowania', [
            'user_id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    public function isActive()
    {
        return $this->is_active;
    }

    public function getPreference($key, $default = null)
    {
        $preferences = $this->preferences ?? [];
        return $preferences[$key] ?? $default;
    }

    public function setPreference($key, $value)
    {
        $preferences = $this->preferences ?? [];
        $preferences[$key] = $value;
        $this->preferences = $preferences;
        $this->save();
    }

    public function getLocalizedAttribute($value, $attribute)
    {
        if ($this->language && $this->language !== Config::get('app.locale')) {
            return $value;
        }
        return $value;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=6366f1&color=fff';
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Sprawdza czy użytkownik jest online (aktywny w ostatnich 5 minutach)
     *
     * @return bool
     */
    public function isOnline(): bool
    {
        \Log::info('User::isOnline - Sprawdzanie statusu online', [
            'user_id' => $this->id,
            'last_seen_at' => $this->last_seen_at?->toDateTimeString(),
            'current_time' => now()->toDateTimeString(),
            'timezone' => config('app.timezone'),
            'threshold' => now()->subMinutes(5)->toDateTimeString(),
            'is_online' => $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(5)),
            'cache_key' => "user.{$this->id}",
            'cache_exists' => Cache::has("user.{$this->id}"),
        ]);

        if (!$this->last_seen_at) {
            return false;
        }

        return $this->last_seen_at->gt(now()->subMinutes(5));
    }

    /**
     * Aktualizuje znacznik czasu ostatniej aktywności
     *
     * @return bool
     */
    public function updateLastSeen(): bool
    {
        \Log::info('User::updateLastSeen - Przed aktualizacją', [
            'user_id' => $this->id,
            'current_last_seen_at' => $this->last_seen_at?->toDateTimeString(),
            'new_last_seen_at' => now()->toDateTimeString(),
            'timezone' => config('app.timezone'),
            'cache_key' => "user.{$this->id}",
            'cache_exists' => Cache::has("user.{$this->id}"),
        ]);

        $result = $this->update(['last_seen_at' => now()]);

        \Log::info('User::updateLastSeen - Po aktualizacji', [
            'user_id' => $this->id,
            'update_success' => $result,
            'new_last_seen_at' => $this->fresh()->last_seen_at?->toDateTimeString(),
            'is_online' => $this->fresh()->isOnline(),
            'cache_key' => "user.{$this->id}",
            'cache_exists' => Cache::has("user.{$this->id}"),
        ]);

        return $result;
    }
}
