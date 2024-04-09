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
        Schema::create('cities_has_stores', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('city_id');
            $table->foreign('store_id')->references('id')->on('stores');
            $table->foreign('city_id')->references('id')->on('cities');
            $table->primary(['store_id', 'city_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities_has_stores');
    }
};
