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
        Schema::create('skin_part_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('skin_part_id');
            $table->foreign('skin_part_id')->references('id')->on('skin_parts')->onDelete('cascade');
            $table->string('name')->nullable ();
            $table->string('image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skin_part_versions');
    }
};
