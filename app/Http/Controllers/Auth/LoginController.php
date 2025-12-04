<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Menampilkan form login
     * Middleware 'guest' akan handle redirect jika sudah login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login dengan validasi dan keamanan yang lebih baik
     */
    public function login(Request $request)
    {
        // Validasi input dengan pesan error yang lebih jelas
        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        // Rate limiting untuk mencegah brute force attack
        $key = Str::lower($request->input('username')) . '|' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            
            throw ValidationException::withMessages([
                'username' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        // Cek kredensial user
        $user = $this->attemptLogin($request);

        if ($user) {
            // Login berhasil - reset rate limiter
            RateLimiter::clear($key);

            // Regenerate session ID untuk keamanan
            $request->session()->regenerate();

            // Simpan data aktor di session
            session([
                'user_id' => $user->id_aktor ?? $user->id,
                'id_aktor' => $user->id_aktor ?? $user->id,
                'username' => $user->nama_aktor ?? $user->username ?? $user->name,
                'user_name' => $user->nama_aktor ?? $user->name ?? $user->username ?? 'User',
                'id_role' => $user->id_role ?? null,
                'role' => $user->role ?? null,
            ]);

            // Jika menggunakan Laravel Auth, login juga melalui Auth facade
            if (method_exists($user, 'getAuthIdentifier')) {
                Auth::login($user, $request->boolean('remember'));
            }

            // Redirect ke halaman yang diminta sebelumnya atau halaman default
            $intendedUrl = $request->session()->pull('url.intended', route('dashboard.index'));
            return redirect($intendedUrl)->with('success', 'Selamat datang! Anda berhasil login.');
        }

        // Login gagal - increment rate limiter
        RateLimiter::hit($key, 60); // Lock selama 60 detik setelah 5 percobaan

        // Pesan error yang tidak terlalu spesifik untuk keamanan
        throw ValidationException::withMessages([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ]);
    }

    /**
     * Mencoba melakukan login menggunakan tabel aktor
     */
    protected function attemptLogin(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        // Query ke tabel aktor berdasarkan nama_aktor
        $aktor = DB::table('aktor')
            ->where('nama_aktor', $username)
            ->first();

        if (!$aktor) {
            return null;
        }

        // Cek password hanya menggunakan Hash::check.
        // Fallback perbandingan plaintext telah dihapus untuk keamanan; pastikan semua password sudah di-hash.
        $passwordMatch = Hash::check($password, $aktor->password);

        if ($passwordMatch) {
            // Ambil informasi role dari tabel pengguna jika ada
            $pengguna = DB::table('pengguna')
                ->where('id_aktor', $aktor->id_aktor)
                  ->first();

            $role = null;
            if ($pengguna) {
                $role = DB::table('role')
                    ->where('id_role', $pengguna->id_role)
                    ->first();
            }

            // Return object dengan struktur yang sesuai
            return (object) [
                'id' => $aktor->id_aktor,
                'id_aktor' => $aktor->id_aktor,
                'username' => $aktor->nama_aktor,
                'name' => $aktor->nama_aktor,
                'nama_aktor' => $aktor->nama_aktor,
                'id_role' => $pengguna->id_role ?? null,
                'role' => $role->nama_role ?? null,
            ];
        }

        return null;
    }

    /**
     * Fungsi untuk logout dengan pembersihan session yang lebih baik
     */
    public function logout(Request $request)
    {
        // Logout dari Laravel Auth jika digunakan
        if (Auth::check()) {
            Auth::logout();
        }

        // Hapus semua data session terkait user
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Clear session data
        session()->forget(['user_id', 'id_aktor', 'username', 'user_name', 'id_role', 'role']);

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
