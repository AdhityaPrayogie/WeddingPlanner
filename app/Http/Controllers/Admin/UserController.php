<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $admins = User::paginate(10, ['*'], 'admin_page')->appends(['tab' => 'admin']);
        $clients = Client::paginate(10, ['*'], 'client_page')->appends(['tab' => 'client']);
        $active_tab = session('active_tab', $request->query('tab', 'admin'));

        return view('admin.user', compact('admins', 'clients', 'active_tab'));
    }

    public function updateAdmin(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'required|email|max:255|unique:users,email,' . $id,
        ]);

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with(['success' => 'Data admin berhasil diperbarui!', 'active_tab' => 'admin']);
    }

    public function updateClient(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $data = $request->validate([
            'username' => 'required|string|max:255|unique:clients,username,' . $id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clients,email,' . $id,
            'phone' => 'required|string|max:20',
        ]);

        $client->update($data);

        return redirect()->route('admin.users.index')
            ->with(['success' => 'Data client berhasil diperbarui!', 'active_tab' => 'client']);
    }

    public function destroy($type, $id)
    {
        if ($type === 'admin') {
            if ((int) $id === Auth::guard('admin')->id()) {
                return redirect()->route('admin.users.index')
                    ->with(['error' => 'Tidak bisa menghapus akun sendiri.', 'active_tab' => 'admin']);
            }
            User::destroy($id);
        } elseif ($type === 'client') {
            Client::destroy($id);
        } else {
            abort(404);
        }

        return redirect()->route('admin.users.index')
            ->with(['success' => ucfirst($type) . ' berhasil dihapus!', 'active_tab' => $type]);
    }
}
