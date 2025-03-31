<?php

namespace App\Services;

use App\Models\User;
use App\Models\Module;
use App\Models\UserModulePermission;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ModulePermissionService
{
    /**
     * Sprawdza, czy użytkownik ma dostęp do określonego modułu
     * 
     * @param User|int $user Użytkownik lub ID użytkownika
     * @param string|Module $module Moduł lub kod modułu
     * @return bool
     */
    public function userCanAccessModule($user, $module): bool
    {
        $user = $this->resolveUser($user);
        $moduleCode = $this->resolveModuleCode($module);
        
        if (!$user || !$moduleCode) {
            return false;
        }
        
        return $user->canAccessModule($moduleCode);
    }
    
    /**
     * Przyznaje dostęp do modułu dla użytkownika
     * 
     * @param User|int $user Użytkownik lub ID użytkownika
     * @param string|Module $module Moduł lub kod modułu
     * @param array $options Opcje dodatkowe: restrictions, valid_until, granted_by
     * @return bool
     */
    public function grantModuleAccess($user, $module, array $options = []): bool
    {
        $user = $this->resolveUser($user);
        $moduleCode = $this->resolveModuleCode($module);
        
        if (!$user || !$moduleCode) {
            return false;
        }
        
        try {
            return $user->grantModuleAccess($moduleCode, $options);
        } catch (\Exception $e) {
            Log::error('Błąd podczas przyznawania dostępu do modułu', [
                'user_id' => $user->id,
                'module' => $moduleCode,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Blokuje dostęp do modułu dla użytkownika
     * 
     * @param User|int $user Użytkownik lub ID użytkownika
     * @param string|Module $module Moduł lub kod modułu
     * @param string|null $grantedBy
     * @return bool
     */
    public function denyModuleAccess($user, $module, string $grantedBy = null): bool
    {
        $user = $this->resolveUser($user);
        $moduleCode = $this->resolveModuleCode($module);
        
        if (!$user || !$moduleCode) {
            return false;
        }
        
        try {
            return $user->denyModuleAccess($moduleCode, $grantedBy);
        } catch (\Exception $e) {
            Log::error('Błąd podczas blokowania dostępu do modułu', [
                'user_id' => $user->id,
                'module' => $moduleCode,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Pobiera aktywne ograniczenia dla modułu dla użytkownika
     * 
     * @param User|int $user Użytkownik lub ID użytkownika
     * @param string|Module $module Moduł lub kod modułu
     * @return array|null
     */
    public function getModuleRestrictions($user, $module)
    {
        $user = $this->resolveUser($user);
        $moduleCode = $this->resolveModuleCode($module);
        
        if (!$user || !$moduleCode) {
            return null;
        }
        
        return $user->getModuleRestrictions($moduleCode);
    }
    
    /**
     * Pobiera wszystkie moduły z informacją o dostępie dla użytkownika
     * 
     * @param User|int $user Użytkownik lub ID użytkownika
     * @return Collection
     */
    public function getUserModulesWithAccess($user): Collection
    {
        $user = $this->resolveUser($user);
        
        if (!$user) {
            return collect([]);
        }
        
        $allModules = Module::where('is_active', true)->get();
        
        return $allModules->map(function($module) use ($user) {
            $module->has_access = $user->canAccessModule($module->code);
            $module->restrictions = $user->getModuleRestrictions($module->code);
            
            // Sprawdź, czy dostęp jest bezpośredni czy z subskrypcji
            $directPermission = UserModulePermission::where([
                'user_id' => $user->id,
                'module_id' => $module->id,
                'access_granted' => true
            ])->first();
            
            $module->access_type = $directPermission ? 'direct' : 'subscription';
            
            return $module;
        });
    }
    
    /**
     * Przypisuje moduły do planu subskrypcji
     * 
     * @param SubscriptionPlan|int $plan Plan lub ID planu
     * @param array $moduleCodes Tablica kodów modułów
     * @param array $moduleLimitations Tablica limitów dla modułów, klucze to kody modułów
     * @return bool
     */
    public function assignModulesToPlan($plan, array $moduleCodes, array $moduleLimitations = []): bool
    {
        if ($plan instanceof SubscriptionPlan) {
            // Już mamy obiekt planu
        } elseif (is_numeric($plan)) {
            $plan = SubscriptionPlan::find($plan);
        } else {
            return false;
        }
        
        if (!$plan) {
            return false;
        }
        
        try {
            // Pobierz wszystkie moduły z kodami z tablicy $moduleCodes
            $modules = Module::whereIn('code', $moduleCodes)->get();
            
            // Przygotuj dane synchronizacji
            $syncData = [];
            foreach ($modules as $module) {
                $limitations = $moduleLimitations[$module->code] ?? null;
                $syncData[$module->id] = ['limitations' => $limitations ? json_encode($limitations) : null];
            }
            
            // Synchronizuj moduły z planem
            $plan->modules()->sync($syncData);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Błąd podczas przypisywania modułów do planu subskrypcji', [
                'plan_id' => $plan->id,
                'modules' => $moduleCodes,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Pomocnicza metoda do pobierania obiektu użytkownika
     * 
     * @param User|int $user
     * @return User|null
     */
    private function resolveUser($user)
    {
        if ($user instanceof User) {
            return $user;
        } elseif (is_numeric($user)) {
            return User::find($user);
        }
        
        return null;
    }
    
    /**
     * Pomocnicza metoda do pobierania kodu modułu
     * 
     * @param Module|string $module
     * @return string|null
     */
    private function resolveModuleCode($module)
    {
        if ($module instanceof Module) {
            return $module->code;
        } elseif (is_string($module)) {
            return $module;
        }
        
        return null;
    }
} 