<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  The required role (e.g., 'student' or 'staff').
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Check if the authenticated user's role matches the required role
        if (Auth::user()->role === $role) {
            return $next($request);
        }

        // Handle unauthorized access based on the current user's actual role
        if (Auth::user()->role === User::ROLE_STAFF) {
            return redirect()->route('staff.dashboard')->with('error', 'Unauthorized access.');
        }

        // Redirect all other unauthorized attempts to the public root
        return redirect('/')->with('error', 'You do not have permission to access this page.');
    }
}