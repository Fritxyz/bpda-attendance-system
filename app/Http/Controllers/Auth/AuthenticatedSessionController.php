<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function index(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        $request->session()->forget('url.intended');

        $user = $request->user();

        \Illuminate\Support\Facades\Log::debug('=== LOGIN DEBUG ===', [
            'auth_id'          => $user?->id,
            'auth_employee_id' => $user?->employee_id,
            'auth_role_raw'    => $user?->role,
            'auth_role_get'    => $user?->getAttribute('role'),
            'auth_role_array'  => $user?->toArray()['role'] ?? 'missing',
            'auth_exists'      => $user ? 'yes' : 'NO USER',
            'intended'         => $request->session()->get('url.intended'),
        ]);

        if ($request->user()->role === 'Admin') {
            return redirect()->route('admin.dashboard');
        }

        // Default redirect para sa regular employees (Attendance Kiosk)
        return redirect()->route('employee.dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('auth.login');
    }
}
