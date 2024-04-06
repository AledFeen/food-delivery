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
        Schema::create('product_category_has_products', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('category_id');

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('parent_id')->references('id')->on('products');
            $table->foreign('category_id')->references('id')->on('product_categories');

            $table->primary(['product_id', 'parent_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_category_has_products');
    }
};
