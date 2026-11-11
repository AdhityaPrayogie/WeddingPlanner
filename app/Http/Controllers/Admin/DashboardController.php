<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Order;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'created_at');

        $orderBy = match ($sort) {
            'client'   => Client::select('name')->whereColumn('clients.id', 'orders.client_id'),
            'paket'    => Package::select('nama')->whereColumn('packages.id', 'orders.package_id'),
            'kategori' => 'paket', // label diawali nama kategori
            default    => 'created_at',
        };

        $orders = Order::with('client:id,name')
            ->orderBy($orderBy)
            ->paginate(5)
            ->withQueryString();

        $counts = Order::selectRaw('status, COUNT(*) AS total')->groupBy('status')->pluck('total', 'status');

        return view('admin.dashboard', [
            'orders'    => $orders,
            'totalData' => $orders->total(),
            'confirmed' => $counts['confirmed'] ?? 0,
            'pending'   => $counts['pending'] ?? 0,
            'cancelled' => $counts['cancelled'] ?? 0,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:confirmed,pending,cancelled',
        ]);

        Order::findOrFail($id)->update(['status' => $request->action]);

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        if ($order->bukti_pembayaran) {
            Storage::delete('bukti/' . $order->bukti_pembayaran);
        }
        $order->delete();

        return redirect()->back()->with('success', 'Pesanan berhasil dihapus.');
    }

    public function bukti($id)
    {
        $order = Order::findOrFail($id);

        return Storage::response('bukti/' . $order->bukti_pembayaran);
    }
}
