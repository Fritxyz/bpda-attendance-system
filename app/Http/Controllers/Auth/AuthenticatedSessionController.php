<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\AuditTrail;
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

        dd($user);

        AuditTrail::create([
            'user_id'        => $user->employee_id,
            'event'          => 'Login',
            'auditable_type' => get_class($user),
            'auditable_id'   => $user->id,
            'remarks'        => "User logged in successfully via Web Dashboard",
            'ip_address'     => $request->ip(),
        ]);

        if ($request->user()->role === 'Admin') {
            return redirect()->route('admin.dashboard');
        }

        // Default redirect para sa regular employees (Attendance Kiosk)
        return redirect()->route('employee.profile');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            // LOGGING LOGOUT EVENT
            AuditTrail::create([
                'user_id'        => $user->employee_id,
                'event'          => 'Logout',
                'auditable_type' => get_class($user),
                'auditable_id'   => $user->id,
                'remarks'        => "User logged out safely",
                'ip_address'     => $request->ip(),
            ]);
        }
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('auth.login');
    }
}
