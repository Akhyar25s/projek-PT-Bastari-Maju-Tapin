<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Check apakah user memiliki role yang diizinkan
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah user sudah login
        if (!session('id_aktor')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Ambil role dari session
        $userRole = session('role');
        $userRoleId = session('id_role');

        // Jika role tidak ada di session, coba ambil dari database
        if (!$userRole || !$userRoleId) {
            $userRole = $this->getUserRoleFromDatabase(session('id_aktor'));
            if ($userRole) {
                session(['role' => $userRole->nama_role, 'id_role' => $userRole->id_role]);
                $userRole = $userRole->nama_role;
                $userRoleId = $userRole->id_role;
            }
        }

        // Check apakah user memiliki salah satu role yang diizinkan
        $allowedRoles = is_array($roles) ? $roles : [$roles];
        
        // Check by role name atau role id
        if (!in_array($userRole, $allowedRoles) && !in_array($userRoleId, $allowedRoles)) {
            // Jika tidak memiliki akses, redirect dengan error
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        return $next($request);
    }

    /**
     * Ambil role dari database
     */
    private function getUserRoleFromDatabase($idAktor)
    {
        return \Illuminate\Support\Facades\DB::table('pengguna')
            ->join('role', 'pengguna.id_role', '=', 'role.id_role')
            ->select('role.nama_role', 'role.id_role')
            ->where('pengguna.id_aktor', $idAktor)
            ->first();
    }
}
