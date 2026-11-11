<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Hanya kategori yang punya paket, hanya kolom yang ditampilkan
        $kategoriList = Category::select('id', 'nama')
            ->whereHas('packages')
            ->with(['packages' => fn ($q) => $q->select('id', 'category_id', 'nama', 'harga', 'deskripsi', 'gambar')->orderBy('harga')])
            ->orderBy('nama')
            ->get();

        return view('home', [
            'kategoriList' => $kategoriList,
            'showWelcome'  => session('welcome', false), // flash: hilang otomatis setelah 1 request
            'client_name'  => Auth::guard('client')->user()->name ?? 'Tamu',
        ]);
    }
}
