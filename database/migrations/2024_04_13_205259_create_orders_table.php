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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('courier_id')->nullable();
            $table->unsignedBigInteger('cities_id');
            $table->unsignedBigInteger('stores_id');
            $table->string('phoneNumber', 15);
            $table->string('address');
            $table->decimal('price');
            $table->timestamps();

            $table->foreign('users_id')->references('id')->on('users');
            $table->foreign('courier_id')->references('id')->on('couriers');
            $table->foreign('cities_id')->references('id')->on('cities');
            $table->foreign('stores_id')->references('id')->on('stores');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
