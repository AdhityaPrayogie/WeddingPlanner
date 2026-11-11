<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // `paket` tetap disimpan sebagai label snapshot (aman bila paket dihapus/diganti nama)
            $table->foreignId('package_id')->nullable()->after('client_id')->constrained('packages')->nullOnDelete();
            $table->decimal('harga', 15, 2)->nullable()->after('paket');
            $table->index('status');
            $table->index('created_at');
        });

        // Backfill dari label lama "Kategori: Nama"
        DB::table('orders')->whereNull('package_id')->orderBy('id')->each(function ($order) {
            [$kat, $nama] = array_map('trim', explode(':', $order->paket, 2)) + [1 => ''];
            $paket = DB::table('packages')
                ->join('categories', 'categories.id', '=', 'packages.category_id')
                ->whereRaw('LOWER(categories.nama) = ?', [strtolower($kat)])
                ->whereRaw('LOWER(packages.nama) = ?', [strtolower($nama)])
                ->select('packages.id', 'packages.harga')
                ->first();

            if ($paket) {
                DB::table('orders')->where('id', $order->id)
                    ->update(['package_id' => $paket->id, 'harga' => $paket->harga]);
            }
        });

        // Pindahkan bukti pembayaran ke storage privat
        foreach (Storage::disk('public')->files('bukti') as $file) {
            Storage::disk('local')->put($file, Storage::disk('public')->get($file));
            Storage::disk('public')->delete($file);
        }
    }

    public function down()
    {
        foreach (Storage::disk('local')->files('bukti') as $file) {
            Storage::disk('public')->put($file, Storage::disk('local')->get($file));
            Storage::disk('local')->delete($file);
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('package_id');
            $table->dropColumn('harga');
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });
    }
};
