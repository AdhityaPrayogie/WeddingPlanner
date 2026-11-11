<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/*
 * File asli migration ini kosong (0 byte) sehingga `migrate` di database baru gagal.
 * Isinya direkonstruksi dari skema produksi: packages.harga = DECIMAL(20,2) NULL.
 * SQL mentah dipakai karena ->change() butuh doctrine/dbal yang tidak terpasang.
 */
return new class extends Migration
{
    public function up()
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE packages MODIFY harga DECIMAL(20,2) NULL');
        }
    }

    public function down()
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE packages MODIFY harga DECIMAL(15,2) NOT NULL');
        }
    }
};
