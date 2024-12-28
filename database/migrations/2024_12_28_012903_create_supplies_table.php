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
        Schema::create('supplies', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->date('date'); // Date of supply
            $table->string('invoice_number'); // Invoice number
            $table->string('article'); // Foreign key linking to products by article
            $table->integer('quantity'); // Quantity of product
            $table->decimal('price', 10, 2); // Price of the supply
            $table->timestamps(); // Created at and updated at timestamps

            // Foreign key constraint
            $table->foreign('article')->references('article')->on('products')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplies');
    }
};
