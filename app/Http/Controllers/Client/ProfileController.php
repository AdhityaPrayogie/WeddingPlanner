<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::guard('client')->user();
        if (!$user) {
            return redirect()->route('client.login');
        }
        return view('client.profile', compact('user'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\Client $user */
        $user = Auth::guard('client')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clients,email,' . $user->id,
            'phone' => 'required|string|max:20',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // Update session client_name jika berubah
        Auth::guard('client')->setUser($user);

        // ✅ Gunakan nama route yang benar
        return redirect()->route('client.profile.index')->with('success', 'Profil berhasil diperbarui!');
    }
}
