<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Satu rencana per client
        Schema::create('planners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->unique()->constrained('clients')->cascadeOnDelete();
            $table->string('partner_1')->nullable();
            $table->string('partner_2')->nullable();
            $table->date('wedding_date')->nullable();
            $table->string('venue')->nullable();
            $table->decimal('budget', 15, 2)->default(0);
            $table->unsignedInteger('guest_target')->default(0);
            $table->timestamps();
        });

        Schema::create('planner_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planner_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('phase', 40);          // kunci tahap, lihat Planner::PHASES
            $table->date('due_date')->nullable();
            $table->timestamp('done_at')->nullable();
            $table->timestamps();
            $table->index(['planner_id', 'phase']);
        });

        Schema::create('planner_budget_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planner_id')->constrained()->cascadeOnDelete();
            $table->string('category', 40);
            $table->string('name');
            $table->decimal('estimate', 15, 2)->default(0);
            $table->decimal('paid', 15, 2)->default(0);
            $table->string('note')->nullable();
            $table->timestamps();
        });

        Schema::create('planner_guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planner_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('side', 10);           // pria | wanita | bersama
            $table->string('phone', 30)->nullable();
            $table->unsignedTinyInteger('pax')->default(1);
            $table->string('rsvp', 10)->default('pending'); // pending | hadir | tidak
            $table->timestamps();
            $table->index(['planner_id', 'rsvp']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('planner_guests');
        Schema::dropIfExists('planner_budget_items');
        Schema::dropIfExists('planner_tasks');
        Schema::dropIfExists('planners');
    }
};
