<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // Role-based redirect kapag logged-in at tinry i-access ang login page
                if ($user && $user->getRawOriginal('role') === 'Admin') {
                    return redirect()->route('admin.dashboard');
                }

                return redirect()->route('employee.dashboard');
            }
        }

        return $next($request);
    }
}