<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  ...$roles
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user has any of the specified roles
        foreach ($roles as $role) {
            if ($role === 'hc' && $user->isAdmin()) {
                return $next($request);
            } elseif ($role === 'it' && $user->isIT()) {
                return $next($request);
            } elseif ($role === 'ga' && $user->isGA()) {
                return $next($request);
            } elseif ($role === 'user' && $user->isUser()) {
                return $next($request);
            }
        }

        // If no matching role, abort with 403 error
        return abort(403, 'Unauthorized action.');
    }
}
