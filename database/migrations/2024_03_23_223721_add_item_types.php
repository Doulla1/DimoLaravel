<?php

use App\Models\ItemType;
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
        ItemType::class::create(['name' => 'hair']);
        ItemType::class::create(['name' => 'upper_body']);
        ItemType::class::create(['name' => 'lower_body']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        ItemType::where('name', 'hair')->delete();
        ItemType::where('name', 'upper_body')->delete();
        ItemType::where('name', 'lower_body')->delete();
    }
};
