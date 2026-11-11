<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HistoryController extends Controller
{
    public function index()
    {
        $orders = Order::where('client_id', Auth::guard('client')->id())
            ->latest()
            ->get();

        return view('client.history', compact('orders'));
    }

    public function bukti($id)
    {
        // Client hanya boleh lihat bukti miliknya sendiri
        $order = Order::where('client_id', Auth::guard('client')->id())->findOrFail($id);

        return Storage::response('bukti/' . $order->bukti_pembayaran);
    }
}
