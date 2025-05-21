<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');

            // Buat transaksi dengan tipe penjualan
            $table->integer('selling_total_price')->nullable();
            $table->integer('fee')->nullable();
            $table->integer('total')->nullable();
            $table->string('payment_method')->nullable();

            // Buat transaksi dengan tipe pembelian
            $table->uuid('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('restrict');
            $table->string('unit')->nullable();
            $table->integer('amount')->nullable();
            $table->integer('amount_per_unit')->nullable();
            $table->integer('price_per_unit')->nullable();
            $table->integer('total_price')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
