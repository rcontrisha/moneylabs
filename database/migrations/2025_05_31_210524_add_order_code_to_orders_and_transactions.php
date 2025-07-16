<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_code')->nullable()->after('id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('order_code')->nullable()->after('order_id');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('order_code');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('order_code');
        });
    }
};
