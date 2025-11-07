<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Handle an incoming request.
     * Redirect ke halaman login jika user belum login
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login melalui session
        if (!session('user_id') && !session('id_aktor')) {
            // Simpan URL yang diminta untuk redirect setelah login
            if (!$request->expectsJson()) {
                $request->session()->put('url.intended', $request->fullUrl());
                return redirect()->route('login');
            }
            
            return redirect()->route('login');
        }

        return $next($request);
    }
}
