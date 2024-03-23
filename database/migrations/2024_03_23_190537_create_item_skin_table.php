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
        Schema::create('item_skin', function (Blueprint $table) {
            $table->id();
            $table->UnsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('item');
            $table->string ('color');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_skin');
    }
};
