<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated AND if the is_admin flag is true
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request); // User is admin, allow request
        }

        // User is not logged in or not an admin
        abort(403, 'Unauthorized action.'); // Forbidden
    }
}