<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property numeric $amount
 * @property numeric $planned_amount
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetCategory whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetCategory wherePlannedAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetCategory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetCategory whereUpdatedAt($value)
 */
	class BudgetCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $company_name
 * @property string|null $tax_number
 * @property string|null $regon
 * @property string|null $street
 * @property string|null $city
 * @property string|null $postal_code
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $website
 * @property string|null $bank_name
 * @property string|null $bank_account
 * @property string|null $logo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyProfile whereBankAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyProfile whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyProfile whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyProfile whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyProfile whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyProfile whereLogoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyProfile wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyProfile wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyProfile whereRegon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyProfile whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyProfile whereTaxNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyProfile whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyProfile whereWebsite($value)
 */
	class CompanyProfile extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $company_name
 * @property string $nip
 * @property string $email
 * @property string $phone
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contractor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contractor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contractor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contractor whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contractor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contractor whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contractor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contractor whereNip($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contractor wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contractor whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contractor whereUpdatedAt($value)
 */
	class Contractor extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property numeric $amount
 * @property string $category
 * @property \Illuminate\Support\Carbon $date
 * @property \Illuminate\Support\Carbon $due_date
 * @property string $status
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cost query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cost whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cost whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cost whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cost whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cost whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cost whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cost whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cost whereUpdatedAt($value)
 */
	class Cost extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $company_name
 * @property string|null $tax_number
 * @property string|null $regon
 * @property string|null $krs
 * @property string $email
 * @property string|null $phone
 * @property string|null $website
 * @property string|null $street
 * @property string|null $street_number
 * @property string|null $apartment_number
 * @property string|null $postal_code
 * @property string|null $city
 * @property string $country
 * @property string|null $bank_name
 * @property string|null $bank_account_number
 * @property string|null $default_payment_method
 * @property int $default_payment_deadline_days
 * @property string|null $invoice_notes
 * @property string|null $logo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $full_address
 * @property-read mixed $full_name
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereApartmentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereBankAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereDefaultPaymentDeadlineDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereDefaultPaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereInvoiceNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereKrs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereLogoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereRegon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereStreetNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereTaxNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile withoutTrashed()
 */
	class Profile extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role withoutTrashed()
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property string|null $last_login_ip
 * @property bool $two_factor_enabled
 * @property string|null $two_factor_secret
 * @property int $failed_login_attempts
 * @property \Illuminate\Support\Carbon|null $locked_until
 * @property string $language
 * @property string $timezone
 * @property array<array-key, mixed>|null $preferences
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\CompanyProfile|null $companyProfile
 * @property-read mixed $localized
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Profile|null $profile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFailedLoginAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLockedUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePreferences($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

