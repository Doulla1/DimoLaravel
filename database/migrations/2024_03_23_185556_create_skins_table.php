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
        Schema::create('skins', function (Blueprint $table) {
            $table->id();
            $table->UnsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->UnsignedBigInteger('hair_id');
            $table->foreign('hair_id')->references('id')->on('item');
            $table->UnsignedBigInteger('upper_id');
            $table->foreign('upper_id')->references('id')->on('item');
            $table->UnsignedBigInteger('lower_id');
            $table->foreign('lower_id')->references('id')->on('item');
            $table->string('skin-color');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skins');
    }
};
