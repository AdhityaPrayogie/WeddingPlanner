<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientLoginController extends Controller
{
    /**
     * Menampilkan form login client.
     */
    public function showLoginForm()
    {
        return view('client.auth.login');
    }

    /**
     * Proses login client.
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Coba login dengan guard client
        if (Auth::guard('client')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            // Redirect ke halaman utama dan kirim session 'welcome'
            return redirect()->route('home')->with('welcome', true);
        }

        // Jika gagal login
        return back()->withErrors([
            'username' => 'Username atau password salah!',
        ])->onlyInput('username');
    }

    /**
     * Logout client.
     */
    public function logout(Request $request)
    {
        Auth::guard('client')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.login');
    }
}
