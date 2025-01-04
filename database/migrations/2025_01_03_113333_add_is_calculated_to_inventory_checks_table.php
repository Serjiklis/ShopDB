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
        Schema::table('inventory_checks', function (Blueprint $table) {
            $table->boolean('is_calculated')
                ->default(false)
                ->after('CountedStock');
            $table->dateTime('calculated_at')
                ->nullable()
                ->after('is_calculated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_checks', function (Blueprint $table) {

        });
    }
};
