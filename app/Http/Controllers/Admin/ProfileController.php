<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $data = Auth::guard('admin')->user();
        if (!$data) {
            return redirect()->route('admin.login');
        }
        return view('admin.profile', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::guard('admin')->user();

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->username = $request->username;
        $user->email = $request->email;

        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
            $request->session()->flash('success', 'Profil dan password berhasil diperbarui!');
        } else {
            $request->session()->flash('success', 'Profil berhasil diperbarui!');
        }

        $user->save();

        Auth::guard('admin')->setUser($user);

        return redirect()->route('admin.profile.index');
    }
}