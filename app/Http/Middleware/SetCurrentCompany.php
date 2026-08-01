<?php

namespace App\Http\Middleware;

use App\Services\CompanyScope;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class SetCurrentCompany
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            CompanyScope::validateSession();

            $currentCompany = CompanyScope::currentCompany();
            $currentCompanyId = CompanyScope::id();
            $userCompanies = CompanyScope::companies();
            $isSuperAdmin = CompanyScope::isSuperAdmin();

            $showCompanyFilter = CompanyScope::isAllCompanies();

            View::share('currentCompany', $currentCompany);
            View::share('currentCompanyId', $currentCompanyId);
            View::share('userCompanies', $userCompanies);
            View::share('isSuperAdmin', $isSuperAdmin);
            View::share('showCompanyFilter', $showCompanyFilter);
        }

        return $next($request);
    }
}
