<?php

// app/Http/Middleware/CheckRole.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  Parameter peran yang diharapkan
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check() || Auth::user()->role !== $role) {
            // abort(403, 'Unauthorized action.');
            // Atau redirect ke halaman lain dengan pesan error
            return redirect()->route('dashboard')->with('error', 'You do not have permission to access this page.');
        }
        return $next($request);
    }
}