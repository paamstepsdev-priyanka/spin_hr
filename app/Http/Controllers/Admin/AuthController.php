<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'employee' || ($user->employee && !$user->isSuperAdmin())) {
                return redirect()->route('employee.dashboard');
            }
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (strtolower($user->status) !== 'active') {
                Auth::logout();
                return back()->withErrors(['email' => 'Your user account is inactive.'])->onlyInput('email');
            }

            if ($user->role === 'employee' || ($user->employee && !$user->isSuperAdmin())) {
                $employee = $user->employee;
                if (!$employee || strtolower($employee->status) !== 'active') {
                    Auth::logout();
                    return back()->withErrors(['email' => 'Your employee profile is inactive.'])->onlyInput('email');
                }

                if (!$employee->company || strtolower($employee->company->status) !== 'active') {
                    Auth::logout();
                    return back()->withErrors(['email' => 'Your company account is inactive.'])->onlyInput('email');
                }

                return redirect()->intended(route('employee.dashboard'))->with('success', 'Welcome back, ' . $user->name . '!');
            }

            return redirect()->intended(route('dashboard'))->with('success', 'Welcome back, ' . $user->name . '!');
        }

        return back()->withErrors([
            'email' => 'Invalid email address or password.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
