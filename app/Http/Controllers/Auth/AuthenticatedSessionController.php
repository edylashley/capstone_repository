<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Check if account is active (Approved by admin)
        if (!$user->is_active) {
            // Log the blocked attempt before logout
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'login_blocked_inactive',
                'target_type' => 'user',
                'target_id' => $user->id,
                'ip' => $request->ip(),
                'meta' => [
                    'email' => $user->email,
                    'reason' => 'Account is inactive/pending approval'
                ],
            ]);

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account is currently pending administrative review. Access will be granted once your CSIT student status and I.D. have been verified.',
            ]);
        }

        $request->session()->regenerate();

        // Log login activity
        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'target_type' => 'user',
            'target_id' => $user->id,
            'ip' => $request->ip(),
            'meta' => ['email' => $user->email],
        ]);

        // Role-based redirect after login
        return match ($user->role) {
            'student' => redirect()->intended(route('student.home', absolute: false)),
            'adviser' => redirect()->intended(route('faculty.dashboard', absolute: false)),
            'admin' => redirect()->intended(route('admin.dashboard', absolute: false)),
            default => redirect()->intended(route('dashboard', absolute: false)),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Log logout activity
        if ($request->user()) {
            \App\Models\ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'logout',
                'target_type' => 'user',
                'target_id' => $request->user()->id,
                'ip' => $request->ip(),
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
