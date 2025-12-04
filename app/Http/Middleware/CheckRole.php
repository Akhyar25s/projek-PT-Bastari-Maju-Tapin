<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Check apakah user memiliki role yang diizinkan
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Role yang diizinkan (bisa lebih dari satu)
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
            $roleData = $this->getUserRoleFromDatabase(session('id_aktor'));
            if ($roleData) {
                session([
                    'role' => $roleData->nama_role,
                    'id_role' => $roleData->id_role
                ]);
                $userRole = $roleData->nama_role;
                $userRoleId = $roleData->id_role;
            }
        }

        // Jika masih tidak ada role, redirect ke login
        if (!$userRole) {
            return redirect()->route('login')->with('error', 'Role tidak ditemukan. Silakan login ulang.');
        }

        // Normalize role names untuk comparison (case insensitive)
        $userRoleNormalized = strtolower(trim($userRole));
        $allowedRoles = is_array($roles) ? $roles : [$roles];
        $allowedRolesNormalized = array_map(function($role) {
            return strtolower(trim($role));
        }, $allowedRoles);
        
        // Check apakah user memiliki salah satu role yang diizinkan
        $hasAccess = in_array($userRoleNormalized, $allowedRolesNormalized) || 
                     in_array($userRoleId, $allowedRoles);

        if (!$hasAccess) {
            // Jika tidak memiliki akses, redirect ke dashboard sesuai role user
            $redirectRoute = $this->getDashboardRouteByRole($userRole);
            
            if ($redirectRoute) {
                return redirect()->route($redirectRoute)
                    ->with('error', 'Anda tidak memiliki akses untuk mengakses halaman ini');
            }
            
            // Fallback: redirect back atau ke dashboard index
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        return $next($request);
    }

    /**
     * Ambil role dari database berdasarkan id_aktor
     * 
     * @param string $idAktor
     * @return object|null
     */
    private function getUserRoleFromDatabase($idAktor)
    {
        return DB::table('pengguna')
            ->join('role', 'pengguna.id_role', '=', 'role.id_role')
            ->select('role.nama_role', 'role.id_role')
            ->where('pengguna.id_aktor', $idAktor)
            ->first();
    }

    /**
     * Mendapatkan route dashboard berdasarkan role user
     * Helper method untuk redirect yang konsisten
     * 
     * @param string $role
     * @return string|null
     */
    private function getDashboardRouteByRole($role)
    {
        if (!$role) {
            return 'dashboard.index';
        }

        // Normalisasi role ke lowercase untuk perbandingan case-insensitive
        $r = strtolower(trim($role));

        if ($r === 'admin') {
            return 'dashboard.admin';
        } elseif ($r === 'penjaga gudang' || $r === 'penjaga_gudang' || $r === 'pejaga gudang' || $r === 'pejaga_gudang') {
            return 'dashboard.gudang';
        } elseif ($r === 'direktur') {
            return 'dashboard.direktur';
        } elseif ($r === 'keuangan') {
            return 'dashboard.keuangan';
        } elseif ($r === 'umum') {
            return 'dashboard.umum';
        } elseif ($r === 'perencanaan') {
            return 'dashboard.perencanaan';
        }

        return 'dashboard.index'; // Fallback
    }
}
