<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Package;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function showForm($id)
    {
        $paket = Package::with('category')->findOrFail($id);
        $sukses = session('sukses');

        return view('client.pembayaran', compact('paket', 'sukses'));
    }

    public function processOrder(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'catatan' => 'nullable|string|max:1000',
            'bukti'   => Media::imageRule(),
        ], Media::messages('bukti', 'Bukti pembayaran'));

        $paket = Package::with('category')->findOrFail($id);

        // Bukti pembayaran disimpan privat (storage/app/bukti), diakses lewat route ber-auth
        $namaBukti = Media::storeImage($request->file('bukti'), 'bukti', 'local', 1600);

        Order::create([
            'client_id'        => Auth::guard('client')->id(),
            'package_id'       => $paket->id,
            'tanggal'          => $request->tanggal,
            'paket'            => $paket->category->nama . ': ' . $paket->nama, // snapshot label
            'harga'            => $paket->harga,                                 // snapshot harga
            'catatan'          => $request->catatan,
            'bukti_pembayaran' => $namaBukti,
            'status'           => 'pending',
        ]);

        return redirect()->route('client.payments.show', ['package' => $id])
            ->with('sukses', 'Pesanan berhasil dikirim!');
    }
}
