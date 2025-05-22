<?php

// app/Http/Controllers/Auth/AuthenticatedSessionController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest; // Kita akan buat request ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request) // Gunakan LoginRequest
    {
        $request->authenticate(); // Method dari LoginRequest

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard')); // Arahkan ke dashboard atau halaman yang dituju sebelumnya
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/'); // Arahkan ke halaman utama setelah logout
    }
}