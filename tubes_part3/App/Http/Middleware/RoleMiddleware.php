<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; 

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Periksa apakah user sudah login
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please login to access this page.');
        }

        // Dapatkan data user yang sedang login
        $user = Auth::user();

        // 2. Periksa apakah peran user (role) ada di daftar peran yang diizinkan ($roles)
        if (!in_array($user->role, $roles)) {
            // Jika tidak, tendang ke halaman dashboard biasa
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        // Jika semua oke, izinkan user masuk ke halaman yang dituju
        return $next($request);
    }
}