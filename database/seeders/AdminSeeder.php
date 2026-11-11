<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Buat akun admin awal. Password TIDAK ditulis di kode (repo bisa publik):
     * diambil dari ADMIN_PASSWORD di .env, atau dibuat acak lalu ditampilkan sekali di terminal.
     */
    public function run()
    {
        $username = env('ADMIN_USERNAME', 'admin');

        if (User::where('username', $username)->exists()) {
            $this->command?->info("Admin '$username' sudah ada, dilewati (password tidak diubah).");
            return;
        }

        $password = env('ADMIN_PASSWORD') ?: Str::random(20);

        User::create([
            'username' => $username,
            'email'    => env('ADMIN_EMAIL', 'admin@example.com'),
            'password' => Hash::make($password),
        ]);

        $this->command?->warn("Admin '$username' dibuat." . (env('ADMIN_PASSWORD') ? '' : " Password: $password (simpan sekarang, tidak ditampilkan lagi)"));
    }
}
