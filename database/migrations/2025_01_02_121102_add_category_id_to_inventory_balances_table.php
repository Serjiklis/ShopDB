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
                $table->unsignedBigInteger('category_id')->nullable()->after('StockCount'); // Add the category_id column
                $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete(); // Add foreign key
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_balances', function (Blueprint $table) {
            $table->dropForeign(['category_id']); // Drop the foreign key
            $table->dropColumn('category_id');   // Remove the column
        });
    }
};
