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
        Schema::create('selected_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('selected_product_id');
            $table->string('name');
            $table->integer('price');

            $table->foreign('selected_product_id')->references('id')->on('selected_products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selected_options');
    }
};
