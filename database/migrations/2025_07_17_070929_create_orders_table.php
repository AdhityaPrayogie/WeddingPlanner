        <?php

        use Illuminate\Database\Migrations\Migration;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Support\Facades\Schema;

        return new class extends Migration
        {
            public function up()
            {
                Schema::create('orders', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
                    $table->date('tanggal');
                    $table->string('paket');
                    $table->text('catatan')->nullable();
                    $table->string('bukti_pembayaran')->nullable();
                    $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
                    $table->timestamps();
                });
            }

            public function down()
            {
                Schema::dropIfExists('orders');
            }
        };
