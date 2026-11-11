<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('planner_tasks', function (Blueprint $table) {
            $table->text('note')->nullable()->after('title');
        });
    }

    public function down()
    {
        Schema::table('planner_tasks', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
};
