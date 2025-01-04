<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventory_balances', function (Blueprint $table) {
            Schema::table('inventory_balances', function (Blueprint $table) {
                $table->boolean('is_discrepancy')->default(false)->after('StockCount');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_balances', function (Blueprint $table) {
            Schema::table('inventory_balances', function (Blueprint $table) {
                $table->dropColumn('is_discrepancy');
            });
        });
    }
};
