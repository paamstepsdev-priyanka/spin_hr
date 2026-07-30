<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Employee;
use App\Models\UserCompany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CompanyScope
{
    /**
     * Cache for accessible companies in current request lifecycle.
     */
    protected static ?Collection $accessibleCompanies = null;

    /**
     * Cache for current company model in current request lifecycle.
     */
    protected static ?Company $currentCompanyModel = null;

    /**
     * Check if logged in user is Super Admin.
     */
    public static function isSuperAdmin(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->isSuperAdmin();
    }

    /**
     * Get accessible active companies for current authenticated user.
     */
    public static function companies(): Collection
    {
        if (!Auth::check()) {
            return collect();
        }

        if (static::$accessibleCompanies !== null) {
            return static::$accessibleCompanies;
        }

        $user = Auth::user();

        if (static::isSuperAdmin()) {
            static::$accessibleCompanies = Company::where('status', 'active')
                ->orderBy('name', 'asc')
                ->get();
        } else {
            // Fetch mapped companies from user_companies table
            $companyIds = UserCompany::where('user_id', $user->id)
                ->where('status', 'active')
                ->pluck('company_id')
                ->toArray();

            // Fallback: Check employee table if user_companies mapping is empty
            if (empty($companyIds)) {
                $empCompanyIds = Employee::where('user_id', $user->id)
                    ->whereNotNull('company_id')
                    ->pluck('company_id')
                    ->toArray();

                $companyIds = array_values(array_unique($empCompanyIds));
            }

            if (!empty($companyIds)) {
                static::$accessibleCompanies = Company::whereIn('id', $companyIds)
                    ->where('status', 'active')
                    ->orderBy('name', 'asc')
                    ->get();
            } else {
                static::$accessibleCompanies = collect();
            }
        }

        return static::$accessibleCompanies;
    }

    /**
     * Validate and resolve the current selected company ID from session.
     */
    public static function id(): ?int
    {
        if (!Auth::check()) {
            return null;
        }

        static::validateSession();

        $sessionVal = session('current_company_id');

        if ($sessionVal === null || $sessionVal === 'all' || $sessionVal === 0 || $sessionVal === '0') {
            return null;
        }

        return (int) $sessionVal;
    }

    /**
     * Check if "All Companies" is selected.
     */
    public static function isAllCompanies(): bool
    {
        return static::id() === null;
    }

    /**
     * Get current active Company model instance.
     */
    public static function currentCompany(): ?Company
    {
        $id = static::id();
        if ($id === null) {
            return null;
        }

        if (static::$currentCompanyModel !== null && static::$currentCompanyModel->id === $id) {
            return static::$currentCompanyModel;
        }

        static::$currentCompanyModel = Company::find($id);

        return static::$currentCompanyModel;
    }

    /**
     * Validate session company selection against database and user authorization.
     */
    public static function validateSession(): void
    {
        if (!Auth::check()) {
            return;
        }

        $isSuper = static::isSuperAdmin();
        $accessible = static::companies();
        $sessionVal = session('current_company_id');

        // If session not set yet
        if (!session()->has('current_company_id')) {
            if ($isSuper) {
                session(['current_company_id' => 'all']);
            } else {
                $defaultComp = $accessible->first();
                session(['current_company_id' => $defaultComp ? $defaultComp->id : null]);
            }
            return;
        }

        // If non-super admin has 'all' or null, force reset
        if (!$isSuper && ($sessionVal === null || $sessionVal === 'all' || $sessionVal === '0' || $sessionVal === 0)) {
            $defaultComp = $accessible->first();
            session(['current_company_id' => $defaultComp ? $defaultComp->id : null]);
            return;
        }

        // If specific company selected, verify it exists and is active and user has permission
        if ($sessionVal !== 'all' && $sessionVal !== null) {
            $comp = Company::where('id', $sessionVal)->where('status', 'active')->first();
            
            if (!$comp) {
                // Selected company was deleted or deactivated
                static::resetSession();
                return;
            }

            if (!$isSuper) {
                $hasAccess = $accessible->contains('id', (int) $sessionVal);
                if (!$hasAccess) {
                    // Unauthorized company in session
                    static::resetSession();
                    return;
                }
            }
        }
    }

    /**
     * Reset session to user's default company or 'all' for Super Admin.
     */
    public static function resetSession(): void
    {
        if (static::isSuperAdmin()) {
            session(['current_company_id' => 'all']);
        } else {
            $accessible = static::companies();
            $defaultComp = $accessible->first();
            session(['current_company_id' => $defaultComp ? $defaultComp->id : null]);
        }
        static::$currentCompanyModel = null;
    }

    /**
     * Set selected company in session and flush caches.
     */
    public static function setCompany($companyId): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        $isSuper = static::isSuperAdmin();

        if ($companyId === 'all' || $companyId === null || $companyId === '' || $companyId === '0' || $companyId === 0) {
            if (!$isSuper) {
                return false; // Non-super admin cannot select All Companies
            }
            session(['current_company_id' => 'all']);
            static::clearCache();
            return true;
        }

        $companyId = (int) $companyId;
        $company = Company::where('id', $companyId)->where('status', 'active')->first();

        if (!$company) {
            return false;
        }

        if (!$isSuper) {
            $accessible = static::companies();
            if (!$accessible->contains('id', $companyId)) {
                return false;
            }
        }

        session(['current_company_id' => $companyId]);
        static::$currentCompanyModel = $company;
        static::clearCache($companyId);

        return true;
    }

    /**
     * Clear dashboard & filter caches.
     */
    public static function clearCache($companyId = null): void
    {
        Cache::forget('dashboard_stats_all');
        if ($companyId) {
            Cache::forget("dashboard_stats_{$companyId}");
        } else {
            foreach (Company::pluck('id') as $cId) {
                Cache::forget("dashboard_stats_{$cId}");
            }
        }
    }
}
