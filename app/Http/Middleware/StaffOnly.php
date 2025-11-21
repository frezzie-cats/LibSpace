<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;   // <-- Add this

class StaffOnly
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is NOT logged in
        if (!Auth::check()) {           // <-- Use Auth::check()
            return redirect('/login');
        }

        // Check if user is NOT staff
        if (Auth::user()->role !== 'staff') {
            abort(403, 'Access denied. Staff only.');
        }

        return $next($request);
    }
}
