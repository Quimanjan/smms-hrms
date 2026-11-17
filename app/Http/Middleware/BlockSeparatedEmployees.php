<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/** Signs out an employee whose offboarding was final approved. */
class BlockSeparatedEmployees
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->employee?->separated_on) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')->withErrors(['email' => 'This account is closed because the employee has left the school.']);
        }

        return $next($request);
    }
}
