<?php

use App\Models\Lobby;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Lobby::create(['name' => 'Eagle']);
        Lobby::create(['name' => 'Turtle']);
        Lobby::create(['name' => 'Mosquito']);
        Lobby::create(['name' => 'Sloth']);
        Lobby::create(['name' => 'Spider']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Lobby::where('name', 'Eagle')->delete();
        Lobby::where('name', 'Turtle')->delete();
        Lobby::where('name', 'Mosquito')->delete();
        Lobby::where('name', 'Sloth')->delete();
        Lobby::where('name', 'Spider')->delete();
    }
};
