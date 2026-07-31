<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EmployeeMiddleware
{
    /**
     * Handle an incoming request for the Employee Portal.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Super admin / admin override if needed or strict employee check
        $employee = $user->employee;

        if (!$employee) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Access Denied: No employee profile is linked to your user account.',
            ]);
        }

        if (strtolower($employee->status) !== 'active') {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Access Denied: Your employee profile is inactive.',
            ]);
        }

        // Verify company is active
        if (!$employee->company || strtolower($employee->company->status) !== 'active') {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Access Denied: Your company account is inactive.',
            ]);
        }

        return $next($request);
    }
}
