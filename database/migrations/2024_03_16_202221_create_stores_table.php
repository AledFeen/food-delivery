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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->unsignedBigInteger('users_id');
            $table->string('image');
            $table->string('type_store');
            $table->string('category')->nullable();

            $table->unique(['id', 'users_id']);
            $table->index('users_id');
            $table->index('type_store');
            $table->index('category');

            $table->foreign('users_id')->references('id')->on('users')->onDelete('NO ACTION')->onUpdate('NO ACTION');
            $table->foreign('type_store')->references('name')->on('store_types')->onDelete('NO ACTION')->onUpdate('NO ACTION');
            $table->foreign('category')->references('name')->on('store_categories')->onDelete('NO ACTION')->onUpdate('NO ACTION');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
